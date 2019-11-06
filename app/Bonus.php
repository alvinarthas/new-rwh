<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $table ='tblbonus';
    protected $fillable = [
        'member_id','bulan','tahun','bonus', 'creator', 'id_jurnal'
    ];

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
