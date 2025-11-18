<?php

namespace App\Exports;

use App\Models\Movie;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MovieExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Movie::all();
    }
    private $rowNumber = 0;
public function map($movie): array
{
    $hours = 0;
    $minutes = 0;

    if (is_numeric($movie->duration)) {
        // Kalau duration dalam menit
        $hours = floor($movie->duration / 60);
        $minutes = $movie->duration % 60;
    } else {
        try {
            // Kalau duration format waktu
            $time = \Carbon\Carbon::createFromFormat('H:i:s', $movie->duration);
            $hours = (int) $time->format('H');
            $minutes = (int) $time->format('i');
        } catch (\Exception $e) {
            // fallback kalau null/invalid
            $hours = 0;
            $minutes = 0;
        }
    }

    return [
        ++$this->rowNumber,
        $movie->title,
        $hours . " Jam " . $minutes . " Menit",
        $movie->genre,
        $movie->director,
        $movie->age_rating . "+",
        asset('storage/' . $movie->poster),
        $movie->description,
    ];
}


    public function headings(): array
    {
        return [
            'No',
            'Judul',
            'Durasi',
            'Genre',
            'Sutradara',
            'Usia Minimal',
            'Poster',
            'Sinopsis'
        ];
    }
}
