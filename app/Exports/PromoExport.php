<?php

namespace App\Exports;

use App\Models\Promo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PromoExport implements FromCollection, WithHeadings, WithMapping
{
    private $rownumber = 0;

    public function collection()
    {
        return Promo::all();
    }

    public function headings(): array
    {
        return ['No', 'Kode Promo', 'Diskon', 'Status'];
    }

    public function map($promo): array
    {
        return [
            ++$this->rownumber,
            $promo->promo_code,
            strtolower($promo->type) === 'percent'
                ? $promo->discount . ' %' // langsung tampil "20 %"
                : 'Rp ' . number_format($promo->discount, 0, ',', '.'), // tampil "Rp 50.000"
            $promo->actived ? 'Aktif' : 'Nonaktif',
        ];
    }
}
