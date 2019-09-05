<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Coa;
use App\CoaGrup;
use App\Jurnal;

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
        $coa = Coa::where('AccNo',$request->coa)->first();
        $jurnals = Jurnal::where('AccNo',$request->coa)->whereBetween('date',[$start,$end])->get();
        $total_debet = $jurnal->where('AccPos','Debet')->sum('Amount');
        $total_credit = $jurnal->where('AccPos','Credit')->sum('Amount');

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
}
