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

    public static function refreshCogs($data){
        $getTrxId = SalesDet::whereIn('prod_id',$data)->select('trx_id')->groupBy('trx_id')->orderBy('trx_id')->get();
        foreach ($getTrxId as $key) {
            // SALES
            $sales_jurnal = $key->trx->jurnal_id;
            $cogs = 0;
            foreach (SalesDet::where('trx_id',$key->trx_id)->select('price','qty','prod_id')->get() as $key2) {
                $avcost = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key2->prod_id)->where('tblpotrx.tgl','<=',$key->trx->trx_date)->avg('tblpotrxdet.price');
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

            // DO
            foreach(DeliveryOrder::where('sales_id',$key->trx_id)->select('id','jurnal_id')->get() as $dokey){
                $do_sum = 0;
               
                foreach(DeliveryDetail::where('do_id',$dokey->id)->get() as $dodet){
                    $do_avcost = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$dodet->product_id)->where('tblpotrx.tgl','<=',$dodet->sales->trx_date)->avg('tblpotrxdet.price');
                    $price = $do_avcost * $dodet->qty;
                    $do_sum+=$price;
                }
                // Update Jurnal DO
                    // debet Persediaan Barang milik Customer
                        $jurnal_do_a = Jurnal::where('id_jurnal',$dokey->jurnal_id)->where('AccNo','2.1.3')->first();
                        $jurnal_do_a->amount = $do_sum;
                        $jurnal_do_a->update();
                    // credit Persediaan Barang digudang
                        $jurnal_do_b = Jurnal::where('id_jurnal',$dokey->jurnal_id)->where('AccNo','1.1.4.1.2')->first();
                        $jurnal_do_b->amount = $do_sum;
                        $jurnal_do_b->update();
            }
        }
    }
}
