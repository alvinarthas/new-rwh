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
    public function neraca(){
        return view('laporan.neraca.index');
    }

    // PERUBAHAN MODAL
    public function perubahanModal(Request $request){
        return view('laporan.perubahan_modal.index');
    }

    // PROFIT LOSS
    public function profitLoss(Request $request){
        if($request->ajax()){
            $start = $request->start;
            $end = $request->end;

            $nett_sales = Coa::nettSales($start,$end);
            $cogs = Coa::cogs($start,$end);
            $gross_profit = $nett_sales - $cogs;
            $biayaa = Coa::biaya($start,$end);
            $laba_rugi = Coa::laba_rugi($start,$end);
            $laba_rugi_bonus = Coa::laba_rugi_bonus($start,$end);
            $laba_bersih_non = Coa::laba_bersih_non($start,$end);

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
