<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GajiPokok extends Model
{
    protected $table ='tbl_gajipokok';
    protected $fillable = [
        'employee_id', 'gaji_pokok','tunjangan_jabatan'
    ];

    public function employee(){
        return $this->belongsTo('App\Employee');
    }
}
