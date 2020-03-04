<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Employee;

class SalaryDet extends Model
{
    protected $table ='tbl_salary_detail';
    protected $fillable = [
        'salary_id', 'employee_id','bonus','gaji_pokok','tunjangan_jabatan','bonus_jabatan','take_home_pay'
    ];

    public function employee(){
        return $this->belongsTo('App\Employee');
    }

    public static function countGajiLebih($id){
        $emps = Employee::join('tblemployeerole as er','er.username','=','tblemployee.username')->join('tblrole as r','r.id','er.role_id')->where('r.role_name','Supervisor')->orWhere('r.role_name','LIKE','Staff%')->select('tblemployee.id')->get();

        $count=0;
        foreach($emps as $emp){
            $take_home = SalaryDet::where('salary_id',$id)->where('employee_id',$emp->id)->first();

            if($take_home->take_home_pay >= 5600000){
                $count++;
            }
        }
        return $count;
    }

    public static function saveGajiManager($bonus_manager,$month,$year){
        $emps = Employee::join('tblemployeerole as er','er.username','=','tblemployee.username')->join('tblrole as r','r.id','er.role_id')->where('r.role_name','General Manager')->orWhere('r.role_name','LIKE','Manager%')->select('tblemployee.id')->get();

        foreach($emps as $emp){
             // Insert to DB Bonus Pegawai
             $bonpeg = BonusPegawai::where('employee_id',$emp->id)->where('month',$month)->where('year',$year)->first();
             $totbon = $bonpeg->total_bonus + $bonus_manager;

             $bonpeg->bonus_divisi = $bonus_manager;
             $bonpeg->total_bonus = $totbon;
             $bonpeg->save();

             // Insert to Salary Det
             $saldet = SalaryDet::where('id',$bonpeg->salary_det_id)->first();

             $tot_takehome = $totbon+$saldet->take_home_pay;

             $saldet->bonus = $totbon;
             $saldet->take_home_pay = $tot_takehome;
             $saldet->save();
        }
    }
}
