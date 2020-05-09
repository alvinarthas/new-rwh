<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class Customer extends Model
{
    protected $table ='tblcustomer';
    protected $fillable = [
        'cid','apname','apbp','apbd','apjt','apphone','apfax','apbirthdate','apidc','apidcn','apidce','apemail','apadd','cicn','cicg','cilob','ciadd','cicty','cizip','cipro','ciweb','ciemail','cinpwp','ciphone','cifax','creator'
    ];
    public $timestamps = false;

    public static function sisaPiutang($param=null){
        $data = collect();
        $total = 0;
        foreach(Customer::all() as $key){
            $supp = collect();
            $sales = Sales::where('customer_id',$key->id)->sum(DB::raw('ttl_harga+ongkir'));
            $paid = Sales::join('tblsopayment','tblsopayment.trx_id','=','tblproducttrx.id')->where('tblproducttrx.customer_id',$key->id)->sum('tblsopayment.payment_amount');

            $selisih = $sales - $paid;
            $total+=$selisih;
            $supp->put('name',$key->apname);
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

    public static function sisaPiutangDetail($customer){
        $data = collect();
        foreach(Sales::where('customer_id',$customer)->get() as $key){
            $data_hutang = collect();
            $payment = SalesPayment::where('trx_id',$key->id)->sum('payment_amount');

            $selisih = ($key->ttl_harga+$key->ongkir) - $payment;

            if($selisih < 0 || $selisih > 0){
                $data_hutang->put('id',$key->id);
                $data_hutang->put('sisa',$selisih);

                $data->push($data_hutang);
            }
        }

        return $data;
    }

    public static function getBirthday(){
        Carbon::setLocale('id');
        $getTime = date('Y-m-d', strtotime(Carbon::now()));
        // $getTime = date('Y-m-d', strtotime("1994-10-18"));
        $data = array();

        $customer = Customer::where('apbirthdate', $getTime)->get();

        if(!empty($customer)){
            foreach($customer as $c){
                $array = array(
                    'name'    => $c->apname,
                    'status'  => "Customer",
                    'tanggal' => $c->apbirthdate,
                );
                array_push($data, $array);
            }
        }

        $employee = Employee::where('tgl_lhr', $getTime)->get();

        if(!empty($employee)){
            foreach($employee as $e){
                $array = array(
                    'name'    => $e->name,
                    'status'  => "Pegawai",
                    'tanggal' => $e->tgl_lhr,
                );
                array_push($data, $array);
            }
        }

        return $data;
    }

}
