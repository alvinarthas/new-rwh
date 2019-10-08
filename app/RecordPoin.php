<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecordPoin extends Model
{
    protected $table ='record_poin';
    protected $fillable = [
        'employee_id', 'poin','date','jenis','creator'
    ];

    public function employee(){
        return $this->belongsTo('App\Employee');
    }

    public static function sumPoin($employee,$start,$end,$jenis){
        return RecordPoin::where('employee_id',$employee)->whereBetween('date',[$start,$end])->where('jenis',$jenis)->sum('poin');
    }

    public static function sumPoin2($employee,$month,$year,$jenis){
        return RecordPoin::where('employee_id',$employee)->whereMonth('date',$month)->whereYear('date',$year)->where('jenis',$jenis)->sum('poin');
    }

    public static function totalPoin($month,$year,$jenis){
        return RecordPoin::whereMonth('date',$month)->whereYear('date',$year)->where('jenis',$jenis)->sum('poin');
    }
}
