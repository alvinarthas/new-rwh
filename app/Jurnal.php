<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Coa;
use App\Product;
use App\ManageHarga;
use App\Purchase;
use App\Member;
use App\BankMember;
use App\BonusBayar;

class Jurnal extends Model
{
    protected $table ='tbljurnal';
    protected $fillable = [
        'id_jurnal', 'AccNo','AccPos','Amount','company_id','date','description','creator','status','nama_category','budget_month','budget_year','notes_item'
    ];

    public function coa(){
        return $this->belongsTo('App\Coa','AccNo','AccNo');
    }

    public static function getJurnalID($jenis){
        $jurnal = Jurnal::where('id_jurnal','LIKE',$jenis.'%')->orderBy(DB::raw('CAST(SUBSTRING(id_jurnal, 4, 10) AS INT)'),'desc')->select('id_jurnal')->distinct('id_jurnal');
        $count_jurnal = $jurnal->count();
        if($count_jurnal == 0){
            $id_jurnal = $jenis.".1";
        }else{
            $getJurnal = $jurnal->first();
            $num_jurnal = intval(substr($getJurnal->id_jurnal,3,10))+1;
            $id_jurnal = $jenis.".".$num_jurnal;
        }
        return $id_jurnal;
    }

    public static function viewJurnal($start,$end,$coa,$position,$param){
        if($param == "all"){
            $jurnal = Jurnal::orderBy('date','asc')->get();
            $ttl_debet = Jurnal::where('AccPos','Debet')->sum('Amount');
            $ttl_credit = Jurnal::where('AccPos','Credit')->sum('Amount');

        }elseif($param == NULL){
            $jurnal = Jurnal::whereBetween('date',[$start,$end]);
            $jurdebet = Jurnal::whereBetween('date',[$start,$end]);
            $jurcredit = Jurnal::whereBetween('date',[$start,$end]);
            if($coa <> "all"){
                $jurnal->where('AccNo',$coa);
                $jurdebet->where('AccNo',$coa);
                $jurcredit->where('AccNo',$coa);
            }

            $ttl_debet = $jurdebet->where('AccPos','Debet')->sum('Amount');
            $ttl_credit = $jurcredit->where('AccPos','Credit')->sum('Amount');

            if($position <> "all"){
                $jurnal->where('AccPos',$position);
            }

            $jurnal = $jurnal->orderBy('date','asc')->get();
        }

        $data = collect();
        $data->put('data',$jurnal);
        $data->put('ttl_debet',$ttl_debet);
        $data->put('ttl_credit',$ttl_credit);
        return $data;
    }

    public static function addJurnal($id_jurnal,$amount,$date,$desc,$coa,$position){
        $jurnal = new Jurnal(array(
            'id_jurnal' => $id_jurnal,
            'AccNo' => $coa,
            'AccPos' => $position,
            'Amount' => $amount,
            'company_id' => 1,
            'date' => $date,
            'description' => $desc,
            'creator' => session('user_id'),
        ));

        $jurnal->save();
    }

    public static function totalPendapatan($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','4-%')->where('tblcoa.SaldoNormal','Cr')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','4-%')->where('tblcoa.SaldoNormal','Cr')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        return $plus-$minus;
    }

    public static function totalPotongan($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','4-%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','4-%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        return $plus-$minus;
    }

    public static function totalCogs($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','5-%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','5-%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        return $plus-$minus;
    }

    public static function totalExpense($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','6-%')->where('tblcoa.AccName','NOT LIKE','%pribadi%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','6-%')->where('tblcoa.AccName','NOT LIKE','%pribadi%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        return $plus-$minus;
    }

    public static function totalExpensePribadi($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','6-%')->where('tblcoa.AccName','LIKE','%pribadi%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','6-%')->where('tblcoa.AccName','LIKE','%pribadi%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        return $plus-$minus;
    }

    public static function setoranModal($start,$end){
        $data = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.description','LIKE','%setoran modal%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        return $data;
    }

    public static function totalPendapatLain($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','8-%')->where('tblcoa.SaldoNormal','Cr')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','8-%')->where('tblcoa.SaldoNormal','Cr')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        return $plus-$minus;
    }

    public static function totalPotonganPendapatan($start,$end){
        $plus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','8-%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Debet')->sum('Amount');

        $minus = Jurnal::join('tblcoa','tbljurnal.AccNo','=','tblcoa.AccNo')->where('tbljurnal.AccNo','LIKE','8-%')->where('tblcoa.SaldoNormal','Db')->whereBetween('tbljurnal.date',[$start,$end])->where('tbljurnal.AccPos','Credit')->sum('Amount');

        return $plus-$minus;
    }

    // SUM
    public static function profitLoss($start,$end){
        $total_pendapatan = (Jurnal::totalPendapatan($start,$end))-(Jurnal::totalPotongan($start,$end));
        $total_pendapatan_lain=(Jurnal::totalPendapatLain($start,$end))-(Jurnal::totalPotonganPendapatan($start,$end));
        $total_cogs = Jurnal::totalCogs($start,$end);
        $total_expense = Jurnal::totalExpense($start,$end);

        return $total_pendapatan-$total_cogs-$total_expense+$total_pendapatan_lain;
    }

    public static function ModalAkhir($start,$end){
        $saldoawal = Coa::where('AccNo','3-100001')->first()->SaldoAwal;
        $profit_loss = profitLoss($start,$end);
        $total_expense_pribadi = Jurnal::totalExpensePribadi($start,$end);
        $setoran_modal = Jurnal::setoranModal($start,$end);

        return $saldoawal+$profit_loss+$setoran_modal-$total_expense_pribadi;
    }

    // SALES REVENUE
    public static function salesRevenue($start,$end){
        $coa = Coa::where('AccNo','LIKE','4-%')->where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
        $data = collect();
        foreach ($coa as $key) {
            $item = collect();
            $debet = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Debet')->sum('Amount');
            $credit = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Credit')->sum('Amount');

            $total_amount=$debet-$credit;

            $item->put('AccNo',$key->AccNo);
            $item->put('AccName',$key->AccName);
            $item->put('total',$total_amount);

            $data->push($item);
        }
        return $data;
    }

    // COGS
    public static function dataCogs($start,$end){
        $coa = Coa::where('AccNo','LIKE','5-%')->where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
        $data = collect();
        foreach ($coa as $key) {
            $item = collect();
            $debet = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Debet')->sum('Amount');
            $credit = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Credit')->sum('Amount');

            $total_amount=$debet-$credit;

            $item->put('AccNo',$key->AccNo);
            $item->put('AccName',$key->AccName);
            $item->put('total',$total_amount);

            $data->push($item);
        }
        return $data;
    }

    // EXPENSES
    public static function dataExpenses($start,$end){
        $coa = Coa::where('AccNo','LIKE','6-%')->where('StatusAccount','Detail')->where('AccName','NOT LIKE','%pribadi%')->orderBy('AccNo','asc')->get();
        $data = collect();
        foreach ($coa as $key) {
            $item = collect();
            $debet = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Debet')->sum('Amount');
            $credit = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Credit')->sum('Amount');

            $total_amount=$debet-$credit;

            $item->put('AccNo',$key->AccNo);
            $item->put('AccName',$key->AccName);
            $item->put('total',$total_amount);

            $data->push($item);
        }
        return $data;
    }

    // PENDAPATAN DAN BEBAN LAINNYA
    public static function dataPendapatandll($start,$end){
        $coa = Coa::where('AccNo','LIKE','8-%')->where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
        $data = collect();
        foreach ($coa as $key) {
            $item = collect();
            $debet = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Debet')->sum('Amount');
            $credit = Jurnal::where('AccNo',$key->AccNo)->whereBetween('date',[$start,$end])->where('AccPos','Credit')->sum('Amount');

            $total_amount=$debet-$credit;

            $item->put('AccNo',$key->AccNo);
            $item->put('AccName',$key->AccName);
            $item->put('total',$total_amount);

            $data->push($item);
        }
        return $data;
    }

    // LABA RUGI
    public static function dataLabaRugi($start){
        $products = Product::orderBy('prod_id','asc')->get();
        $total_bonus = 0;

        $month = date("n", strtotime($start));
        foreach ($products as $product) {
            $m_product = ManageHarga::where('prod_id',$product->id)->where('month',$month)->where('year',$year)->select('harga_modal','harga_distributor')->first();
            $qty = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->where('tblpotrx.month',$month)->where('tblpotrx.year',$year)->where('tblpotrxdet.prod_id',$product->id)->sum('tblpotrxdet.qty');

            if($m_product){
                $selisih = $m_product->harga_distributor - $m_product->harga_modal;
            }else{
                $selisih = 0;
            }
            $bonus = $qty*$selisih;
            $total_bonus+=$bonus;
        }

        //
        $total_realisasi_all=0;
        $members = Member::orderBy('nama','asc')->select('ktp')->get();

        foreach ($members as $member) {
            $total_realisasi=0;
            $banks = BankMember::where('ktp',$member->ktp)->select('norek')->get();

            foreach ($banks as $bank) {
                $bonus = BonusBayar::where('bulan',$month)->where('tahun',$year)->where('no_rek',$bank->norek)->select('bonus')->first();
                if($bonus == NULL){
                    $total_realisasi+=0;
                }else{
                    $total_realisasi+=$bonus->bonus;
                }

            }
            $total_realisasi_all+=$total_realisasi;
        }

        $result = $total_realisasi_all - $total_bonus;
        return $result;
    }
}
