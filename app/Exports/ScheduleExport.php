<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping
{
    private $rowNumber = 0;

    public function collection()
    {
        return Schedule::with(['cinema', 'movie'])->get();
    }

    public function headings(): array
    {
        return ['No', 'Bioskop', 'Film', 'Jam', 'Harga'];
    }

    public function map($schedule): array
    {
        return [
            ++$this->rowNumber,
            $schedule->cinema->name,
            $schedule->movie->title,
            implode(", ", $schedule->hours), // fix
            'Rp' . number_format($schedule->price, 0, ',', '.') // fix

        ];
    }
}
