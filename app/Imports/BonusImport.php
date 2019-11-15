<?php

namespace App\Imports;

use App\Bonus;
use Maatwebsite\Excel\Concerns\ToModel;

class BonusImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Bonus([
            'ktp'       => $row[1],
            'bonus'     => $row[4],
        ]);
    }
}
