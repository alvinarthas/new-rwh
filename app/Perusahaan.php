<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Purchase;
use App\PurchasePayment;
use App\ReturDetail;

class Perusahaan extends Model
{
    protected $table ='tblperusahaan';
    protected $fillable = [
        'nama','alamat','telp','creator','cp1', 'cp2', 'cp3'
    ];

    public $timestamps = true;

    public static function sisaHutang($param=null){
        $data = collect();
        $total = 0;
        foreach(Perusahaan::all() as $key){
            $supp = collect();
            $purchase = Purchase::where('supplier',$key->id)->sum('total_harga_dist');
            $paid = Purchase::join('tblpopayment','tblpopayment.trx_id','=','tblpotrx.id')->where('tblpotrx.supplier',$key->id)->sum('tblpopayment.payment_amount');
            $retur = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.status', 0)->where('tblretur.supplier', $key->id)->sum(DB::raw('tblreturdet.qty * tblreturdet.harga_dist'));

            $selisih = $purchase - $paid - $retur;
            $total+=$selisih;
            $supp->put('nama',$key->nama);
            $supp->put('id',$key->id);
            $supp->put('sisa',$selisih);

            $data->push($supp);
        }

        if($param){
            return $total;
        }else{
            return $data;
        }

    }

    public static function sisaHutangDetail($supplier){
        $data = collect();
        foreach(Purchase::where('supplier',$supplier)->get() as $key){
            $data_hutang = collect();
            $payment = PurchasePayment::where('trx_id',$key->id)->sum('payment_amount');

            $selisih = $key->total_harga_dist - $payment;

            if($selisih < 0 || $selisih > 0){
                $data_hutang->put('id',$key->id);
                $data_hutang->put('trx_id', $key->jurnal_id);
                $data_hutang->put('sisa',$selisih);
                $data_hutang->put('jenis',"PO");

                $data->push($data_hutang);
            }
        }

        foreach(ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.supplier',$supplier)->where('tblretur.status', 0)->select('tblretur.id', 'tblretur.id_jurnal', 'tblreturdet.harga_dist', 'tblreturdet.qty')->get() as $key){
            $dataretur = collect();

            $amount = $key->harga_dist * $key->qty;

            if($amount < 0 || $amount > 0){
                $dataretur->put('id',$key->id);
                $dataretur->put('trx_id',$key->id_jurnal);
                $dataretur->put('sisa', "-".$amount);
                $dataretur->put('jenis',"RB");

                $data->push($dataretur);
            }
        }

        return $data;
    }
}
