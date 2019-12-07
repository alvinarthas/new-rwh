<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $table ='tblbonus';
    protected $primaryKey = 'id_bonus';
    protected $fillable = [
        'noid','tgl','bulan','tahun','bonus', 'perusahaan_id','creator', 'id_jurnal'
    ];

    public $timestamps = true;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
