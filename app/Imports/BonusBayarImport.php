<?php

namespace App\Imports;

use App\BonusBayar;
use Maatwebsite\Excel\Concerns\ToModel;

class BonusBayarImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BonusBayar([
            'no_rek'    => $row[1],
            'bonus'     => $row[3],
        ]);
    }
}
