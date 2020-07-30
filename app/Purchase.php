<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\PurchasePayment;
use App\PurchaseDetail;
use App\TempPO;
use App\TempPODet;
use App\Jurnal;
use App\Perusahaan;
use App\ReceiveDet;

class Purchase extends Model
{
    protected $table ='tblpotrx';
    protected $fillable = [
        'month','year','creator','supplier','notes','jurnal_id','tgl','approve','approve_by','total_harga_modal','total_harga_dist'
    ];

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public static function getTop3($month,$year){
        return Purchase::where('month',$month)->where('year',$year)->groupBy('supplier')->orderBy(DB::raw('SUM(total_harga_dist)'),'desc')->select('creator')->get();
    }

    public static function sharePost($month,$year,$creator){
        return Purchase::where('month',$month)->where('year',$year)->where('creator',$creator)->distinct()->count('supplier');
    }

    public static function getOrderPayment($bulan,$tahun,$param){
        $data = collect();
        $data1 = collect();
        if($param == 'all'){
            $purchase = Purchase::where('approve',1);
        }elseif($param == NULL){
            $purchase = Purchase::where('month',$bulan)->where('year',$tahun)->where('approve',1);
        }

        $ttl_trx = $purchase->count('id');
        $ttl_order = 0;
        $ttl_pay = 0;
        foreach($purchase->get() as $key){
            $temp = collect();
            $order = PurchaseDetail::where('trx_id',$key->id)->sum(DB::raw('qty * price_dist'));
            $pay_amount = PurchasePayment::where('trx_id',$key->id)->sum('payment_amount');
            $ttl_order+=$order;
            $ttl_pay+=$pay_amount;

            $temp->put('trx_id',$key->id);
            $temp->put('month',$key->month);
            $temp->put('year',$key->year);
            $temp->put('supplier',$key->supplier()->first()->nama);
            $temp->put('id',$key->id);
            $temp->put('order',$order);
            $temp->put('paid',$pay_amount);
            if($pay_amount == $order){
                $temp->put('status',1);
            }elseif($pay_amount > $order){
                $temp->put('status',2);
            }else{
                $temp->put('status',0);
            }
            $data1->push($temp);
        }
        $data->put('data',$data1);
        $data->put('ttl_order',$ttl_order);
        $data->put('ttl_pay',$ttl_pay);
        $data->put('ttl_trx',$ttl_trx);

        return $data;
    }

    public static function setJurnal($id,$user_id){
        $id_jurnal = 'PO.'.$id;

        $purchase = Purchase::where('id',$id)->first();

        $purchase->approve = 1;
        $purchase->approve_by = $user_id;
        $purchase->jurnal_id = $id_jurnal;

        $purchase->update();

        $total_modal = $purchase->total_harga_modal;
        $total_tertahan = PurchaseDetail::where('trx_id',$id)->sum(DB::Raw('(price_dist - price)* qty'));
        $total_distributor = $purchase->total_harga_dist;

        $jurnal_desc = "PO.".$id;

        //insert debet Persediaan Barang Indent ( harga modal x qty )
        Jurnal::addJurnal($id_jurnal,$total_modal,$purchase->tgl,$jurnal_desc,'1.1.4.1.1','Debet',$user_id);
        //insert debet Estimasi Bonus
        if($total_tertahan < 0){
            $total_tertahan = $total_tertahan *-1;
            Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Credit',$user_id);
        }else{
            Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Debet',$user_id);
        }

        //insert credit hutang Dagang
        Jurnal::addJurnal($id_jurnal,$total_distributor,$purchase->tgl,$jurnal_desc,'2.1.1','Credit',$user_id);
    }

    public static function updatePurchase($id,$user_id){
        $temp_po = TempPO::where('purchase_id',$id)->first();
        $temp_po_det = TempPODet::where('temp_id',$temp_po->id)->get();

        $purchase = Purchase::where('id',$id)->first();

        // Update and tranfer to Purchase Orginal
        $purchase->supplier = $temp_po->supplier;
        $purchase->month = $temp_po->month;
        $purchase->year = $temp_po->year;
        $purchase->notes = $temp_po->notes;
        $purchase->creator = $temp_po->creator;
        $purchase->tgl = $temp_po->tgl;
        $purchase->total_harga_modal = $temp_po->total_harga_modal;
        $purchase->total_harga_dist = $temp_po->total_harga_dist;

        $purchase->update();

        // Delete Old Original Detail
        $purdet = PurchaseDetail::where('trx_id',$id)->delete();

        // transfer new Detail
        foreach ($temp_po_det as $key) {
            if ($key->purchasedetail_id == "baru"){
                $purchasedet = new PurchaseDetail(array(
                    'trx_id' => $purchase->id,
                    'prod_id' => $key->prod_id,
                    'qty' => $key->qty,
                    'unit' => $key->unit,
                    'creator' => $key->creator,
                    'price' => $key->price,
                    'price_dist' => $key->price_dist,
                ));
                $purchasedet->save();
            }else{
                $purchasedet = new PurchaseDetail(array(
                    'id' => $key->purchasedetail_id,
                    'trx_id' => $purchase->id,
                    'prod_id' => $key->prod_id,
                    'qty' => $key->qty,
                    'unit' => $key->unit,
                    'creator' => $key->creator,
                    'price' => $key->price,
                    'price_dist' => $key->price_dist,
                ));
                $purchasedet->save();
            }
        }

        // Matikan status temp po
        $temp_po->delete();

        // Re Check Receive ITEM and Recycle
        foreach (ReceiveDet::select('id','purchasedetail_id')->where('trx_id',$purchase->id)->get() as $key) {
            $checkPodet = PurchaseDetail::where('id',$key->purchasedetail_id)->count();

            if ($checkPodet == 0){
                $key->delete();
            }
        }
        ReceiveDet::recycleRP($purchase->id);

        // Update Jurnal

        // Penjurnalan
        $total_modal=0;
        $total_tertahan=0;
        $total_distributor=0;
        $prodarray = collect();

        foreach (PurchaseDetail::where('trx_id',$id)->get() as $key) {
            $selisih = $key->price_dist - $key->price;
            $total_modal += ($key->price * $key->qty);
            $total_tertahan+=($selisih*$key->qty);
            $total_distributor+=($key->price_dist*$key->qty);

            $prodarray->push($key->prod_id);
        }

        if($purchase->jurnal_id <> 0 || $purchase->jurnal_id <> '0'){
            //Update debet Persediaan Barang Indent ( harga modal x qty )
                $jurnal1 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.4.1.1')->first();
                $jurnal1->amount = $total_modal;
                $jurnal1->date = $purchase->tgl;
                $jurnal1->update();
            //Update debet Estimasi Bonus
                if($total_tertahan < 0){
                    $total_tertahan = $total_tertahan *-1;
                    $jurnal2 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.3.4')->first();
                    $jurnal2->amount = $total_tertahan;
                    $jurnal2->date = $purchase->tgl;
                    $jurnal2->AccPos = "Credit";
                    $jurnal2->update();
                }else{
                    $jurnal2 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.3.4')->first();
                    $jurnal2->amount = $total_tertahan;
                    $jurnal2->date = $purchase->tgl;
                    $jurnal2->update();
                }

            //Update credit hutang Dagang
                $jurnal3 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','2.1.1')->first();
                $jurnal3->amount = $total_distributor;
                $jurnal3->date = $purchase->tgl;
                $jurnal3->update();
        }else{
            $id_jurnal = 'PO.'.$id;
            $purchase->jurnal_id = $id_jurnal;
            $purchase->approve = 1;
            $purchase->approve_by = $user_id;
            $purchase->update();
            $jurnal_desc = "PO.".$id;
            //insert debet Persediaan Barang Indent ( harga modal x qty )
            Jurnal::addJurnal($id_jurnal,$total_modal,$purchase->tgl,$jurnal_desc,'1.1.4.1.1','Debet',$user_id);
            //insert debet Estimasi Bonus
            if($total_tertahan < 0){
                $total_tertahan = $total_tertahan *-1;
                Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Credit',$user_id);
            }else{
                Jurnal::addJurnal($id_jurnal,$total_tertahan,$purchase->tgl,$jurnal_desc,'1.1.3.4','Debet',$user_id);
            }

            //insert credit hutang Dagang
            Jurnal::addJurnal($id_jurnal,$total_distributor,$purchase->tgl,$jurnal_desc,'2.1.1','Credit',$user_id);
        }
        Jurnal::refreshCogs($prodarray);
    }

    public static function report($start,$end){
        $data = collect();

        foreach(Perusahaan::all() as $key){
            $temp = collect();
            $detail = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id');

            if($start <> NULL && $end <> NULL){
                $detail->whereBetween('tblpotrx.tgl',[$start,$end]);
            }

            $price = $detail->where('tblpotrx.supplier',$key->id)->sum(DB::Raw('tblpotrxdet.qty * tblpotrxdet.price'));
            $price_dist = $detail->where('tblpotrx.supplier',$key->id)->sum(DB::Raw('tblpotrxdet.qty * tblpotrxdet.price_dist'));

            $temp->put('supplier',$key->nama);
            $temp->put('price',$price);
            $temp->put('price_dist',$price_dist);

            $data->push($temp);
        }
        return $data;
    }

    public static function countPost($month,$year){
        $d = array_values(array_column(DB::select("SELECT e.id FROM tblemployee as e
        INNER JOIN tblemployeerole as rl on e.username = rl.username
        INNER JOIN tblrole as r ON r.id = rl.role_id
        WHERE r.role_name REGEXP 'Manager|General|Superadmin|Direktur|security'"),'id'));

        $count = Purchase::where('month',$month)->where('year',$year)->whereNotIn('creator',$d)->distinct()->count('supplier');

        return $count;
    }

    public static function checkReceive($start,$end){
        if($start <> NULL && $end <> NULL){
            $purchases = Purchase::whereBetween('tgl',[$start,$end])->where('approve',1)->get();
        }else{
            $purchases = Purchase::where('approve',1)->get();
        }

        $data = collect();
        foreach ($purchases as $purchase) {
            $collect = collect();
            $purchasedet = PurchaseDetail::where('trx_id',$purchase->id)->select('*',DB::Raw('SUM(qty) as sum_qty'))->groupBy('prod_id', 'price')->get();
            $detcount = $purchasedet->count();
            $count = 0;
            foreach($purchasedet as $key){
                $count_receive_product = ReceiveDet::where('purchasedetail_id', $key->id)->sum('qty');
                if($key->sum_qty == $count_receive_product){
                    $count++;
                }
            }

            if($detcount == $count){
                $status = 1;
            }else{
                $status = 0;
            }
            $collect->put('trx_id',$purchase->id);
            $collect->put('supplier',Perusahaan::where('id', $purchase->supplier)->first()->nama);
            $collect->put('ttl_dist',$purchase->total_harga_dist);
            $collect->put('ttl_modal',$purchase->total_harga_modal);
            $collect->put('status_receive',$status);
            $data->push($collect);
        }
        return $data;
    }

    public static function recylePurchase($id){
        $purchase = Purchase::where('id',$id)->first();

        // Penjurnalan
        $total_modal=0;
        $total_tertahan=0;
        $total_distributor=0;

        $prodarray = collect();

        foreach (PurchaseDetail::where('trx_id',$id)->get() as $key) {
            $selisih = $key->price_dist - $key->price;
            $total_modal += ($key->price * $key->qty);
            $total_tertahan+=($selisih*$key->qty);
            $total_distributor+=($key->price_dist*$key->qty);

            $prodarray->push($key->prod_id);
        }

        //Update debet Persediaan Barang Indent ( harga modal x qty )
            $jurnal1 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.4.1.1')->first();
            $jurnal1->amount = $total_modal;
            $jurnal1->update();
        //Update debet Estimasi Bonus
            if($total_tertahan < 0){
                $total_tertahan = $total_tertahan *-1;
                $jurnal2 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.3.4')->first();
                $jurnal2->amount = $total_tertahan;
                $jurnal2->AccPos = "Credit";
                $jurnal2->update();
            }else{
                $jurnal2 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','1.1.3.4')->first();
                $jurnal2->amount = $total_tertahan;
                $jurnal2->update();
            }

        //Update credit hutang Dagang
            $jurnal3 = Jurnal::where('id_jurnal',$purchase->jurnal_id)->where('AccNo','2.1.1')->first();
            $jurnal3->amount = $total_distributor;
            $jurnal3->update();

        // Refresh COGS
            Jurnal::refreshCogs($prodarray);
    }

    // Get Data for Serverless PO
    public static function purchaseData(Request $request){
        // Datatables Parameter POST
        $draw = $request->draw;
        $row = $request->start;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        // Custom Parameter Post
        $month = $request->month;
        $year = $request->year;
        $param = $request->param;

        // Start Query
        $page = MenuMapping::getMap(session('user_id'),"PUPU");

        // Sales Initialization
        $purchase = Purchase::select('id','month','creator','year','supplier','notes','approve','tgl');

        // Query By Param
        if($param != "all"){
            $purchase = $purchase->where('month',$month)->where('year',$year);
        }

        $totalRecords = $purchase->count();

        // Search based on param
        if($searchValue != ''){
            $tempCount = 0;

            // GET Char of TRX ID
            $charID = preg_replace("/[^a-zA-Z]/", "", $searchValue);
            $numID = preg_replace('/[^0-9]/', '', $searchValue);

            // Search based on Posting Period
            $parseDate = date_parse($charID);

            if ($parseDate['month']){
                $purchase = $purchase->orWhere('month',$parseDate['month'])->orWhere('year',$numID);
            }

            // Search Based on Supplier Name
            $purchase = $purchase->orWhereHas('supplier', function ($query) use ($searchValue) {
                $query->where('nama', 'like', '%'.$searchValue.'%');
            });
            // or Search Based on Creator Name
            $purchase = $purchase->orWhereHas('creator', function ($query) use ($searchValue) {
                $query->where('name', 'like', $searchValue.'%');
            });

            // or Search Based on TRX DATE
            $purchase = $purchase->orWhere('tgl','like',$searchValue.'%');

            // or Search Based on Notes
            $purchase = $purchase->orWhere('notes','like',$searchValue.'%');

            // or Search Based on TRX ID
            $purchase = $purchase->orWhere('id',$numID);
        }

        $totalRecordwithFilter = $purchase->count();

        // Sort and Pagination
        $purchase = $purchase->groupBy('id');
        if($columnName == "no" || $columnName == "trx_id"){
            $purchase->orderBy('id', $columnSortOrder);
        }elseif($columnName == "period"){
            $purchase->orderBy('month', $columnSortOrder);
        }elseif($columnName == "supplier"){
            $purchase->orderBy('supplier', $columnSortOrder);
        }elseif($columnName == "creator"){
            $purchase->orderBy('creator', $columnSortOrder);
        }elseif($columnName == "tgl"){
            $purchase->orderBy('tgl', $columnSortOrder);
        }elseif($columnName == "notes"){
            $purchase->orderBy('notes', $columnSortOrder);
        }else{
            $purchase->orderBy($columnName, $columnSortOrder);
        }

        $purchase = $purchase->offset($row)->limit($rowperpage)->get();
        $data = collect();
        $i = 1;

        foreach($purchase as $key){
            $detail = collect();
            $period = date("F", mktime(0, 0, 0, $key->month, 10))." ".$key->year;
            $options = '';
            $trxModal = '';

                $trxModal .= '<td><a href="javascript:;" onclick="getDetail('.$key->id.')" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">PO.'.$key->id.'</a></td>';

                if (array_search("PUPUU",$page)){
                    $options .= '<a href="'.route('purchase.edit',['id'=>$key->id]).'" class="btn btn-purple btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>';
                }

                if (array_search("PUPUD",$page)){
                    $options .= '<a href="javascript:;" class="btn btn-pink btn-trans waves-effect w-md waves-danger m-b-5" onclick="deletePurchase('.$key->id.')">Delete</a>';
                }

                if ($key->approve == 0){
                    $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$key->id,'role'=>session('role')]));
                    if (array_search("PUPUA",$page)){
                        $options .= '<a href="finspot:FingerspotVer;'.$url_register.'" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase</a>';
                    }

                }else{
                    $count_temp = TempPO::where('purchase_id',$key->id)->count('purchase_id');
                    $status_temp = TempPO::where('purchase_id',$key->id)->where('status',1)->count('purchase_id');

                    if($count_temp > 0 && $status_temp == 1){
                        $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$key->id,'role'=>session('role')]));
                        if (array_search("PUPUA",$page)){
                            $options .= '<a href="finspot:FingerspotVer;'.$url_register.'" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase yang sudah diupdate</a>';
                        }
                    }else{
                        $options .= '<a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Purchase sudah di approve</a>';
                    }
                }

            $detail->put('no', $i++);
            $detail->put('trx_id', $trxModal);
            $detail->put('period', $period);
            $detail->put('supplier', $key->supplier()->first()->nama);
            $detail->put('tgl', $key->tgl);
            $detail->put('notes',$key->notes);
            $detail->put('creator', $key->creator()->first()->name);
            $detail->put('option', $options);
            $data->push($detail);
        }

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );

        return $response;
    }
}
