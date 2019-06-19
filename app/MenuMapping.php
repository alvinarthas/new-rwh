<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\SubModul;
use App\SubMapping;

class MenuMapping extends Model
{
    protected $table ='menumapping';
    protected $fillable = [
        'user_id', 'submapping_id'
    ];

    public static function current($user){
        $modul = DB::select("SELECT DISTINCT m.modul_desc,s.submodul_id,s.submodul_desc FROM menumapping mm
        INNER JOIN sub_mapping sm ON sm.id = mm.submapping_id 
        INNER JOIN tblsubmodul s ON s.submodul_id = sm.submodul_id
        INNER JOIN tblmodul m ON m.modul_id = s.modul_id
        WHERE mm.user_id =$user");
        $data = collect();
        foreach ($modul as $mod) {
            $current = collect($mod);
            $submap = DB::select("SELECT submapping_id,jenis_id FROM menumapping 
            INNER JOIN sub_mapping ON menumapping.submapping_id = sub_mapping.id
            WHERE user_id=$user");
            $current->put('submapping',$submap);
            $data->push($current);
        }
        return $data;
    }

    public static function rest($user){
        $submodul = SubModul::all();
        $data = collect();

        foreach ($submodul as $submod) {
            $check_original = SubMapping::where('submodul_id',$submod->submodul_id)->count();
            $check_current = MenuMapping::join('sub_mapping','sub_mapping.id','=','menumapping.submapping_id')->where('user_id',$user)->where('sub_mapping.submodul_id',$submod->submodul_id)->count();

            if($check_original != $check_current){
                $rest = DB::select("SELECT id,jenis_id FROM sub_mapping
                WHERE sub_mapping.id NOT IN (SELECT menumapping.submapping_id FROM menumapping WHERE menumapping.user_id = $user)");
                $subcollect = collect();
                $subcollect->put('modul',$submod->modul->modul_desc);
                $subcollect->put('submodul',$submod->submodul_desc);
                $subcollect->put('submapping',$rest);
                $data->push($subcollect);
            }
        }

        return $data;
    }

    public function submodul(){
        return $this->belongsTo('App\SubModul','submodul_id','submodul_id');
    }
}
