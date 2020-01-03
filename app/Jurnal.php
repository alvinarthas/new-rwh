<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Coa;
use App\Product;
use App\ManageHarga;
use App\Purchase;
use App\Member;
use App\BankMember;
use App\BonusBayar;

class Jurnal extends Model
{
    protected $table ='tbljurnal';
    protected $fillable = [
        'id_jurnal', 'AccNo','AccPos','Amount','company_id','date','description','creator','status','nama_category','budget_month','budget_year','notes_item'
    ];

    public function coa(){
        return $this->belongsTo('App\Coa','AccNo','AccNo');
    }

    public static function getJurnalID($jenis){
        $jurnal = Jurnal::where('id_jurnal','LIKE',$jenis.'%')->orderBy(DB::raw('CAST(SUBSTRING(id_jurnal, 4, 10) AS INT)'),'desc')->select('id_jurnal')->distinct('id_jurnal');
        $count_jurnal = $jurnal->count();
        if($count_jurnal == 0){
            $id_jurnal = $jenis.".1";
        }else{
            $getJurnal = $jurnal->first();
            $num_jurnal = intval(substr($getJurnal->id_jurnal,3,10))+1;
            $id_jurnal = $jenis.".".$num_jurnal;
        }
        return $id_jurnal;
    }

    public static function viewJurnal($start,$end,$coa,$position,$param){

        if ($param == "umum") {
            $jurnal = Jurnal::where('id_jurnal','LIKE','JN%');
            $jurdebet = Jurnal::where('id_jurnal','LIKE','JN%');
            $jurcredit = Jurnal::where('id_jurnal','LIKE','JN%');
        }elseif ($param == "mutasi") {
            $jurnal = Jurnal::where('id_jurnal','LIKE','%%');
            $jurdebet = Jurnal::where('id_jurnal','LIKE','%%');
            $jurcredit = Jurnal::where('id_jurnal','LIKE','%%');
        }

        if($coa <> "all"){
            $jurnal->where('AccNo',$coa);
            $jurdebet->where('AccNo',$coa);
            $jurcredit->where('AccNo',$coa);
        }

        if($position <> "all"){
            $jurnal->where('AccPos',$position);
            $jurdebet->where('AccPos',$position);
            $jurcredit->where('AccPos',$position);
        }

        if($start <> NULL && $end <> NULL){
            $jurnal->whereBetween('date',[$start,$end]);
            $jurdebet->whereBetween('date',[$start,$end]);
            $jurcredit->whereBetween('date',[$start,$end]);
        }

        $ttl_debet = $jurdebet->where('AccPos','Debet')->sum('Amount');
        $ttl_credit = $jurcredit->where('AccPos','Credit')->sum('Amount');
        $jurnal = $jurnal->orderBy('date','asc')->get();

        $data = collect();
        $data->put('data',$jurnal);
        $data->put('ttl_debet',$ttl_debet);
        $data->put('ttl_credit',$ttl_credit);
        return $data;
    }

    public static function generalLedger($start,$end,$coa){

        $jurnal = Jurnal::where('AccNo',$coa);
        $jurdebet = Jurnal::where('AccNo',$coa);
        $jurcredit = Jurnal::where('AccNo',$coa);

        if($start <> NULL && $end <> NULL){
            $jurnal->whereBetween('date',[$start,$end]);
            $jurdebet->whereBetween('date',[$start,$end]);
            $jurcredit->whereBetween('date',[$start,$end]);
        }

        $ttl_debet = $jurdebet->where('AccPos','Debet')->sum('Amount');
        $ttl_credit = $jurcredit->where('AccPos','Credit')->sum('Amount');
        $jurnal = $jurnal->orderBy('date','asc')->get();

        $data = collect();
        $data->put('data',$jurnal);
        $data->put('ttl_debet',$ttl_debet);
        $data->put('ttl_credit',$ttl_credit);
        return $data;
    }

    public static function addJurnal($id_jurnal,$amount,$date,$desc,$coa,$position,$user_id=null){
        if($user_id != null){
            $user = $user_id;
        }else{
            $user = session('user_id');
        }

        $jurnal = new Jurnal(array(
            'id_jurnal' => $id_jurnal,
            'AccNo' => $coa,
            'AccPos' => $position,
            'Amount' => $amount,
            'company_id' => 1,
            'date' => $date,
            'description' => $desc,
            'creator' => $user,
        ));

        $jurnal->save();
    }
}
