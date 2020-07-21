<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    public function petugas(){
        return $this->belongsTo('App\Employee', 'creator', 'id');
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

    public static function viewJurnal(Request $request){

        $page = MenuMapping::getMap(session('user_id'),"FIJU");
        $param = $request->param;
        $coa = $request->coa;
        $position = $request->position;
        $start = $request->start_date;
        $end = $request->end_date;

        $draw = $request->draw;
        $row = $request->start;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
        $searchValue = $_POST['search']['value']; // Search value

        if ($param == "umum") {
            $jurnal = Jurnal::join('tblcoa', 'tbljurnal.AccNo', 'tblcoa.AccNo')->join('tblemployee', 'tbljurnal.creator', 'tblemployee.id')->select('tbljurnal.id','id_jurnal','tbljurnal.AccNo','tblcoa.AccName','AccPos','Amount','date','description','notes_item','tbljurnal.created_at','tbljurnal.updated_at','tblemployee.name AS petugas')->where('id_jurnal','LIKE','JN%');
        }elseif ($param == "mutasi") {
            $jurnal = Jurnal::join('tblcoa', 'tbljurnal.AccNo', 'tblcoa.AccNo')->join('tblemployee', 'tbljurnal.creator', 'tblemployee.id')->select('tbljurnal.id','id_jurnal','tbljurnal.AccNo','tblcoa.AccName','AccPos','Amount','date','description','notes_item','tbljurnal.created_at','tbljurnal.updated_at','tblemployee.name AS petugas')->where('id_jurnal','LIKE','%%');
        }

        if($coa <> "all"){
            $jurnal->where('tbljurnal.AccNo',$coa);
        }

        if($position <> "all"){
            $jurnal->where('AccPos',$position);
        }

        if($start <> NULL && $end <> NULL){
            $jurnal->whereBetween('date',[$start,$end]);
        }

        $totalRecords = $jurnal->count();

        if($searchValue != ''){
            $jurnal->where('id_jurnal', 'LIKE', '%'.$searchValue.'%')->orWhere('tbljurnal.AccNo', 'LIKE', '%'.$searchValue.'%')->orWhere('tblcoa.AccName', 'LIKE', '%'.$searchValue.'%')->orWhere('Amount', 'LIKE', '%'.$searchValue.'%')->orWhere('date', 'LIKE', '%'.$searchValue.'%')->orWhere('description', 'LIKE', '%'.$searchValue.'%')->orWhere('notes_item', 'LIKE', '%'.$searchValue.'%')->orWhere('tblemployee.name', 'LIKE', '%'.$searchValue.'%');
        }
        $totalRecordwithFilter = $jurnal->count();

        if($columnName == "no"){
            $jurnal = $jurnal->orderBy('id', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }elseif($columnName == "Debet" || $columnName == "Credit"){
            $jurnal = $jurnal->orderBy('Amount', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }elseif($columnName == "creator"){
            $jurnal = $jurnal->orderBy('petugas', $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }else{
            $jurnal = $jurnal->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        }

        $data = collect();
        $nomor = 1;
        foreach($jurnal as $jn){
            $row = collect();
            $row->put('no', $nomor++);
            $row->put('id_jurnal', $jn->id_jurnal);
            $row->put('date', $jn->date);
            $row->put('AccNo', $jn->AccNo);
            $row->put('AccName', $jn->coa->AccName);
            if($jn->AccPos == "Debet"){
                $row->put('Debet', $jn->Amount);
                $row->put('Credit', "");
            }elseif($jn->AccPos == "Credit"){
                $row->put('Credit', $jn->Amount);
                $row->put('Debet', "");
            }
            $row->put('notes_item', $jn->notes_item);
            $row->put('description', $jn->description);
            $row->put('creator', $jn->petugas);
            if($param == "umum"){
                $button = "";
                if(array_search("FIJUE", $page)){
                    $button .= '<a href="/jurnal/'.$jn->id_jurnal.'/edit" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5"> Update</a>';
                }

                if(array_search("FIJUD", $page)){
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:;" id="'.$jn->id_jurnal.'" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5 delete"> Delete</a>';
                }
                $row->put('option', $button);
            }
            $data->push($row);
        }

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );
        return $response;
    }

    public static function getTotalJurnal($start,$end,$coa,$position,$param){

        // ini_set('memory_limit', '256M');
        if ($param == "umum") {
            $jurdebet = Jurnal::where('id_jurnal','LIKE','JN%');
            $jurcredit = Jurnal::where('id_jurnal','LIKE','JN%');
        }elseif ($param == "mutasi") {
            $jurdebet = Jurnal::where('id_jurnal','LIKE','%%');
            $jurcredit = Jurnal::where('id_jurnal','LIKE','%%');
        }

        if($coa <> "all"){
            $jurdebet->where('AccNo',$coa);
            $jurcredit->where('AccNo',$coa);
        }

        if($position <> "all"){
            $jurdebet->where('AccPos',$position);
            $jurcredit->where('AccPos',$position);
        }

        if($start <> NULL && $end <> NULL){
            $jurdebet->whereBetween('date',[$start,$end]);
            $jurcredit->whereBetween('date',[$start,$end]);
        }

        $ttl_debet = $jurdebet->where('AccPos','Debet')->sum('Amount');
        $ttl_credit = $jurcredit->where('AccPos','Credit')->sum('Amount');

        $data = collect();
        $data->put('ttl_debet',$ttl_debet);
        $data->put('ttl_credit',$ttl_credit);
        return $data;
    }

    public static function generalLedger($start,$end,$coa){

        $jurnal = Jurnal::where('AccNo',$coa);
        $jurdebet = Jurnal::where('AccNo',$coa);
        $jurcredit = Jurnal::where('AccNo',$coa);

        if($start <> NULL && $end <> NULL){
            $jurnal->whereBetween('date',[$start,$end]);
            $jurdebet->whereBetween('date',[$start,$end]);
            $jurcredit->whereBetween('date',[$start,$end]);
        }

        $ttl_debet = $jurdebet->where('AccPos','Debet')->sum('Amount');
        $ttl_credit = $jurcredit->where('AccPos','Credit')->sum('Amount');
        $jurnal = $jurnal->orderBy('date','asc')->get();

        $data = collect();
        $data->put('data',$jurnal);
        $data->put('ttl_debet',$ttl_debet);
        $data->put('ttl_credit',$ttl_credit);
        return $data;
    }

    public static function addJurnal($id_jurnal,$amount,$date,$desc,$coa,$position,$user_id=null){
        if($user_id != null){
            $user = $user_id;
        }else{
            $user = session('user_id');
        }

        $jurnal = new Jurnal(array(
            'id_jurnal' => $id_jurnal,
            'AccNo' => $coa,
            'AccPos' => $position,
            'Amount' => $amount,
            'company_id' => 1,
            'date' => $date,
            'description' => $desc,
            'creator' => $user,
        ));

        $jurnal->save();
    }

    public static function refreshCogs($data){
        $getTrxId = SalesDet::whereIn('prod_id',$data)->select('trx_id')->groupBy('trx_id')->orderBy('trx_id')->get();
        foreach ($getTrxId as $key) {
            // SALES
            $sales_jurnal = $key->trx->jurnal_id;
            $trx_date = $key->trx->trx_date;
            $cogs = 0;
            foreach (SalesDet::where('trx_id',$key->trx_id)->select('price','qty','prod_id')->get() as $key2) {
                $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key2->prod_id)->where('tblpotrx.tgl','<=',$trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key2->prod_id)->where('tblpotrx.tgl','<=',$trx_date)->sum('tblpotrxdet.qty');

                if($sumprice <> 0 && $sumqty <> 0){
                    $avcost = $sumprice/$sumqty;
                }else{
                    $avcost = 0;
                }

                $cogs +=  $avcost * $key2->qty;
            }
            // Update Jurnal Sales
            if($sales_jurnal <> 0){
                // debet COGS
                    $jurnal_sales_a = Jurnal::where('id_jurnal',$sales_jurnal)->where('AccNo','5.1')->first();
                    $jurnal_sales_a->amount = $cogs;
                    $jurnal_sales_a->update();
                // Credit Persediaan Barang milik customer
                    $jurnal_sales_b = Jurnal::where('id_jurnal',$sales_jurnal)->where('AccNo','2.1.3')->first();
                    $jurnal_sales_b->amount = $cogs;
                    $jurnal_sales_b->update();
            }

            // DO
            foreach(DeliveryOrder::where('sales_id',$key->id)->select('id','jurnal_id')->get() as $dokey){
                $do_sum = 0;

                foreach(DeliveryDetail::where('do_id',$dokey->id)->select('qty','product_id')->get() as $dodet){
                    $do_sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$dodet->product_id)->where('tblpotrx.tgl','<=',$trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                    $do_sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$dodet->product_id)->where('tblpotrx.tgl','<=',$trx_date)->sum('tblpotrxdet.qty');

                    if($do_sumprice <> 0 && $do_sumqty <> 0){
                        $do_avcost = $do_sumprice/$do_sumqty;
                    }else{
                        $do_avcost = 0;
                    }

                    $price = $do_avcost * $dodet->qty;
                    $do_sum+=$price;
                }
                // Update Jurnal DO
                    // debet Persediaan Barang milik Customer
                        $jurnal_do_a = Jurnal::where('id_jurnal',$dokey->jurnal_id)->where('AccNo','2.1.3')->first();
                        $jurnal_do_a->amount = $do_sum;
                        $jurnal_do_a->update();
                    // credit Persediaan Barang digudang
                        $jurnal_do_b = Jurnal::where('id_jurnal',$dokey->jurnal_id)->where('AccNo','1.1.4.1.2')->first();
                        $jurnal_do_b->amount = $do_sum;
                        $jurnal_do_b->update();
            }
        }
    }
}
