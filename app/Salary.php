<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table ='tbl_salary';
    protected $fillable = [
        'month', 'year','bv','hari_kerja','creator'
    ];

    public static function getPerhitunganGaji($bulan,$tahun){
        $salaries = Salary::join('tbl_salary_detail as sd','tbl_salary.id','=','sd.salary_id')->where('tbl_salary.month',1)->where('tbl_salary.year',2018)->select('sd.*')->get();
    }

    public static function defaultValue(){
        // Get Month, Year
        $tahun = date('Y');
        $bulan = date('m');
        // Get BV
        $bv = Sales::getBV($request->bulan,$request->tahun);
        // Total Poin

        // Anggaran Bonus

        // Value Per Poin
    }

    public static function currentBonus($emp){

    }
}
