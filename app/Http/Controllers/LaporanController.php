<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Coa;
use App\CoaGrup;
use App\Jurnal;
use App\Purchase;
use App\Sales;
use App\Customer;
use App\Perusahaan;

class LaporanController extends Controller
{
    // BALANCE SHEET
    public function neraca(Request $request){
        if($request->ajax()){
            $date = $request->date;
            $start = '2019-10-01';

            $assets = Coa::neraca($date,1);
            $hutangs = Coa::neraca($date,2);
            $modal = Coa::modalAkhir($start,$date);

            return response()->json(view('laporan.neraca.view',compact('date','assets','hutangs','modal'))->render());
        }else{
            return view('laporan.neraca.index');
        }
        
    }

    // PERUBAHAN MODAL
    public function perubahanModal(Request $request){
        if($request->ajax()){
            $start = $request->start_date;
            $end = $request->end_date;

            $modal_awal = Coa::modalAwal($start,$end);
            $set_modal = Coa::setoranModal($start,$end);
            $prive = Coa::pengeluaranPribadi($start,$end);
            $nett_profit = Coa::nettProfit($start,$end);
            $perubahan_modal = $set_modal-$prive+$nett_profit;
            $modal_akhir = $modal_awal+$perubahan_modal;

            return response()->json(view('laporan.perubahan_modal.view',compact('start','end','modal_awal','set_modal','prive','nett_profit','modal_akhir','laba_operasional','perubahan_modal'))->render());
        }else{
            return view('laporan.perubahan_modal.index');
        }
        
    }

    // PROFIT LOSS
    public function profitLoss(Request $request){
        if($request->ajax()){
            $start = $request->start_date;
            $end = $request->end_date;

            $nett_sales = Coa::nettSales($start,$end);
            $cogs = Coa::cogs($start,$end);
            $gross_profit = $nett_sales - $cogs;
            $biayaa = Coa::biaya($start,$end);
            $laba_operasional = $gross_profit - $biayaa['amount'];
            $laba_bersih_non = Coa::laba_bersih_non($start,$end);
            $laba_rugi = Coa::laba_rugi($start,$end);

            $nett_profit = $laba_operasional+$laba_bersih_non['amount']+$laba_rugi[0]['amount']+$laba_rugi[1]['amount'];

            return response()->json(view('laporan.profit_loss.view',compact('start','end','nett_sales','cogs','gross_profit','biayaa','laba_bersih_non','laba_operasional','laba_rugi','nett_profit'))->render());

        }else{
            return view('laporan.profit_loss.index');
        }
    }

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
}
