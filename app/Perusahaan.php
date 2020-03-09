<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Purchase;
use App\PurchasePayment;

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

            $selisih = $purchase - $paid;
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
                $data_hutang->put('sisa',$selisih);

                $data->push($data_hutang);
            }
        }

        return $data;
    }
}
