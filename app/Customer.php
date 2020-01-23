<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $table ='tblcustomer';
    protected $fillable = [
        'cid','apname','apbp','apbd','apjt','apphone','apfax','apidc','apidcn','apidce','apemail','apadd','cicn','cicg','cilob','ciadd','cicty','cizip','cipro','ciweb','ciemail','cinpwp','ciphone','cifax','creator'
    ];
    public $timestamps = false;

    public static function sisaPiutang(){
        $data = collect();
        foreach(Customer::all() as $key){
            $supp = collect();
            $sales = Sales::where('customer_id',$key->id)->sum(DB::raw('ttl_harga+ongkir'));
            $paid = Sales::join('tblsopayment','tblsopayment.trx_id','=','tblproducttrx.id')->where('tblproducttrx.customer_id',$key->id)->sum('tblsopayment.payment_amount');

            $selisih = $sales - $paid;

            $supp->put('name',$key->apname);
            $supp->put('id',$key->id);
            $supp->put('sisa',$selisih);

            $data->push($supp);
        }
        return $data;
    }

    public static function sisaPiutangDetail($customer){
        $data = collect();
        foreach(Sales::where('customer_id',$customer)->get() as $key){
            $data_hutang = collect();
            $payment = SalesPayment::where('trx_id',$key->id)->sum('payment_amount');

            $selisih = ($key->ttl_harga+$key->ongkir) - $payment;

            if($selisih < 0 || $selisih > 0){
                $data_hutang->put('id',"SO.".$key->id);
                $data_hutang->put('sisa',$selisih);

                $data->push($data_hutang);
            }
        }

        return $data;
    }

}
