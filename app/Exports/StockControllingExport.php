<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockControllingExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoices;

    public function __construct(array $stock)
    {
        $this->stock = $stock;
    }

    public function array(): array
    {
        return $this->stock;
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
            'Supplier',
            'Product ID',
            'Nama Produk',
            'Indent',
            'di Gudang',
            'milik Customer',
            'Nett',
        ];
    }
}
