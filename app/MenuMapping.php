<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MenuMapping extends Model
{
    protected $table ='menumapping';
    protected $fillable = [
        'user_id', 'submodul_id','jenis'
    ];

    public static function current($user){
        return MenuMapping::where('user_id',$user)->get();
    }

    public static function rest($user){
        return DB::select("SELECT * FROM tblsubmodul WHERE submodul_id NOT IN(SELECT submodul_id FROM menumapping WHERE menumapping.user_id = $user )");
    }

    public function submodul(){
        return $this->belongsTo('App\SubModul','submodul_id','submodul_id');
    }
}
