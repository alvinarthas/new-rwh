<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseMap extends Model
{
    protected $table ='map_purchase';
    protected $fillable = [
        'employee_id','supplier_id'
    ];

    public function employee(){
        return $this->belongsTo('App\Employee');
    }

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier_id');
    }
}
