<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Koordinator extends Model
{
    protected $table ='tblkoordinator';
    protected $fillable = [
        'nama','alamat','telp', 'ktp', 'memberid'
    ];

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
