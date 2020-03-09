<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Jurnal;

class Coa extends Model
{
    protected $table ='tblcoa';
    protected $fillable = [
        'AccNo', 'AccName', 'SaldoNormal', 'StatusAccount', 'SaldoAwal', 'company_id', 'AccParent'
    ];

    public static function checkChild($id){
        return Coa::where('AccParent',$id)->where('AccNo','NOT LIKE',$id)->count();
    }

    public static function getChild($id){
        return Coa::where('AccParent',$id)->where('AccNo','NOT LIKE',$id)->get();
    }

    // NETT PROFIT
    public static function nettSales($start,$end){
        $sales_debet = Jurnal::where('AccNo','4.1.1')->where('AccPos','Debet');
        $sales_credit = Jurnal::where('AccNo','4.1.1')->where('AccPos','Credit');
        if($start <> NULL || $end <> NULL){
            $sales_debet->whereBetween('date',[$start,$end]);
            $sales_credit->whereBetween('date',[$start,$end]);
        }
        $total = $sales_credit->sum('Amount')-$sales_debet->sum('Amount');
        return $total;
    }

    public static function cogs($start,$end){
        $sales = Jurnal::where('AccNo','5.1');
        if($start <> NULL || $end <> NULL){
            $sales->whereBetween('date',[$start,$end]);
        }
        return $sales->sum('Amount');
    }

    public static function biaya($start,$end){
        $parent = Coa::where('AccNo','6')->first();
        $data_parent = collect();
        $subparent = collect();
        $parent_sum = 0;

        foreach(Coa::where('AccNo','LIKE','6.1')->orwhere('AccNo','LIKE','6.2')->orwhere('AccNo','LIKE','7.1')->orwhere('AccNo','LIKE','7.2')->get() as $key){
            $subsub = collect();
            $sub_coa_collect = collect();
            $sub_sum = 0;

            foreach (Coa::where('AccParent',$key->AccNo)->get() as $key2) {

                $coa_collect = collect();
                $sales_debet = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Debet');
                $sales_credit = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Credit');
                if($start <> NULL || $end <> NULL){
                    $sales_debet->whereBetween('date',[$start,$end]);
                    $sales_credit->whereBetween('date',[$start,$end]);
                }

                $coasum = $sales_credit->sum('Amount')-$sales_debet->sum('Amount');
                $sub_sum+=$coasum;

                $coa_collect->put('name',$key2->AccName);
                $coa_collect->put('amount',$coasum);

                $sub_coa_collect->push($coa_collect);
            }

            $parent_sum+=$sub_sum;

            $subsub->put('name',$key->AccName);
            $subsub->put('amount',$sub_sum);
            $subsub->put('data',$sub_coa_collect);
            $subparent->push($subsub);
        }
        $data_parent->put('name',$parent->AccName);
        $data_parent->put('amount',$parent_sum);
        $data_parent->put('data',$subparent);

        return $data_parent;
    }

    public static function laba_rugi($start,$end){
        $subparent = collect();

        foreach (Coa::where('AccNo','LIKE','7.3')->orwhere('AccNo','LIKE','7.4')->get() as $key) {
            $subsub = collect();
            $sales_debet = Jurnal::where('AccNo',$key->AccNo)->where('AccPos','Debet');
            $sales_credit = Jurnal::where('AccNo',$key->AccNo)->where('AccPos','Credit');

            if($start <> NULL || $end <> NULL){
                $sales_debet->whereBetween('date',[$start,$end]);
                $sales_credit->whereBetween('date',[$start,$end]);
            }

            $coasum = $sales_credit->sum('Amount')-$sales_debet->sum('Amount');

            $subsub->put('name',$key->AccName);
            $subsub->put('amount',$coasum);
            $subparent->push($subsub);
        }

        return $subparent;
    }

    public static function laba_bersih_non($start,$end){
        $data_parent = collect();
        $subparent = collect();
        $parent_sum = 0;

        foreach(Coa::where('AccNo','LIKE','4.3')->orwhere('AccNo','LIKE','4.4')->orwhere('AccNo','LIKE','6.3')->orwhere('AccNo','LIKE','6.4')->get() as $key){
            $subsub = collect();
            $sub_coa_collect = collect();
            $sub_sum = 0;
            foreach (Coa::where('AccParent',$key->AccNo)->get() as $key2) {

                $coa_collect = collect();
                $sales_debet = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Debet');
                $sales_credit = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Credit');
                if($start <> NULL || $end <> NULL){
                    $sales_debet->whereBetween('date',[$start,$end]);
                    $sales_credit->whereBetween('date',[$start,$end]);
                }

                $coasum = $sales_credit->sum('Amount')-$sales_debet->sum('Amount');

                $sub_sum+=$coasum;

                $coa_collect->put('name',$key2->AccName);
                $coa_collect->put('no',$key2->AccNo);
                $coa_collect->put('amount',$coasum);

                $sub_coa_collect->push($coa_collect);
            }

            $parent_sum+=$sub_sum;

            $subsub->put('name',$key->AccName);
            $subsub->put('no',$key->AccNo);
            $subsub->put('amount',$sub_sum);
            $subsub->put('data',$sub_coa_collect);
            $subparent->push($subsub);
        }
        $data_parent->put('name',"Laba/Rugi Bersih Non Operasional");
        $data_parent->put('amount',$parent_sum);
        $data_parent->put('data',$subparent);

        return $data_parent;
    }

    // Perubahan Modal

    public static function nettProfit($start,$end){
        $nett_sales = Coa::nettSales($start,$end);
        $cogs = Coa::cogs($start,$end);
        $gross_profit = $nett_sales - $cogs;
        $biayaa = Coa::biaya($start,$end);
        $laba_operasional = $gross_profit - $biayaa['amount'];
        $laba_bersih_non = Coa::laba_bersih_non($start,$end);
        $laba_rugi = Coa::laba_rugi($start,$end);
        $nett_profit = $laba_operasional+$laba_bersih_non['amount']+$laba_rugi[0]['amount']+$laba_rugi[1]['amount'];

        return $nett_profit;
    }

    public static function modalAwal($start,$end){
        if($start <> NULL || $end <> NULL){
            $end = date_create($start)->modify('-1 days')->format('Y-m-d');
            $start = '';

            $set_modal = Coa::setoranModal($start,$end);
            $prive = Coa::pengeluaranPribadi($start,$end);
            $nett_profit = Coa::nettProfit($start,$end);

            $modal_awal = $set_modal-$prive+$nett_profit;
        }else{
            $modal_awal = 0;
        }

        return $modal_awal;
    }

    public static function setoranModal($start,$end){
        $sales_debet = Jurnal::where('AccNo','3.3')->where('AccPos','Debet');
        $sales_credit = Jurnal::where('AccNo','3.3')->where('AccPos','Credit');
        if($start <> NULL || $end <> NULL){
            $sales_debet->whereBetween('date',[$start,$end]);
            $sales_credit->whereBetween('date',[$start,$end]);
        }
        return $sales_credit->sum('Amount')-$sales_debet->sum('Amount');
    }

    public static function pengeluaranPribadi($start,$end){
        $sales_debet = Jurnal::where('AccNo','3.2')->where('AccPos','Debet');
        $sales_credit = Jurnal::where('AccNo','3.2')->where('AccPos','Credit');
        if($start <> NULL || $end <> NULL){
            $sales_debet->whereBetween('date',[$start,$end]);
            $sales_credit->whereBetween('date',[$start,$end]);
        }
        return $sales_credit->sum('Amount')-$sales_debet->sum('Amount');
    }

    public static function modalAkhir($start,$end){
        $modal_awal = Coa::modalAwal($start,$end);
        $set_modal = Coa::setoranModal($start,$end);
        $prive = Coa::pengeluaranPribadi($start,$end);
        $nett_profit = Coa::nettProfit($start,$end);
        $perubahan_modal = $set_modal-$prive+$nett_profit;
        $modal_akhir = $modal_awal+$perubahan_modal;

        return $modal_akhir;
    }

    // Neraca

    public static function neraca($date,$no){
        // GET Parent Account  = ASSET
        $parent = Coa::where('AccNo',$no)->select('AccNo','AccName')->first();
        $sum = 0;
        $data = collect();

        // GET 2nd inheritance
        $collect2 = collect();
        foreach(Coa::where('AccParent',$parent->AccNo)->where('AccNo','NOT LIKE',$parent->AccNo)->get() as $key2){
            $sum2 = 0;
            $col2 = collect();

            // GET 3rd Inheritance
            $collect3 = collect();
            foreach(Coa::where('AccParent',$key2->AccNo)->get() as $key3){
                $sum3 = 0;
                $col3 = collect();

                // GET 4th Inheritance
                $collect4 = collect();
                foreach(Coa::where('AccParent',$key3->AccNo)->get() as $key4){
                    $sum4 = 0;
                    $col4 = collect();

                    // Get 5th Inheritance
                    $collect5 = collect();
                    foreach(Coa::where('AccParent',$key4->AccNo)->get() as $key5){
                        $sum5 = 0;
                        $col5 = collect();

                        // Get 6th Inheritance
                        $collect6 = collect();
                        foreach(Coa::where('AccParent',$key5->AccNo)->get() as $key6){
                            $col6 = collect();
                            if($key6->StatusAccount == 'Detail'){
                                // Get Total Amount From Jurnal
                                $sales6_debet = Jurnal::where('AccNo',$key6->AccNo)->where('AccPos','Debet');
                                $sales6_credit = Jurnal::where('AccNo',$key6->AccNo)->where('AccPos','Credit');
                                if($date <> NULL){
                                    $sales6_debet->where('date','<=',$date);
                                    $sales6_credit->where('date','<=',$date);
                                }
                                if($no == 1){
                                    $amount6 = $sales6_debet->sum('Amount') - $sales6_credit->sum('Amount');
                                }else {
                                    $amount6 = $sales6_credit->sum('Amount') - $sales6_debet->sum('Amount');
                                }


                                // Incement
                                $sum5+=$amount6;
                            }

                            $col6->put('name',$key6->AccName);
                            $col6->put('no',$key6->AccNo);
                            $col6->put('amount',$amount6);

                            $collect6->push($col6);
                        }
                        if($key5->StatusAccount == 'Detail'){
                            // Get Total Amount From Jurnal
                            $sales5_debet = Jurnal::where('AccNo',$key5->AccNo)->where('AccPos','Debet');
                            $sales5_credit = Jurnal::where('AccNo',$key5->AccNo)->where('AccPos','Credit');
                            if($date <> NULL){
                                $sales5_debet->where('date','<=',$date);
                                $sales5_credit->where('date','<=',$date);
                            }
                            if($no == 1){
                                $amount5 = $sales5_debet->sum('Amount')-$sales5_credit->sum('Amount');
                            }else {
                                $amount5 = $sales5_credit->sum('Amount')-$sales5_debet->sum('Amount');
                            }


                            // Incement
                            $sum4+=$amount5;
                        }else{
                            $sum4+=$sum5;
                            $amount5=$sum5;
                        }

                        $col5->put('name',$key5->AccName);
                        $col5->put('no',$key5->AccNo);
                        $col5->put('amount',$amount5);
                        $col5->put('data',$collect6);

                        $collect5->push($col5);
                    }

                    // Cek if Detail or not
                    if($key4->StatusAccount == 'Detail'){
                        // Get Total Amount From Jurnal
                        $sales4_debet = Jurnal::where('AccNo',$key4->AccNo)->where('AccPos','Debet');
                        $sales4_credit = Jurnal::where('AccNo',$key4->AccNo)->where('AccPos','Credit');
                        if($date <> NULL){
                            $sales4_debet->where('date','<=',$date);
                            $sales4_credit->where('date','<=',$date);
                        }
                        if($no == 1){
                            $amount4 = $sales4_debet->sum('Amount')-$sales4_credit->sum('Amount');
                        }else {
                            $amount4 = $sales4_credit->sum('Amount')-$sales4_debet->sum('Amount');
                        }

                        // Incement
                        $sum3+=$amount4;
                    }else{
                        $sum3+=$sum4;
                        $amount4=$sum4;
                    }

                    $col4->put('name',$key4->AccName);
                    $col4->put('no',$key4->AccNo);
                    $col4->put('amount',$amount4);
                    $col4->put('data',$collect5);

                    $collect4->push($col4);
                }

                // Cek if Detail or not
                if($key3->StatusAccount == 'Detail'){
                    // Get Total Amount From Jurnal
                    $sales3_debet = Jurnal::where('AccNo',$key3->AccNo)->where('AccPos','Debet');
                    $sales3_credit = Jurnal::where('AccNo',$key3->AccNo)->where('AccPos','Credit');
                    if($date <> NULL){
                        $sales3_debet->where('date','<=',$date);
                        $sales3_credit->where('date','<=',$date);
                    }
                    if($no == 1){
                        $amount3 = $sales3_debet->sum('Amount')-$sales3_credit->sum('Amount');
                    }else {
                        $amount3 = $sales3_credit->sum('Amount')-$sales3_debet->sum('Amount');
                    }

                    // Incement
                    $sum2+=$amount3;
                }else{
                    $sum2+=$sum3;
                    $amount3=$sum3;
                }

                $col3->put('name',$key3->AccName);
                $col3->put('no',$key3->AccNo);
                $col3->put('amount',$amount3);
                $col3->put('data',$collect4);

                $collect3->push($col3);
            }

            // Cek if Detail or not
            if($key2->StatusAccount == 'Detail'){
                // Get Total Amount From Jurnal
                $sales2_debet = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Debet');
                $sales2_credit = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Credit');
                if($date <> NULL){
                    $sales2_debet->where('date','<=',$date);
                    $sales2_credit->where('date','<=',$date);
                }
                if($no == 1){
                    $amount2 = $sales2_debet->sum('Amount')-$sales2_credit->sum('Amount');
                }else {
                    $amount2 = $sales2_credit->sum('Amount')-$sales2_debet->sum('Amount');
                }

                // Incement
                $sum+=$amount2;
            }else{
                $sum+=$sum2;
                $amount2=$sum2;
            }
            $col2->put('name',$key2->AccName);
            $col2->put('no',$key2->AccNo);
            $col2->put('amount',$amount2);
            $col2->put('data',$collect3);

            $collect2->push($col2);
        }

        $data->put('name',$parent->AccName);
        $data->put('no',$parent->AccNo);
        $data->put('amount',$sum);
        $data->put('data',$collect2);

        return $data;
    }

    public static function hutang($date){

    }

    public static function kasBank($parent){
        $sum = 0;
        foreach($parent as $key2){
            $tot = 0;
            $check = Coa::where('AccParent',$key2->AccNo)->where('AccNo','NOT LIKE',$key2->AccNo)->count();
            if($check > 0){
                $temp = $key2->AccNo;
                $sub = Coa::where('AccParent',$key2->AccNo)->where('AccNo','NOT LIKE',$key2->AccNo)->get();
                Coa::kasBank($sub);
            }else{
                $debet = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Debet')->sum('Amount');
                $credit = Jurnal::where('AccNo',$key2->AccNo)->where('AccPos','Credit')->sum('Amount');
                $value = $debet-$credit;
                $acparent = $key2->AccParent;
                $sum+=$value;
                // echo $key2->AccNo." - ".$key2->AccName.": ".$value."<br>";
                echo "<tr>
                <td>".$key2->AccNo." - ".$key2->AccName."</td>
                <td>Rp ".number_format($value,2,',','.')."</td></tr>";
            }
        }
    }
}
