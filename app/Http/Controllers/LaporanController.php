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
    public function neraca_awal(){
        $dataaktvia = collect();
        $datapasiva = collect();
        $coaaktiva = Coa::where('AccNo','LIKE','1-%')->where('StatusAccount','Grup')->orderBy('AccNo','asc')->get();
        $coapasiva = Coa::where('AccNo','LIKE','2-%')->where('StatusAccount','Grup')->orderBy('AccNo','asc')->get();
        $sum_aktiva = 0;
        $sum_pasiva = 0;

        foreach ($coaaktiva as $key) {
            $acoa = Coa::where('AccParent',$key->AccNo)->where('StatusAccount','Detail')->orderBy('AccNo','asc');
            $sum = $acoa->sum('SaldoAwal');
            $sum_aktiva+=$sum;    
            $datacoa = collect();
            $datacoa->put('grup',$key);
            $datacoa->put('sum',$sum);
            $datacoa->put('data',$acoa->get());

            $dataaktvia->push($datacoa);
        }

        foreach ($coapasiva as $key2) {
            $pcoa = Coa::where('AccParent',$key2->AccNo)->where('StatusAccount','Detail')->orderBy('AccNo','asc');
            $sum2 = $pcoa->sum('SaldoAwal');
            $sum_pasiva+=$sum2;    
            $datacoap = collect();
            $datacoap->put('grup',$key2);
            $datacoap->put('sum',$sum2);
            $datacoap->put('data',$pcoa->get());

            $datapasiva->push($datacoap);
        }

        return view('laporan.balance_sheet.neraca_saldo_awal',compact('dataaktvia','sum_aktiva','sum_pasiva','datapasiva'));
    }

    public function laporan_neraca(){
        return view('laporan.balance_sheet.index_laporan_neraca');
    }

    public function laporan_neraca_view(Request $request){
        $start = $request->start_date;
        $end = $request->end_date;

        $dataaktiva = collect();
        $datapasiva = collect();
        $coaaktiva = Coa::where('AccNo','LIKE','1-%')->where('StatusAccount','Grup')->orderBy('AccNo','asc')->get();
        $coapasiva = Coa::where('AccNo','LIKE','2-%')->where('StatusAccount','Grup')->orderBy('AccNo','asc')->get();
        $sum_aktiva = 0;
        $sum_pasiva = 0;

        // AKTIVA
        foreach ($coaaktiva as $key) {
            $sum_grup_aktiva = 0;
            $datagrup = collect();
            $dataakun = collect();

            $acoa = Coa::where('AccParent',$key->AccNo)->where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
            
            foreach ($acoa as $key2) {
                $sum_debet = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Debet')->whereBetween('date',[$start,$end])->sum('amount');
                $sum_credit = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Credit')->whereBetween('date',[$start,$end])->sum('amount');

                $total = $sum_debet - $sum_credit;
                $sum_grup_aktiva+=$total;

                $data = collect();
                $data->put('data',$key2);
                $data->put('total',$total);

                $dataakun->push($data);
            }
            $sum_aktiva+=$sum_grup_aktiva;

            $datagrup->put('grup',$key);
            $datagrup->put('sum',$sum_grup_aktiva);
            $datagrup->put('data',$dataakun);

            $dataaktiva->push($datagrup);
        }

        // PASIVA
        foreach ($coapasiva as $keypas) {
            $sum_grup_pasiva = 0;
            $datagruppas = collect();
            $dataakunpas = collect();

            $acoapas = Coa::where('AccParent',$keypas->AccNo)->where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
            
            foreach ($acoapas as $key2pas) {
                $sum_debet_pas = Jurnal::where('AccNo',$key2pas->AccNo)->where('AccPos','Debet')->whereBetween('date',[$start,$end])->sum('amount');
                $sum_credit_pas = Jurnal::where('AccNo',$key2pas->AccNo)->where('AccPos','Credit')->whereBetween('date',[$start,$end])->sum('amount');

                $total_pas = $sum_debet_pas - $sum_credit_pas;
                $sum_grup_pasiva+=$total_pas;

                $data = collect();
                $data->put('data',$key2);
                $data->put('total',$total);

                $dataakunpas->push($data);
            }
            $sum_pasiva+=$sum_grup_pasiva;

            $datagruppas->put('grup',$keypas);
            $datagruppas->put('sum',$sum_grup_pasiva);
            $datagruppas->put('data',$dataakunpas);

            $datapasiva->push($datagruppas);
        }

        // RESPONSE
        if ($request->ajax()) {
            return response()->json(view('laporan.balance_sheet.view_laporan_neraca',compact('dataaktiva','sum_aktiva','datapasiva','sum_pasiva'))->render());
        }
        
    }

    //  GENERAL LEDGER
    public function index_gl(){
        $coas = Coa::where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
        return view('laporan.general_ledger.index',compact('coas'));
    }

    public function view_gl(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $coa = Coa::where('AccNo',$request->coa)->first();
        $jurnals = Jurnal::where('AccNo',$request->coa)->whereBetween('date',[$start_date,$end_date])->get();
        $total_debet = $jurnals->where('AccPos','Debet')->sum('Amount');
        $total_credit = $jurnals->where('AccPos','Credit')->sum('Amount');

        if($coa->SaldoNormal == 'Db'){
            $current = $coa->SaldoAwal+$total_debet-$total_credit;
        }elseif($coa->SaldoNomral == 'Cr'){
            $current = $coa->SaldoAwal-$total_debet+$total_credit;
        }

        // RESPONSE
        if ($request->ajax()) {
            return response()->json(view('laporan.general_ledger.view',compact('coa','jurnals','total_debet','total_credit','current'))->render());
        }
    }

    public function view_glJurnal(Request $request){
        $jurnal_id = $request->jurnal_id;

        $jurnals = Jurnal::where('id_jurnal',$jurnal_id)->get();

        // RESPONSE
        if ($request->ajax()) {
            return response()->json(view('laporan.general_ledger.viewjurnal',compact('jurnals'))->render());
        }
    }

    // PERUBAHAN MODAL
    public function perubahanModal(Request $request){
        if($request->ajax()){
            $start = $request->start_date;
            $end = $request->end_date;

            $total_expense_pribadi = Jurnal::totalExpensePribadi($start,$end);
            $setoran_modal = Jurnal::setoranModal($start,$end);
            $profit_loss=profitLoss($start,$end);
            $saldoawal = Coa::where('AccNo','3-100001')->first()->SaldoAwal;
            $modal_akhir = ModalAkhir($start,$end);

            return response()->json(view('laporan.perubahan_modal.view',compact('end','modal_akhir','profit_loss','setoran_modal','total_expense_pribadi','saldoawal'))->render());
        }else{
            return view('laporan.perubahan_modal.index');
        }
        
    }

    // PROFIT LOSS
    public function profitLoss(Request $request){
        if($request->ajax()){
            $start = $request->start_date;
            $end = $request->end_date;

            $datalabarugi = Jurnal::dataLabaRugi($start);
            $total_pendapatan = (Jurnal::totalPendapatan($start,$end))-(Jurnal::totalPotongan($start,$end));
            $total_cogs = Jurnal::totalCogs($start,$end);
            $total_pendapatan_lain=(Jurnal::totalPendapatLain($start,$end))-(Jurnal::totalPotonganPendapatan($start,$end));
            $profit_loss=Jurnal::profitLoss($start,$end);
            $total_expense = Jurnal::totalExpense($start,$end);

            // Sales Revenue
            $sales_revenues = Jurnal::salesRevenue($start,$end);
            // COGS
            $cogss = Jurnal::dataCogs($start,$end);
            // Expenses
            $expensess = Jurnal::dataExpenses($start,$end);
            // Pendapatan & Bebab Lainnya
            $pendapatans = Jurnal::dataPendapatandll($start,$end);
            // LABA / RUGI

            return response()->json(view('laporan.profit_loss.view',compact('start','end','total_pendapatan','total_cogs','total_pendapatan_lain','profit_loss','total_Expense','sales_revenues','cogss','expensess','pendapatans','total_expense','datalabarugi'))->render());
        }else{
            return view('laporan.profit_loss.index');
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
