<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BonusGagalUploadExport2 implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoices;

    public function __construct(array $bonusgagal)
    {
        $this->bonusgagal = $bonusgagal;
    }

    public function array(): array
    {
        return $this->bonusgagal;
    }

    public function startCell()
    {
        return 'A2';
    }

    public function headings(): array
    {
        // Penerimaan Bonus & Top Up Bonus
        return [
            'No',
            'Nama',
            'No KTP',
            'No Rekening',
            'Bonus',
        ];
    }
}
