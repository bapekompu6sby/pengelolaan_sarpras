<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Properties;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            ->get();


        $properties->transform(function ($p) {
            $p->floors = $p->kamar
                ->filter(fn($k) => !is_null($k->lantai) && (int)$k->lantai !== 0)
                ->groupBy(fn($k) => (int) $k->lantai)  
                ->sortKeys();                            

            return $p;
        });

        return view('admin.kamar', compact('properties'));
    }






    public function kamarTerpakai()
    {
        $today = Carbon::today();

        // Format rentang tanggal (end eksklusif = checkout)
        $formatRange = function ($start, $end) {
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            $diff = $s->diffInDays($e);

            if ($diff === 1) {
                return $s->format('d M Y') . ' – ' . $e->format('d M Y'); // 1 malam (tetap tampil keduanya)
            }
            $last = $e->copy()->subDay(); // tampil s/d end-1
            if ($s->isSameMonth($last) && $s->isSameYear($last)) {
                return $s->format('d') . '–' . $last->format('d M Y');
            } elseif ($s->isSameYear($last)) {
                return $s->format('d M') . '–' . $last->format('d M Y');
            }
            return $s->format('d M Y') . ' – ' . $last->format('d M Y');
        };

        // Occupied jika start <= today < end
        $isOccupiedToday = function ($start, $end) use ($today) {
            $s = Carbon::parse($start)->startOfDay();
            $e = Carbon::parse($end)->startOfDay();
            return $today->betweenIncluded($s, $e->copy()->subDay());
        };

        $rooms = DetailKamarTransaction::with([
            'kamar',                    // id, properties_id, nama_kamar, kapasitas
            'transaction.user',        // pemesan (name)
            'penghunis'                // list penghuni
        ])
            // ambil yang masih relevan dari hari ini ke depan (termasuk yang masih menginap)
            ->whereDate('end', '>=', $today)
            ->orderBy('start', 'asc')
            ->get()
            ->groupBy('kamar_id')
            ->map(function ($items) use ($formatRange, $isOccupiedToday) {
                $first = $items->first();
                $kamar = $first->kamar;

                $occupiedNow = $items->contains(fn($d) => $isOccupiedToday($d->start, $d->end));

                // ringkasan 3 jadwal terdekat + penghuni
                $upcoming = $items->sortBy('start')->take(3)->map(function ($d) use ($formatRange) {
                    return [
                        'range'          => $formatRange($d->start, $d->end),
                        'tx'             => $d->transaction_id,
                        'guest'          => data_get($d, 'transaction.name', '—'),
                        'penghunis'      => $d->penghunis->pluck('nama_penghuni')->values(),
                        'count_penghuni' => $d->penghunis->count(),
                    ];
                })->values();

                // opsional: daftar lengkap booking kamar ini (kalau mau dipakai nanti)
                $bookings = $items->sortBy('start')->map(function ($d) use ($formatRange) {
                    return [
                        'detail_id'  => $d->id,
                        'transaction' => $d->transaction_id,
                        'range'      => $formatRange($d->start, $d->end),
                        'start'      => $d->start,
                        'end'        => $d->end,
                        'pemesan'    => data_get($d, 'transaction.name', '—'),
                        'penghunis' => $d->penghunis->pluck('nama_penghuni')->values()->all(),

                    ];
                })->values();

                return [
                    'kamar'           => $kamar, // biar Blade tetap bisa $room['kamar']->nama_kamar
                    'kapasitas'       => $kamar->kapasitas ?? 1,
                    'occupied'        => $occupiedNow,
                    'upcoming'        => $upcoming,
                    'bookings'        => $bookings,
                    'total'           => $items->count(),
                    'total_penghuni'  => $items->sum(fn($d) => $d->penghunis->count()),
                ];
            })
            ->values(); // biar index numerik


        // Debug rapi (pilih salah satu):
        // return response()->json($rooms);
        // logger()->info('kamarTerpakai', ['rooms' => $rooms->toArray()]);

        return view('admin.kamar_terpakai', compact('rooms'));
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
