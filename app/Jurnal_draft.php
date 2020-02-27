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

    public static function viewJurnal($start,$end,$coa,$position,$param)
    {
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

        $collect = array();
        $no = 1;
        foreach($jurnal as $jn){
            $action = "<a href='/jurnal/".$jn->id_jurnal."/edit' class='btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5'>Update</a>";
            $action .= "<a href='javascript:;' onclick='jurnalDelete('{{".$jn->id_jurnal."}}')' class='btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5'>Delete</a>";
            if($jn->AccPos == "Debet"){
                $array = array(
                    "no"          => $no,
                    "id_jurnal"   => $jn->id_jurnal,
                    "date"        => $jn->date,
                    "AccNo"       => $jn->AccNo,
                    "AccName"     => $jn->coa->AccName,
                    "debet"       => $jn->Amount,
                    "credit"      => "",
                    "notes_item"  => $jn->notes_item,
                    "description" => $jn->description,
                );
            }elseif($jn->AccPos == "Credit"){
                $array = array(
                    "no"          => $no,
                    "id_jurnal"   => $jn->id_jurnal,
                    "date"        => $jn->date,
                    "AccNo"       => $jn->AccNo,
                    "AccName"     => $jn->coa->AccName,
                    "debet"       => "",
                    "credit"      => $jn->Amount,
                    "notes_item"  => $jn->notes_item,
                    "description" => $jn->description,
                );
            }
            array_push($collect, $array);
            $no++;
        }

        $data = collect();
        $data->put('data',$collect);
        // $data->put('ttl_debet',$ttl_debet);
        // $data->put('ttl_credit',$ttl_credit);
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

    public static function refreshCogs($data){
        $getTrxId = SalesDet::whereIn('prod_id',$data)->select('trx_id')->groupBy('trx_id')->orderBy('trx_id')->get();
        foreach ($getTrxId as $key) {
            $sales_jurnal = $key->trx->jurnal_id;
            $do_jurnal = DeliveryOrder::where('sales_id',$key->trx_id)->select('jurnal_id')->first();
            $cogs = 0;
            foreach (SalesDet::where('trx_id',$key->trx_id)->select('price','qty','prod_id')->get() as $key2) {
                $avcost = PurchaseDetail::where('prod_id',$key2->prod_id)->where('created_at','<=',$key->trx->created_at)->avg('price');
                $cogs +=  $avcost * $key2->qty;
            }
            // Update Jurnal Sales
            if($sales_jurnal <> 0){
                // debet COGS
                    $jurnal_sales_a = Jurnal::where('id_jurnal',$sales_jurnal)->where('AccNo','5.1')->first();
                    $jurnal_sales_a->amount = $cogs;
                    $jurnal_sales_a->update();
                // Credit Persediaan Barang milik customer
                    $jurnal_sales_b = Jurnal::where('id_jurnal',$sales_jurnal)->where('AccNo','2.1.3')->first();
                    $jurnal_sales_b->amount = $cogs;
                    $jurnal_sales_b->update();
            }
            if($do_jurnal){
            // Update Jurnal DO
                // debet Persediaan Barang milik Customer
                    $jurnal_do_a = Jurnal::where('id_jurnal',$do_jurnal->jurnal_id)->where('AccNo','2.1.3')->first();
                    $jurnal_do_a->amount = $cogs;
                    $jurnal_do_a->update();
                // credit Persediaan Barang digudang
                    $jurnal_do_b = Jurnal::where('id_jurnal',$do_jurnal->jurnal_id)->where('AccNo','1.1.4.1.2')->first();
                    $jurnal_do_b->amount = $cogs;
                    $jurnal_do_b->update();

            }
        }
    }
}
