<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Properties;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\DetailKamarTransaction;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Properties::with([
            'kamar' => fn($q) => $q
                ->select('id', 'properties_id', 'nama_kamar', 'kapasitas', 'lantai')
                ->orderBy('nama_kamar'),
        ])
            ->whereIn('type', ['asrama', 'paviliun'])
            ->orderBy('name', 'asc')
            ->get();


        $properties->transform(function ($p) {
            $p->floors = $p->kamar
                ->filter(fn($k) => !is_null($k->lantai) && (int)$k->lantai !== 0)
                ->groupBy(fn($k) => (int) $k->lantai)
                ->sortKeys();

            return $p;
        });

        return view('admin.bedrooms.index', compact('properties'));
    }


    public function updatePenghunis(Request $request)
    {
        $request->validate([
            'detail_id'    => 'required|integer|exists:detail_kamar_transaction,id',
            'names'        => 'array',
            'names.*'      => 'nullable|string|max:100',
        ]);

        $detail = DetailKamarTransaction::with(['kamar:id,kapasitas', 'penghunis'])->findOrFail($request->detail_id);
        $kapasitas = max(1, (int) data_get($detail, 'kamar.kapasitas', 1));

        // potong jumlah input sesuai kapasitas
        $names = array_slice($request->input('names', []), 0, $kapasitas);

        DB::transaction(function () use ($detail, $names, $kapasitas) {
            // urutkan existing penghunis by id untuk dipasangkan per-index
            $existing = $detail->penghunis()->orderBy('id')->get()->values();

            for ($i = 0; $i < $kapasitas; $i++) {
                $name = trim((string) ($names[$i] ?? ''));

                if ($existing->has($i)) {
                    $row = $existing[$i];
                    if ($name === '') {
                        // kosong => hapus baris ini
                        $row->delete();
                    } else {
                        // update nama
                        $row->nama_penghuni = $name;
                        $row->save();
                    }
                } else {
                    // belum ada baris ke-i
                    if ($name !== '') {
                        Penghuni::create([
                            'detail_kamar_transaction_id' => $detail->id,
                            'nama_penghuni'               => $name,
                        ]);
                    }
                }
            }

            // jaga-jaga: jika existing lebih banyak dari kapasitas (data lama), hapus sisanya
            if ($existing->count() > $kapasitas) {
                $detail->penghunis()
                    ->orderBy('id')
                    ->skip($kapasitas)
                    ->take(PHP_INT_MAX)
                    ->delete();
            }
        });

        return back()->with('success', 'Nama penghuni berhasil diperbarui.');
    }

    public function destroyPenghunis($id)
    {
        DB::transaction(function () use ($id) {
            $detail = DetailKamarTransaction::with('penghunis')->findOrFail($id);


            foreach ($detail->penghunis as $p) {
                $p->delete();
            }

            $detail->delete();
        });

        return back()->with('success', 'Jadwal kamar & data penghuni berhasil dihapus.');
    }



    public function kamarTerpakai()
    {
        $today = Carbon::today();

        $formatRange = function ($start, $end) {
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            if ($s->diffInDays($e) === 1) {
                return $s->format('d M Y') . ' – ' . $e->format('d M Y');
            }
            $last = $e->copy()->subDay();
            if ($s->isSameMonth($last) && $s->isSameYear($last)) {
                return $s->format('d') . '–' . $last->format('d M Y');
            } elseif ($s->isSameYear($last)) {
                return $s->format('d M') . '–' . $last->format('d M Y');
            }
            return $s->format('d M Y') . ' – ' . $last->format('d M Y');
        };

        $isOccupiedToday = function ($start, $end) use ($today) {
            $s = Carbon::parse($start)->startOfDay();
            $e = Carbon::parse($end)->startOfDay();
            return $today->betweenIncluded($s, $e->copy()->subDay());
        };

        $rooms = \App\Models\DetailKamarTransaction::with([
            // pastikan relasi Kamar -> properties ada (belongsTo Properties::class, 'properties_id')
            'kamar:id,properties_id,nama_kamar,kapasitas',
            'kamar.properties:id,name',
            'transaction:id,name,kegiatan',
            'transaction:id,name,kegiatan,status',
            'transaction.user:id,name',
            'penghunis:id,detail_kamar_transaction_id,nama_penghuni',
        ])
            ->whereDate('end', '>=', $today)
            // ===== ONLY APPROVED =====
            ->whereHas('transaction', function ($q) {
                $q->where('status', 'approved');
            })
            // =========================
            ->orderBy('start', 'asc')
            ->get()
            ->groupBy('kamar_id')
            ->map(function ($items) use ($formatRange, $isOccupiedToday) {
                $first  = $items->first();
                $kamar  = $first->kamar;
                $propId = $kamar->properties_id;
                $propNm = data_get($kamar, 'properties.name', '(Tanpa Properti)');

                $occupiedNow = $items->contains(fn($d) => $isOccupiedToday($d->start, $d->end));

                $upcoming = $items->sortBy('start')->take(3)->map(function ($d) use ($formatRange) {
                    return [
                        'detail_id'       => $d->id,
                        'range'          => $formatRange($d->start, $d->end),
                        'tx'             => $d->transaction_id,
                        'kegiatan'       => data_get($d, 'transaction.kegiatan', '—'),
                        'pemesan'        => data_get($d, 'transaction.name', '—'),
                        'penghunis'      => $d->penghunis->pluck('nama_penghuni')->values()->all(),
                        'count_penghuni' => $d->penghunis->count(),
                    ];
                })->values();

                $bookings = $items->sortBy('start')->map(function ($d) use ($formatRange) {
                    return [
                        'detail_id'   => $d->id,
                        'transaction' => $d->transaction_id,
                        'range'       => $formatRange($d->start, $d->end),
                        'start'       => $d->start,
                        'end'         => $d->end,
                        'pemesan'     => data_get($d, 'transaction.name', '—'),
                        'kegiatan'    => data_get($d, 'transaction.kegiatan', '—'),
                        'penghunis'   => $d->penghunis->pluck('nama_penghuni')->values()->all(),
                    ];
                })->values();

                return [
                    'property_id'    => $propId,
                    'property_name'  => $propNm,
                    'kamar'          => $kamar,
                    'kapasitas'      => $kamar->kapasitas ?? 1,
                    'occupied'       => $occupiedNow,
                    'upcoming'       => $upcoming,
                    'bookings'       => $bookings,
                    'total'          => $items->count(),
                    'total_penghuni' => $items->sum(fn($d) => $d->penghunis->count()),
                ];
            })
            ->sortBy(function ($r) {
                return strval($r['kamar']->nama_kamar);
            })
            ->values();

        // Group per properti untuk tabs
        $groups = $rooms->groupBy('property_name')->sortKeys();

        // (Opsional) statistik untuk pill filter
        $stats = [
            'total'     => $rooms->count(),
            'occupied'  => $rooms->where('occupied', true)->count(),
            'scheduled' => $rooms->filter(fn($r) => !$r['occupied'] && collect($r['upcoming'])->isNotEmpty())->count(),
            'free'      => $rooms->filter(fn($r) => !$r['occupied'] && collect($r['upcoming'])->isEmpty())->count(),
        ];

        return view('admin.bedroomsUse.index', compact('rooms', 'groups', 'stats'));
    }





   






    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // echo "<pre>";
        // print_r($request->toArray());
        // echo "</pre>";
        // Validasi data
        $request->validate([
            'properties_id' => 'required|exists:properties,id',
            'nama_kamar'    => 'required|string|max:100',
            'kapasitas'     => 'required|integer|min:1',
            'lantai'        => 'required|integer|min:1',
        ]);

        // Simpan ke database
        Kamar::create([
            'properties_id' => $request->properties_id,
            'nama_kamar'    => $request->nama_kamar,
            'kapasitas'     => $request->kapasitas,
            'lantai'        => $request->lantai,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Kamar berhasil ditambahkan!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'nama_kamar' => 'required|string|max:100',
            'kapasitas'  => 'required|integer|min:1',
            'lantai'     => 'required|integer|min:1',
        ]);

        // Ambil kamar berdasarkan ID
        $kamar = Kamar::findOrFail($id);

        // Update data
        $kamar->nama_kamar = $request->nama_kamar;
        $kamar->kapasitas  = $request->kapasitas;
        $kamar->lantai     = $request->lantai;
        $kamar->save();

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Kamar berhasil diupdate!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Cari kamar berdasarkan ID
        $kamar = Kamar::find($id);

        if (!$kamar) {
            return redirect()->back()->with('failed', 'Kamar tidak ditemukan.');
        }

        // Hapus kamar
        $kamar->delete();

        return redirect()->back()->with('success', 'Kamar berhasil dihapus.');
    }



    public function check_kamar($transactionId, $kamarId, Request $r)
    {
        $start = $r->query('start');
        $end   = $r->query('end');


        if (!$start || !$end) {
            return response()->json([
                'ok' => false,
                'message' => 'Start/End wajib diisi',
            ], 422);
        }


        $conflicts = DetailKamarTransaction::where('kamar_id', $kamarId)
            ->where('transaction_id', '!=', $transactionId)
            ->where('start', '<=', $end)
            ->where('end', '>=', $start)
            ->get(['transaction_id', 'start', 'end']);

        return response()->json([
            'ok'        => true,
            'kamar_id'  => (int) $kamarId,
            'tx_id'     => (int) $transactionId,
            'start'     => $start,
            'end'       => $end,
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts,
        ]);
    }
}
