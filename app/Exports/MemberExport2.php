<?php

namespace App\Exports;

use App\Member;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MemberExport2 implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoices;

    public function __construct(array $member)
    {
        $this->member = $member;
    }

    public function array(): array
    {
        return $this->member;
    }

    public function startCell()
    {
        return 'A2';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'ID Member',
            'No KTP',
            'Alamat',
            'Tempat Tanggal Lahir',
            'Koordinator',
            'Sub Koordinator',
            'Bank Member',
            'Perusahaan Member',
        ];
    }
}
