<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\DetailKamarTransaction;
use App\Models\Penghuni; // pastikan modelnya ada
use Carbon\Carbon;

class DetailTransactionController extends Controller
{



    public function store(Request $request)
    {
        // 1) Validasi payload dari form
        $validated = $request->validate([
            'transaction_id' => ['required', 'integer', 'exists:transactions,id'],
            'start'          => ['required', 'date'],
            'end'            => ['required', 'date', 'after_or_equal:start'],
            'kamar_id'       => ['required', 'array', 'min:1'],
            'kamar_id.*'     => ['integer', 'distinct', 'exists:kamar,id'],

            'nama_penghuni'  => ['required', 'array'],
            // isinya boleh kosong stringnya, nanti kita filter di controller
        ], [], [
            'kamar_id' => 'kamar'
        ]);

        DB::transaction(function () use ($request) {
            $txId  = (int) $request->transaction_id;
            $start = $request->start;
            $end   = $request->end;
            $now   = now();

            foreach ($request->kamar_id as $kamarId) {
                $kamarId = (int) $kamarId;

                // (opsional) server-side overlap check, kalau mau aman double check:
                // $conflict = DetailKamarTransaction::where('kamar_id', $kamarId)
                //     ->where('transaction_id','!=',$txId)
                //     ->where('start','<', $end)
                //     ->where('end','>', $start)
                //     ->exists();
                // if ($conflict) {
                //     throw new \RuntimeException("Kamar {$kamarId} bentrok pada rentang tanggal.");
                // }

                // 2) Insert DETAIL KAMAR
                $detail = DetailKamarTransaction::create([
                    'transaction_id' => $txId,
                    'kamar_id'       => $kamarId,
                    'start'          => $start,
                    'end'            => $end,
                ]);

                // 3) Ambil list nama untuk kamar ini => insert ke tabel PENGHUNI
                $names = collect(data_get($request->nama_penghuni, $kamarId, []))
                    ->map(fn($n) => trim((string) $n))
                    ->filter() // buang yg kosong
                    ->values();

                if ($names->isNotEmpty()) {
                    $rows = $names->map(fn($nama) => [
                        'detail_kamar_transaction_id' => $detail->id,
                        'nama_penghuni'               => $nama,
                        'created_at'                  => $now,
                        'updated_at'                  => $now,
                    ])->all();

                    Penghuni::insert($rows);
                }
            }
        });

        return back()->with('success', 'Detail kamar & penghuni berhasil disimpan.');
    }
}
