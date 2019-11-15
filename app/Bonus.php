<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $table ='tblbonus';
    protected $fillable = [
        'noid','bulan','tahun','bonus', 'creator', 'id_jurnal'
    ];

    public $timestamps = true;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
