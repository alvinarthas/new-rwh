<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Coa;
use App\CoaGrup;
use App\Jurnal;
use App\Purchase;
use App\PurchaseDetail;
use App\Sales;
use App\Customer;
use App\Perusahaan;
use App\Product;

class LaporanController extends Controller
{
    // General Ledger
    public function generalLedger(Request $request){
        if($request->ajax()){
            $coa = Coa::where('AccNo',$request->coa)->first();
            $jurnals = Jurnal::generalLedger($request->start_date,$request->end_date,$request->coa);
            return response()->json(view('laporan.general_ledger.view',compact('jurnals','coa'))->render());
        }else{
            $coas = Coa::where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
            return view('laporan.general_ledger.index',compact('coas'));
        }
    }

    // Laporan Sales
    public function salesReport(Request $request){
        if($request->ajax()){
            $start = $request->start;
            $end = $request->end;

            $report = Sales::report($start,$end);
            $count = Customer::count();
            return view('laporan.sales.show',compact('report','start','end','count'));
        }else{
            return view('laporan.sales.index');
        }
    }

    // Laporan Purchase
    public function purchaseReport(Request $request){
        if($request->ajax()){
            $start = $request->start;
            $end = $request->end;

            $report = Purchase::report($start,$end);
            $count = Perusahaan::count();
            return view('laporan.purchase.show',compact('report','start','end','count'));
        }else{
            return view('laporan.purchase.index');
        }
    }

    // Laporan Keuangan
    public function financeReport(Request $request){
        // Contain of Profit Loss, General Ledger, Perubahan Modal, Neraca

        if($request->ajax()){
            $start = $request->start_date;
            $end = $request->end_date;

            // Profit Loss
                $nett_sales = Coa::nettSales($start,$end);
                $cogs = Coa::cogs($start,$end);
                $gross_profit = $nett_sales - $cogs;
                $biayaa = Coa::biaya($start,$end);
                $laba_operasional = $gross_profit - $biayaa['amount'];
                $laba_bersih_non = Coa::laba_bersih_non($start,$end);
                $laba_rugi = Coa::laba_rugi($start,$end);

                $nett_profit = $laba_operasional+$laba_bersih_non['amount']+$laba_rugi[0]['amount']+$laba_rugi[1]['amount'];
            // Laporan Modal
                $modal_awal = Coa::modalAwal($start,$end);
                $set_modal = Coa::setoranModal($start,$end);
                $prive = Coa::pengeluaranPribadi($start,$end);
                $perubahan_modal = $set_modal-$prive+$nett_profit;
                $modal_akhir = $modal_awal+$perubahan_modal;
            // Neraca
                $date = $request->end_date;
                $start2 = '2019-10-01';

                $assets = Coa::neraca($date,1);
                $hutangs = Coa::neraca($date,2);
                // $modal = Coa::modalAkhir($start2,$date);

                return response()->json(view('laporan.keuangan.view',compact('start','end','modal_awal','set_modal','prive','nett_profit','modal_akhir','laba_operasional','perubahan_modal','nett_sales','cogs','gross_profit','biayaa','laba_bersih_non','laba_operasional','laba_rugi','assets','hutangs'))->render());
        }else{
            return view('laporan.keuangan.index');
        }
    }

    // Laporan Hutang Supplier
    public function sisaHutangReport(Request $request){
        if($request->ajax()){
            $supplier = Perusahaan::where('id',$request->supplier)->first();
            $detail = Perusahaan::sisaHutangDetail($request->supplier);

            return response()->json(view('laporan.sisahutang.view',compact('supplier','detail'))->render());
        }else{
            $suppliers = Perusahaan::sisaHutang();
            return view('laporan.sisahutang.index',compact('suppliers'));
        }
    }

    // Laporan Piutang Customer
    public function sisaPiutangReport(Request $request){
        if($request->ajax()){
            $customer = Customer::where('id',$request->customer)->first();
            $detail = Customer::sisaPiutangDetail($request->customer);
            return response()->json(view('laporan.sisapiutang.view',compact('customer','detail'))->render());
        }else{
            $customers = Customer::sisaPiutang();
            return view('laporan.sisapiutang.index',compact('customers'));
        }
    }

    public function kendaliBarang(){
        $products = Product::all();

        return view('laporan.kendali.index',compact('products'));
    }

    public function checkGrossProfit(Request $request){
        if($request->ajax()){
            $jenis = $request->jenis;
            $start = $request->start;
            $end = $request->end;

            $sum = 0;
            $data = collect();
            // Based on Product
            if($jenis == 1){
                foreach(Product::all() as $item){
                    $total = 0;
                    $sales = Sales::join('tblproducttrxdet','tblproducttrxdet.trx_id','=','tblproducttrx.id')->where('prod_id',$item->prod_id);

                    if($start <> NULL && $end <> NULL){
                        $sales = $sales->whereBetween('tblproducttrx.trx_date',[$start,$end]);
                    }

                    $sales = $sales->select('tblproducttrxdet.*','tblproducttrx.trx_date')->get();
                    
                    $collects = collect();
                    foreach($sales as $item2){
                        // AVG COST
                        $avg_cost = PurchaseDetail::avgCost($item->prod_id,$item2->trx_date);
                        // SELISIH
                        $selisih = $item2->price - $avg_cost;
                        // Result
                        $value = $selisih*$item2->qty;
                        $total+=$value;
                    }
                    $sum+=$total;
                    $collects->put('id',$item->prod_id);
                    $collects->put('name',$item->name);
                    $collects->put('value',$total);

                    $data->push($collects);
                }
                $count = Product::count();
            // Based on Customer
            }elseif($jenis == 2){
                foreach(Customer::all() as $item){
                    $total = 0;
                    $collects = collect();
                    $sales = Sales::join('tblproducttrxdet','tblproducttrxdet.trx_id','=','tblproducttrx.id')->where('customer_id',$item->id);

                    if($start <> NULL && $end <> NULL){
                        $sales = $sales->whereBetween('tblproducttrx.trx_date',[$start,$end]);
                    }

                    $sales = $sales->select('tblproducttrxdet.*','tblproducttrx.trx_date')->get();

                    foreach($sales as $item2){
                        // AVG COST
                        $avg_cost = PurchaseDetail::avgCost($item2->prod_id,$item2->trx_date);
                        // SELISIH
                        $selisih = $item2->price - $avg_cost;
                        // Result
                        $value = $selisih*$item2->qty;
                        $total+=$value;
                    }
                    $sum+=$total;
                    $collects->put('id',$item->id);
                    $collects->put('name',$item->apname);
                    $collects->put('value',$total);

                    $data->push($collects);
                }
                $count = Customer::count();
            }
            return view('laporan.checkGrossProfit.show',compact('data','start','end','sum','count','jenis'));
        }else{
            return view('laporan.checkGrossProfit.index');
        }
    }
}
