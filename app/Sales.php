<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\SalesDet;
use App\DeliveryOrder;
use App\DeliveryDetail;
use App\PurchaseDetail;
use App\Jurnal;
use App\Log;
use App\TempSales;
use App\TempSalesDet;
use App\Customer;

class Sales extends Model
{
    protected $table ='tblproducttrx';
    protected $fillable = [
        'trx_date','creator','payment','ttl_harga','customer_id','jurnal_id','hpp_jurnal_id','ongkir','approve','approve_by','status'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public static function getOrder($start,$end,$param){
        $data = collect();
        if($param == "all"){
            $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id');
        }else{
            $order = Sales::join('tblproducttrxdet as x','tblproducttrx.id','=','x.trx_id')
            ->whereBetween('trx_date',[$start,$end]);
        }
        
        $ttl_count = $order->sum('x.qty');
        $order->orderBy('tblproducttrx.id')->select('tblproducttrx.*');
        
        $ttl_pemasukan = $order->sum('x.sub_ttl');
        $ttl_total = $order->sum('x.sub_ttl_pv');
        if($param == "all"){
            $ttl_trx = Sales::count('id');
        }else{
            $ttl_trx = Sales::whereBetween('trx_date',[$start,$end])->count('id');
        }
        

        $data->put('ttl_count',$ttl_count);
        $data->put('ttl_pemasukan',$ttl_pemasukan);
        $data->put('ttl_total',$ttl_total);
        $data->put('ttl_trx',$ttl_trx);
        $data->put('start',$start);
        $data->put('end',$end);
        $data->put('data',$order->get());
        return $data;
    }

    public static function getOrderPayment($start_trx,$end_trx,$start_pay,$end_pay,$customer,$param){
        if($param == "all"){
            $payment = SalesPayment::sum('payment_amount');
            $sales = Sales::where('approve',1);
            $data = collect();
            
            $ttl_trx = $sales->count('id');
    
            $ttl_harga = $sales->sum('ttl_harga');
            $ttl_ongkir = $sales->sum('ongkir');
        }elseif($param == null){
            $payment = SalesPayment::whereBetween('payment_date',[$start_pay,$end_pay])->sum('payment_amount');
            $sales = Sales::whereBetween('trx_date',[$start_trx,$end_trx])->where('approve',1);
            $data = collect();
    
            if($customer <> "all"){
                $sales->where('customer_id',$customer);
            }
            
            $ttl_trx = $sales->count('id');
    
            $ttl_harga = $sales->sum('ttl_harga');
            $ttl_ongkir = $sales->sum('ongkir');
        }
        
        $ttl_sales = $ttl_harga+$ttl_ongkir;

        $data->put('ttl_trx',$ttl_trx);
        $data->put('ttl_sales',$ttl_sales);
        $data->put('ttl_payment',$payment);
        $data->put('data',$sales->orderBy('id','desc')->get());

        return $data;
    }

    public static function checkDO($start,$end){
        if($start <> NULL && $end <> NULL){
            $sales = Sales::whereBetween('trx_date',[$start,$end])->where('approve',1)->get();
        }else{
            $sales = Sales::where('approve',1)->get();
        }
        
        $data = collect();
        foreach ($sales as $sale) {
            $collect = collect();
            $salesdet = SalesDet::where('trx_id',$sale->id)->get();
            $detcount = $salesdet->count();
            $count = 0;
            foreach($salesdet as $key){
                $count_do_product = DeliveryDetail::where('sales_id',$sale->id)->where('product_id',$key->prod_id)->sum('qty');
                if($key->qty == $count_do_product){
                    $count++;
                }
            }

            if($detcount == $count){
                $status = 1;
            }else{
                $status = 0;
            }
            $collect->put('sales_id',$sale->id);
            $collect->put('customer',$sale->customer->apname);
            $collect->put('ttl',$sale->ttl_harga+$sale->ongkir);
            $collect->put('status_do',$status);
            $data->push($collect);
        }
        return $data;
    }

    public static function checkSent($product,$trx){
        return DeliveryDetail::where('product_id',$product)->where('sales_id',$trx)->sum('qty');
    }

    public static function getBV($bulan,$tahun){
        return Sales::join('tblproducttrxdet','tblproducttrx.id','=','tblproducttrxdet.trx_id')->whereMonth('trx_date',$bulan)->whereYear('trx_date',$tahun)->sum('tblproducttrxdet.sub_ttl_pv');
    }

    public static function setJurnal($id,$user_id){
        $id_jurnal = 'SO.'.$id;

        $sales = Sales::where('id',$id)->first();
        $sales->approve = 1;
        $sales->approve_by = $user_id;
        $sales->jurnal_id = $id_jurnal;
        $sales->update();

        $jurnal_desc = "SO.".$sales->id;
        
        $total_transaksi = $sales->ttl_harga + $sales->ongkir;

        $modal = 0;
        foreach (SalesDet::where('trx_id',$id)->get() as $key) {
            $avcharga = PurchaseDetail::where('prod_id',$key->prod_id)->avg('price');
            $modal += ($key->qty * $avcharga);
        }

        // Jurnal 1
            //insert debet Piutang Konsumen Masukkan harga total - diskon
            Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'1.1.3.1','Debet',$user_id);
            //insert credit pendapatan retail (SALES)
            Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'4.1.1','Credit',$user_id);
        // Jurnal 2
            //insert debet COGS
            Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'5.1','Debet',$user_id);
            //insert Credit Persediaan Barang milik customer
            Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'1.1.4.1.2','Credit',$user_id);
    }

    public static function updateSales($id,$user_id){
        $temp_sales = TempSales::where('trx_id',$id)->first();
        $temp_sales_det = TempSalesDet::where('temp_id',$temp_sales->id)->get();
        
        $sales = Sales::where('id',$id)->first();

        // Update and tranfer to Sales Orginal
        $sales->trx_date = $temp_sales->trx_date;
        $sales->creator = $user_id;
        $sales->ttl_harga = $temp_sales->ttl_harga;
        $sales->ongkir = $temp_sales->ongkir;
        $sales->customer_id = $temp_sales->customer_id;

        $sales->update();

        // Delete Old Original Detail
        $saldet = SalesDet::where('trx_id',$id)->delete();

        // transfer new Detail
        $modal = 0;
        foreach ($temp_sales_det as $key) {
            $salesdet = new SalesDet(array(
                'trx_id' => $sales->id,
                'prod_id' => $key->prod_id,
                'qty' => $key->qty,
                'unit' => $key->unit,
                'creator' => $user_id,
                'price' => $key->price,
                'pv' => $key->pv,
                'sub_ttl' => $key->sub_ttl,
                'sub_ttl_pv' => $key->sub_ttl_pv,
            ));
            
            $salesdet->save();

            $avcharga = PurchaseDetail::where('prod_id',$key->prod_id)->avg('price');
            $modal += ($key->qty * $avcharga);
        }

        // Matikan status temp po
        $temp_sales->delete();

        // Update Jurnal
        $total_transaksi = $sales->ttl_harga+$sales->ongkir;
        if($sales->jurnal_id <> '0' || $sales->jurnal_id <> 0){
            $jurnal_a = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','1.1.3.1')->first();
            $jurnal_a->amount = $total_transaksi;
            $jurnal_a->date = $sales->trx_date;
            $jurnal_a->update();

            $jurnal_b = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','4.1.1')->first();
            $jurnal_b->amount = $total_transaksi;
            $jurnal_b->date = $sales->trx_date;
            $jurnal_b->update();

            $jurnal_c = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','5.1')->first();
            $jurnal_c->amount = $modal;
            $jurnal_c->date = $sales->trx_date;
            $jurnal_c->update();

            $jurnal_d = Jurnal::where('id_jurnal',$sales->jurnal_id)->where('AccNo','1.1.4.1.2')->first();
            $jurnal_d->amount = $modal;
            $jurnal_d->date = $sales->trx_date;
            $jurnal_d->update();
        }else{
            $id_jurnal = 'SO.'.$id;
            $sales->jurnal_id = $id_jurnal;
            $sales->approve = 1;
            $sales->approve_by = $user_id;
            $sales->update();

            $jurnal_desc = "SO.".$sales->id;

            // Jurnal 1
                //insert debet Piutang Konsumen Masukkan harga total - diskon
                Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'1.1.3.1','Debet',$user_id);
                //insert credit pendapatan retail (SALES)
                Jurnal::addJurnal($id_jurnal,$total_transaksi,$sales->trx_date,$jurnal_desc,'4.1.1','Credit',$user_id);
            // Jurnal 2
                //insert debet COGS
                Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'5.1','Debet',$user_id);
                //insert Credit Persediaan Barang milik customer
                Jurnal::addJurnal($id_jurnal,$modal,$sales->trx_date,$jurnal_desc,'1.1.4.1.2','Credit',$user_id);
        }
    }

    public static function report($start,$end){
        $data = collect();

        foreach(Customer::all() as $key){
            $temp = collect();
            $detail = Sales::join('tblproducttrxdet','tblproducttrx.id','=','tblproducttrxdet.trx_id');

            if($start <> NULL && $end <> NULL){
                $detail->whereBetween('tblproducttrx.trx_date',[$start,$end]);
            }

            $bv = $detail->where('tblproducttrx.customer_id',$key->id)->sum('tblproducttrxdet.sub_ttl_pv');
            $price = $detail->where('tblproducttrx.customer_id',$key->id)->sum('tblproducttrxdet.sub_ttl');

            $temp->put('customer',$key->apname);
            $temp->put('price',$price);
            $temp->put('bv',$bv);

            $data->push($temp);
        }
        return $data;
    }
}
