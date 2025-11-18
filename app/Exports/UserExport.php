<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $rownumber = 0;
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return ['No', 'Nama', 'Email', 'Role','Created At'];
    }

    public function map($user): array
    {
        return [

            ++$this->rownumber,
            $user->name,
            $user->email,
            $user->role,
            $user->created_at->format('d-m-Y'),
        ];
    }
}
