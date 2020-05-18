<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RealisasiBonusExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $bonus;

    public function __construct(array $bonus)
    {
        $this->bonus = $bonus;
    }

    public function array(): array
    {
        return $this->bonus;
    }

    public function startCell()
    {
        return 'A3';
    }

    public function headings(): array
    {
        return [
            'No',
            'KTP',
            'Nama',
            'Perhitungan Bonus',
            'Realisasi Bonus',
            'Selisih',
        ];
    }
}
