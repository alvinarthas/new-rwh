<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubModul extends Model
{
    protected $table ='tblsubmodul';
    protected $fillable = [
        'submodul_id', 'submodul_desc', 'submodul_page','modul_id'
    ];

    public static function getSub($modul_id){
        return SubModul::where('modul_id',$modul_id)->orderBy('submodul_id','asc')->get();
    }

    public function modul(){
        return $this->belongsTo('App\Modul','modul_id','modul_id');
    }
}
