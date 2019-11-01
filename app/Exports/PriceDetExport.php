<?php

namespace App\Exports;

use App\PriceDet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PriceDetExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $pricedet;

    public function __construct(array $pricedet)
    {
        $this->pricedet = $pricedet;
    }

    public function array(): array
    {
        return $this->pricedet;
    }

    public function startCell()
    {
        return 'A3';
    }

    public function headings(): array
    {
        return [
            'NO',
            'PRODUCT NAME',
            'PRODUCT BRAND',
            'PRICE',
            'BV',
            'SUPPLIER',
        ];
    }
}
