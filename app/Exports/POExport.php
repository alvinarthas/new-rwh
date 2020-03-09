<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class POExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchase;

    public function __construct(array $purchase)
    {
        $this->purchase = $purchase;
    }

    public function array(): array
    {
        return $this->purchase;
    }

    public function startCell()
    {
        return 'A2';
    }

    public function headings(): array
    {
        // Purchase Detail
        return [
            'No',
            'Transaction ID',
            'Transaction Date',
            'Supplier',
            'Bulan Bonus',
            'Product ID',
            'Product Name',
            'Price',
            'Price Dist',
            'Qty',
            'Unit',
            'Sub Total Price',
            'Sub Total Price Dist'
        ];
    }
}
