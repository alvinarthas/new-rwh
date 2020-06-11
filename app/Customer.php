<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\ReturDetail;
use App\Employee;
use App\Sales;
use App\SalesPayment;
use App\Saldo;

class Customer extends Model
{
    protected $table ='tblcustomer';
    protected $fillable = [
        'cid','apname','apbp','apbd','apjt','apphone','apfax','apbirthdate','apidc','apidcn','apidce','apemail','apadd','cicn','cicg','cilob','ciadd','cicty','cizip','cipro','ciweb','ciemail','cinpwp','ciphone','cifax','creator', 'cust_type'
    ];
    public $timestamps = false;

    public static function sisaPiutang($param=null){
        $data = collect();
        $total = 0;
        foreach(Customer::all() as $key){
            $supp = collect();
            $sales = Sales::where('customer_id',$key->id)->where('approve',1)->sum(DB::raw('ttl_harga+ongkir'));
            $paid = Sales::join('tblsopayment','tblsopayment.trx_id','=','tblproducttrx.id')->where('tblproducttrx.customer_id',$key->id)->where('tblproducttrx.approve',1)->sum('tblsopayment.payment_amount');

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
        foreach(Sales::where('customer_id',$customer)->where('approve',1)->get() as $key){
            $data_hutang = collect();
            $payment = SalesPayment::where('trx_id',$key->id)->sum('payment_amount');

            $selisih = ($key->ttl_harga+$key->ongkir) - $payment;

            if($selisih < 0 || $selisih > 0){
                $data_hutang->put('id',$key->id);
                $data_hutang->put('trx_id', $key->jurnal_id);
                $data_hutang->put('sisa',$selisih);
                $data_hutang->put('jenis', "SO");

                $data->push($data_hutang);
            }
        }

        foreach(ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.customer',$customer)->where('tblretur.status', 1)->select('tblretur.id', 'tblretur.id_jurnal', 'tblreturdet.harga', 'tblreturdet.qty')->get() as $key){
            $dataretur = collect();

            $amount = $key->harga * $key->qty;

            if($amount < 0 || $amount > 0){
                $dataretur->put('id',$key->id);
                $dataretur->put('trx_id', $key->id_jurnal);
                $dataretur->put('sisa', "-".$amount);
                $dataretur->put('jenis', "RJ");

                $data->push($dataretur);
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

    public static function getDeposit(){
        $data = collect();
        $customers = Customer::all();
        foreach($customers as $customer){
            $deposit = collect();
            $saldo = Saldo::getSaldo($customer->id);

            if($saldo > 0){
                $deposit->put('name',$customer->apname);
                $deposit->put('id',$customer->id);
                $deposit->put('saldo',$saldo);
                $data->push($deposit);
            }
        }

        $data = $data->sortByDesc('saldo');

        return $data;
    }

}
