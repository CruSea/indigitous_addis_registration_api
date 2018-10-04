<?php

namespace App;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;


class AttendantExports implements FromCollection, WithHeadings{

    /**
     * @return Builder
     */
    public function collection()
    {
        return Attendant::select(
            'full_name',
            'phone',
            'email',
            'age',
            'sex',
            'region',
            'city',
            'profession',
            'academic_status',
            'conference_year'
        )
            ->orderBy('id', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Phone',
            'Email',
            'Age',
            'Sex',
            'City',
            'Region',
            'Profession',
            'Academic Status'
        ];
    }
}