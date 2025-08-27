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
        $properties = Properties::with('kamar')
            ->whereIn('type', ['asrama', 'paviliun'])
            ->get();

        return view('admin.kamar', compact('properties'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function kamarTerpakai()
    {
        $today = Carbon::today();

        $rooms = DetailKamarTransaction::with(['kamar', 'transaction.user'])
            ->whereDate('start', '>=', $today)
            ->orderBy('start', 'asc')
            ->get()
            ->groupBy('kamar_id')
            ->map(function ($items) use ($today) {
                $first = $items->first();
                $kamar = $first->kamar;

                $formatRange = function ($start, $end) {
                    $s = Carbon::parse($start);
                    $e = Carbon::parse($end);
                    $diff = $s->diffInDays($e);

                    if ($diff === 1) {
                        return $s->format('d M Y') . ' – ' . $e->format('d M Y');
                    }
                    $last = $e->copy()->subDay(); // checkout eksklusif
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

                $guestName = function ($detail) {
                    return data_get($detail, 'transaction.peminjam_nama')
                        ?? data_get($detail, 'transaction.pemesan_nama')
                        ?? data_get($detail, 'transaction.nama_pemesan')
                        ?? data_get($detail, 'transaction.user.name')
                        ?? '—';
                };

                $occupiedNow = $items->contains(fn($d) => $isOccupiedToday($d->start, $d->end));

                $upcoming = $items->sortBy('start')->take(3)->map(function ($d) use ($formatRange, $guestName) {
                    return [
                        'range' => $formatRange($d->start, $d->end),
                        'tx'    => $d->transaction_id,
                        'guest' => $guestName($d),
                    ];
                });

                return [
                    'kamar'     => $kamar,
                    'kapasitas' => $kamar->kapasitas ?? 1,
                    'occupied'  => $occupiedNow,
                    'upcoming'  => $upcoming,
                    'total'     => $items->count(),
                ];
            });

        return view('admin.kamar_terpakai', compact('rooms'));
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'penginapan_id' => 'required|exists:properties,id',
            'nama_kamar'    => 'required|string|max:100',
            'kapasitas'     => 'required|integer|min:1',
        ]);

        // Simpan ke database
        Kamar::create([
            'penginapan_id' => $request->penginapan_id,
            'nama_kamar'    => $request->nama_kamar,
            'kapasitas'     => $request->kapasitas,
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
        ]);

        // Ambil kamar berdasarkan ID
        $kamar = Kamar::findOrFail($id);

        // Update data
        $kamar->nama_kamar = $request->nama_kamar;
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

        // validasi minimal
        if (!$start || !$end) {
            return response()->json([
                'ok' => false,
                'message' => 'Start/End wajib diisi',
            ], 422);
        }

        // overlap: req_start < existing_end && req_end > existing_start
        $conflicts = DetailKamarTransaction::where('kamar_id', $kamarId)
            ->where('transaction_id', '!=', $transactionId) // abaikan transaksi yang sedang diedit
            ->where('start', '<', $end)
            ->where('end', '>', $start)
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
