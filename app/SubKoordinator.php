<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subkoordinator extends Model
{
    protected $table ='tblsubkoordinator';
    protected $fillable = [
        'nama','alamat','telp', 'ktp', 'memberid', 'creator', 'created'
    ];

    public $timestamps = false;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
