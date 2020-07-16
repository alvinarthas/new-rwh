<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Purchase;
use App\Product;

class ReceiveDet extends Model
{
    protected $table ='tblreceivedet';
    protected $fillable = [
        'trx_id','prod_id','qty','expired_date', 'gudang_id','creator', 'receive_date', 'id_jurnal', 'purchasedetail_id',
    ];

    public function prod(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function gudang(){
        return $this->belongsTo('App\Gudang');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public function price(){
        return $this->belongsTo('App\PurchaseDetail', 'purchasedetail_id', 'id');
    }

    public static function listReceive($start,$end){
        // $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->whereBetween('tblpotrx.month',[$bulan_start,$bulan_end])->whereBetween('tblpotrx.year',[$tahun_start,$tahun_end])->where('tblpotrx.approve',1)->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit')->get();
        $purchase = Purchase::whereBetween('tblpotrx.tgl',[$start,$end])->where('tblpotrx.approve',1)->get();

        $data = collect();
        foreach($purchase as $pur){
            $purdet = PurchaseDetail::where('trx_id', $pur->id)->groupBy('prod_id')->get();

            foreach($purdet as $key){
                $key->qty = PurchaseDetail::where('trx_id', $pur->id)->where('prod_id', $key->prod_id)->sum('qty');
                $detail = collect($key);

                $rp_id = "";
                $receive = ReceiveDet::where('trx_id', $pur->id)->where('prod_id', $key->prod_id)->select('id_jurnal')->get();
                foreach($receive as $r){
                    $rp_id .= $r->id_jurnal." ";
                }

                $qtyrec = ReceiveDet::where('trx_id',$pur->id)->where('prod_id',$key->prod_id)->sum('qty');
                $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

                $detail->put('rp_id', $rp_id);
                $detail->put('supplier', $pur->supplier()->first()->nama);
                $detail->put('qtyrec',$qtyrec);
                $detail->put('prod_name',$prodname);
                $data->push($detail);
            }
        }

        return $data;
    }

    public static function listReceiveAll(){
        // $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->where('tblpotrx.approve',1)->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit')->get();
        $purchase = Purchase::where('tblpotrx.approve',1)->get();

        $data = collect();
        foreach($purchase as $pur){
            $purdet = PurchaseDetail::where('trx_id', $pur->id)->groupBy('prod_id')->get();

            foreach($purdet as $key){
                $key->qty = PurchaseDetail::where('trx_id', $pur->id)->where('prod_id', $key->prod_id)->sum('qty');
                $detail = collect($key);

                $rp_id = "";
                $receive = ReceiveDet::where('trx_id', $pur->id)->where('prod_id', $key->prod_id)->select('id_jurnal')->get();
                foreach($receive as $r){
                    $rp_id .= $r->id_jurnal." ";
                }

                $qtyrec = ReceiveDet::where('trx_id',$pur->id)->where('prod_id',$key->prod_id)->sum('qty');
                $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

                $detail->put('rp_id', $rp_id);
                $detail->put('supplier', $pur->supplier()->first()->nama);
                $detail->put('qtyrec',$qtyrec);
                $detail->put('prod_name',$prodname);
                $data->push($detail);
            }
        }

        return $data;
    }

    public static function detailPurchase($trx){
        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit','tblpotrxdet.id AS purchasedetail_id', 'tblpotrxdet.price')->where('tblpotrx.id',$trx)->get();

        $data = collect();
        foreach($purchase as $key){
            $key->supplier = $key->supplier()->first()->nama;
            $detail = collect($key);

            $qtyrec = ReceiveDet::where('purchasedetail_id',$key->purchasedetail_id)->sum('qty');
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname." - (Rp ".number_format($key->price, 2, ",", ".").")");
            $data->push($detail);
        }

        return $data;
    }

    public static function recycleRP($id,$purchasedetail_id=null){
        if ($purchasedetail_id){
            ReceiveDet::where('purchasedetail_id',$purchasedetail_id)->delete();
        }

        // Refesh and Loop
        foreach(ReceiveDet::where('trx_id', $id)->select('id_jurnal')->groupBy('id_jurnal')->get() as $key){
            $price = 0;
            foreach (ReceiveDet::where('id_jurnal', $key->id_jurnal)->get() as $data) {
                $pricedet = PurchaseDetail::where('id', $data->purchasedetail_id)->first();
                if(!$pricedet){
                    $pricedet = PurchaseDetail::where('prod_id',$data->prod_id)->where('trx_id',$data->trx_id)->first();
                }
                $price += $pricedet->price * $data->qty;
            }
            $debet = Jurnal::where('id_jurnal',$key->id_jurnal)->where('AccPos', 'Debet')->first();
            $debet->amount = $price;
            $debet->update();

            $credit = Jurnal::where('id_jurnal',$key->id_jurnal)->where('AccPos', 'Credit')->first();
            $credit->amount = $price;
            $credit->update();
        }
    }
}
