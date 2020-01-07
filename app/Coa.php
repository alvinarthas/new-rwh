<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Jurnal;

class Coa extends Model
{
    protected $table ='tblcoa';
    protected $fillable = [
        'AccNo', 'AccName', 'SaldoNormal', 'StatusAccount', 'SaldoAwal', 'company_id', 'AccParent'
    ];

    public static function checkChild($id){
        return Coa::where('AccParent',$id)->where('AccNo','NOT LIKE',$id)->count();
    }

    public static function getChild($id){
        return Coa::where('AccParent',$id)->where('AccNo','NOT LIKE',$id)->get();
    }

    public static function nettSales($start,$end){
        $sales = Jurnal::where('AccNo','4.1.1');
        if($start <> NULL && $end <> NULL){
            $sales->whereBetween('date',[$start,$end]);
        }
        return $sales->sum('Amount');
    }

    public static function cogs($start,$end){
        $sales = Jurnal::where('AccNo','5.1');
        if($start <> NULL && $end <> NULL){
            $sales->whereBetween('date',[$start,$end]);
        }
        return $sales->sum('Amount');
    }

    public static function biaya($start,$end){
        $parent = Coa::where('AccNo','6')->first();
        $data_parent = collect();
        $subparent = collect();

        foreach(Coa::where('AccNo','LIKE','6.1')->orwhere('AccNo','LIKE','6.2')->get() as $key){
            $subsub = collect();
            
            $sales = Jurnal::join('tblcoa','tblcoa.AccNo','=','tbljurnal.AccNo')->where('tbljurnal.AccNo','LIKE',$key->AccNo.'%');

            if($start <> NULL && $end <> NULL){
                $sales->whereBetween('date',[$start,$end]);
            }
            $sum = $sales;
            
            $sales->select(DB::raw('SUM(tbljurnal.Amount) as total'),'tblcoa.AccName','tblcoa.AccNo')->groupBy('tbljurnal.AccNo');

            $subsub->put('name',$key->AccName);
            $subsub->put('no',$key->AccNo);
            $subsub->put('amount',$sum->sum('Amount'));
            $subsub->put('data',$sales->get());
            $subparent->push($subsub);
        }
        $data_parent->put('name',$parent->AccName);
        $data_parent->put('no',$parent->AccNo);
        $data_parent->put('amount',0);
        $data_parent->put('data',$subparent);

        return $data_parent;
    }

    public static function laba_rugi($start,$end){
        $sales = Jurnal::join('tblcoa','tblcoa.AccNo','=','tbljurnal.AccNo')->where('tbljurnal.AccNo','LIKE','7.3')->orwhere('tbljurnal.AccNo','LIKE','7.4');

        if($start <> NULL && $end <> NULL){
            $sales->whereBetween('date',[$start,$end]);
        }

        $sales->select(DB::raw('SUM(tbljurnal.Amount)'),'tblcoa.AccName','tblcoa.AccNo')->groupBy('tbljurnal.AccNo')->get();

        return $sales;
    }

    public static function laba_rugi_bonus($start,$end){
        $sales = Jurnal::join('tblcoa','tblcoa.AccNo','=','tbljurnal.AccNo')->where('tbljurnal.AccNo','LIKE','7.1')->orwhere('tbljurnal.AccNo','LIKE','7.2');

        if($start <> NULL && $end <> NULL){
            $sales->whereBetween('date',[$start,$end]);
        }
        $sales->select(DB::raw('SUM(tbljurnal.Amount)'),'tblcoa.AccName','tblcoa.AccNo')->groupBy('tbljurnal.AccNo')->get();

        return $sales;
    }
    
    public static function laba_bersih_non($start,$end){
        
    }
}
