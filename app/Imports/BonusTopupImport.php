<?php

namespace App\Imports;

use App\TopUpBonus;
use Maatwebsite\Excel\Concerns\ToModel;

class BonusTopupImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TopUpBonus([
            'no_rek'    => $row[1],
            'bonus'     => $row[3],
        ]);
    }
}
