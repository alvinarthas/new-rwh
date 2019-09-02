<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PurchasePayment;
use App\PurchaseDetail;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    protected $table ='tblpotrx';
    protected $fillable = [
        'month','year','creator','supplier','notes','jurnal_id','tgl','approve','approve_by'
    ];

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }

    public static function getOrderPayment($bulan,$tahun){
        $data = collect();
        $data1 = collect();
        $purchase = Purchase::where('month',$bulan)->where('year',$tahun);
        $ttl_trx = $purchase->count('id');
        $ttl_order = 0;
        $ttl_pay = 0;
        foreach($purchase->get() as $key){
            $temp = collect();
            $order = PurchaseDetail::where('trx_id',$key->id)->sum(DB::raw('qty * price'));
            $pay_amount = PurchasePayment::where('trx_id',$key->id)->sum('payment_amount');
            $ttl_order+=$order;
            $ttl_pay+=$pay_amount;

            $temp->put('trx_id',$key->id);
            $temp->put('month',$key->month);
            $temp->put('year',$key->year);
            $temp->put('supplier',$key->supplier()->first()->nama);
            $temp->put('id',$key->id);
            $temp->put('status',$key->status);
            $temp->put('order',$order);
            $temp->put('paid',$pay_amount);
            $data1->push($temp);
        }
        $data->put('data',$data1);
        $data->put('ttl_order',$ttl_order);
        $data->put('ttl_pay',$ttl_pay);
        $data->put('ttl_trx',$ttl_trx);

        return $data;
    }
}
