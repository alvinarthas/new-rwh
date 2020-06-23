<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\ReturDetail;
use App\ReturPayment;

class Retur extends Model
{
    protected $table ='tblretur';
    protected $fillable = [
        'tgl','customer', 'supplier', 'status', 'creator','id_jurnal','status_bayar'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer','customer');
    }

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public static function getTotal($product, $status){
        $total = 0;
        if($status == null){
            foreach(ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblreturdet.prod_id',$product)->get() as $detail){
                if($detail->status == 0){
                    $total-=$detail->qty;
                }else{
                    $total+=$detail->qty;
                }
            }
        }else{
            foreach(ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('status', $status)->where('tblreturdet.prod_id',$product)->get() as $detail){
                $total+=$detail->qty;
            }
        }

        return $total;
    }

    public static function getReturPay($jenis){
        $data = collect();
        foreach(Retur::where('status', $jenis)->get() as $key){
            $retur = collect();
            $detail = ReturDetail::where('trx_id', $key->id)->get();
            $total = 0;

            foreach($detail as $det){
                if($jenis == 0){
                    $total += $det->harga_dist * $det->qty;
                }elseif($jenis == 1){
                    $total += $det->harga * $det->qty;
                }
            }
            $returpay = ReturPayment::where('trx_id',$key->id)->sum('amount');

            $retur->put('id',$key->id);
            $retur->put('id_jurnal', $key->id_jurnal);
            $retur->put('tgl',$key->tgl);
            if($jenis == 0){
                $retur->put('supplier', $key->supplier()->first()->nama);
            }elseif($jenis == 1){
                $retur->put('customer', $key->customer()->first()->apname);
            }

            $retur->put('total', $total);
            $retur->put('paid', $returpay);
            $data->push($retur);
        }

        return $data;
    }

    public static function getReturStock($jenis){
        $data = collect();
        foreach(Retur::where('status', $jenis)->get() as $key){
            $retur = collect();
            $detail = ReturDetail::where('trx_id', $key->id)->get();
            $statuscount = 0;

            foreach($detail as $det){
                $qtynota = ReturDetail::where('trx_id',$key->id)->where('prod_id', $det->prod_id)->sum('qty');
                $qtystock = ReturStock::where('trx_id',$key->id)->where('prod_id', $det->prod_id)->sum('qty');
                if($qtynota != $qtystock){
                    $statuscount++;
                }
            }

            if($statuscount == 0){
                $status = "Selesai";
            }else{
                $status = "Belum Selesai";
            }

            $retur->put('id',$key->id);
            $retur->put('id_jurnal', $key->id_jurnal);
            $retur->put('tgl',$key->tgl);
            if($jenis == 0){
                $retur->put('supplier', $key->supplier()->first()->nama);
                $retur->put('po_id', $key->source_id);
            }elseif($jenis == 1){
                $retur->put('customer', $key->customer()->first()->apname);
                $retur->put('so_id', $key->source_id);
            }
            $retur->put('status', $status);
            $data->push($retur);
        }

        return $data;
    }
}
