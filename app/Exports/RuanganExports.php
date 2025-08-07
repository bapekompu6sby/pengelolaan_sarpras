<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RuanganExports implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Transaction::select('instansi', 'kegiatan', 'start', 'end', 'property_id', 'description')
                ->get();
    }

    public function headings(): array
    {
        return [
            'Instansi',
            'Kegiatan',
            'Tanggal mulai',
            'Tanggal selesai',
            'Tempat',
            'Keterangan',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->instansi,
            $transaction->kegiatan,
            date("d-m-Y", strtotime($transaction->start)),
            date("d-m-Y", strtotime($transaction->end)),
            $transaction->properties->name,
            $transaction->description,
        ];
    }
}
