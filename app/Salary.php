<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\RecordPoin;
use App\Purchase;
use App\Sales;

class Salary extends Model
{
    protected $table ='tbl_salary';
    protected $fillable = [
        'month', 'year','bv','hari_kerja','creator'
    ];

    public static function getPerhitunganGaji($bulan,$tahun){
        $salaries = Salary::join('tbl_salary_detail as sd','tbl_salary.id','=','sd.salary_id')->where('tbl_salary.month',1)->where('tbl_salary.year',2018)->select('sd.*')->get();
    }

    public static function currentBonus($emp){
        // Get Month, Year
        $data = collect();
        $tahun = date('Y');
        $bulan = date('m');
        $employee = Employee::where('id',$emp)->first();
        $role = $employee->rolemapping->role;
        // Get BV
        $bv = Sales::getBV($bulan,$tahun);
        $bonus_divisi = 0;
        // Total Poin
        $ttl_poin_internal = RecordPoin::totalPoin($bulan,$tahun,1);
        $ttl_poin_logistik = RecordPoin::totalPoin($bulan,$tahun,0);
        $ttl_poin_kendali_perusahaan = Purchase::countPost($bulan,$tahun);
        $ttl_poin_top3 = 0;

        // Anggaran Bonus
        $anggaran_internal = $bv*0.3;
        $anggaran_logistik = $bv*0.25;
        $anggaran_kendali_perusahaan = $bv*0.1;
        $anggaran_top3 = 0;
        $anggaran_eom = $bv*0.1;

        // Value Per Poin
        if($ttl_poin_internal == 0){
            $value_share_internal = 0;
        }else{
            $value_share_internal = $anggaran_internal/$ttl_poin_internal;
        }

        if($ttl_poin_logistik == 0){
            $value_share_logistik = 0;
        }else{
            $value_share_logistik = $anggaran_logistik/$ttl_poin_logistik;
        }

        if($ttl_poin_kendali_perusahaan == 0){
            $value_share_kendali_perusahaan = 0;
        }else{
            $value_share_kendali_perusahaan = $anggaran_kendali_perusahaan/$ttl_poin_kendali_perusahaan;
        }

        if($ttl_poin_top3 == 0){
            $value_share_top3 = 0;
        }else{
            $value_share_top3 = $anggaran_top3/$ttl_poin_top3;
        }

        // Bonus Pegawai
        // Share Internal
        $poin_internal = RecordPoin::sumPoin2($emp,$bulan,$tahun,1);

        $value_internal = $poin_internal*$value_share_internal;
        // Share Logistik
        $poin_logistik = RecordPoin::sumPoin2($emp,$bulan,$tahun,0);

        $value_logistik = $poin_logistik*$value_share_logistik;

        // Share Kendali Perusahaan
        $poin_kendali_perusahaan = Purchase::sharePost($bulan,$tahun,$emp);
        if(substr($role->role_name,0,7) == "Manager" || substr($role->role_name,0,7) == "General"){
            $poin_kendali_perusahaan = 0;
        }

        $value_kendali_perusahaan = $poin_kendali_perusahaan*$value_share_kendali_perusahaan;

        // Share Top 3
        $arr_top3 = Purchase::getTop3($bulan,$tahun);
        if (in_array($emp, (array) $arr_top3)){
            $poin_top3 = 1;
        }else{
            $poin_top3 = 0;
        }

        $value_top3 = $poin_top3*$value_share_top3;

        // Total Bonus
        $total_bonus = $value_internal+$value_logistik+$value_kendali_perusahaan+$value_top3+$bonus_divisi;

        // Take Home Pay
        $take_home_pay = $role->gaji_pokok+$role->tunjangan_jabatan+$total_bonus;

        // Poin value
        $data->put('internal',$value_internal);
        $data->put('logistik',$value_logistik);
        $data->put('kendali',$value_kendali_perusahaan);
        $data->put('top3',$value_top3);

        // 
        $data->put('total_bonus',$total_bonus);
        $data->put('gaji_pokok',$role->gaji_pokok);
        $data->put('tunjangan_jabatan',$role->tunjangan_jabatan);
        $data->put('take_home_pay',$take_home_pay);

        return $data;
    }
}
