<?php

namespace App\Imports;

use App\BonusGagal;
use Maatwebsite\Excel\Concerns\ToModel;

class BonusGagalImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BonusGagal([
            'ktp'       => $row[1],
            'member_id' => $row[2],
            'nama'      => $row[3],
            'bonus'     => $row[4],
        ]);
    }
}
