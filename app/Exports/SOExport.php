<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SOExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $sales;

    public function __construct(array $sales)
    {
        $this->sales = $sales;
    }

    public function array(): array
    {
        return $this->sales;
    }

    public function startCell()
    {
        return 'A2';
    }

    public function headings(): array
    {
        // Sales Detail
        return [
            'No',
            'Transaction ID',
            'Transaction Date',
            'Customer Name',
            'Product ID',
            'Product Name',
            'Price',
            'Qty',
            'Unit',
            'Sub Total',
            'BV per Product',
            'Total BV',
        ];
    }
}
