<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransitProduct extends Model
{
    protected $table ='tbl_transit_product';
    protected $fillable = [
        'tgl', 'keterangan','creator'
    ];

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }
}
