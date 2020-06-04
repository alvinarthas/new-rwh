<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PiutangKaryawan extends Model
{
    protected $table ='tblpiutang';
    protected $fillable = [
        'employee_id','description','amount','status','AccNo','date','creator', 'id_jurnal',
    ];

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public function coa(){
        return $this->belongsTo('App\Coa','AccNo','AccNo');
    }

    public static function getPiutang($employee){
        $plus = PiutangKaryawan::where('employee_id',$employee)->where('status',0)->sum('amount');
        $minus = PiutangKaryawan::where('employee_id',$employee)->where('status',1)->sum('amount');

        return $plus-$minus;
    }

    public static function getData(){
        $employees = Employee::where('id', '!=', 1)->get();
        $data = collect();
        foreach($employees as $employee){
            $piutang = collect();
            $amount = PiutangKaryawan::getPiutang($employee->id);

            $piutang->put('name',$employee->name);
            $piutang->put('id',$employee->id);
            $piutang->put('amount',$amount);
            $data->push($piutang);
        }

        $data = $data->sortByDesc('amount');

        return $data;
    }
}
