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
        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit')->where('tblpotrx.id',$trx)->get();

        $data = collect();
        foreach($purchase as $key){
            $key->supplier = $key->supplier()->first()->nama;
            $detail = collect($key);

            $qtyrec = ReceiveDet::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->sum('qty');
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname);
            $data->push($detail);
        }

        return $data;
    }
}
