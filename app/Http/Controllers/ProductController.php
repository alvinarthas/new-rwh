<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use PDF;
use App\Exports\StockControllingExport;
use Carbon\Carbon;

use App\Product;
use App\Perusahaan;
use App\Company;
use App\MenuMapping;
use App\DeliveryDetail;
use App\PurchaseDetail;
use App\ReceiveDet;
use App\Customer;
use App\SalesDet;
use App\Sales;
use App\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('name','asc')->get();
        $page = MenuMapping::getMap(session('user_id'),"PRPD");
        return view('product.index', compact('products','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companys = Company::all();
        $perusahaans = Perusahaan::all();
        $jenis = "create";
        return view('product.form', compact('companys','perusahaans','jenis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'prod_id' => 'required|string|unique:tblproduct',
            'name' => 'required|string',
            'category' => 'required|string',
            'supplier' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $product = new Product(array(
                // Informasi Pribadi
                'company_id' => $request->company_id,
                'name' => $request->name,
                'prod_id' => $request->prod_id,
                'category' => $request->category,
                'supplier' => $request->supplier,
                'stock' => 0
            ));
            // success
            if($product->save()){
                Log::setLog('PRPDC','Create Product ID: '.$request->prod_id);
                return redirect()->route('product.index')->with('status', 'Data berhasil dibuat');
            // fail
            }else{
                return redirect()->back()->withErrors($e);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $companys = Company::all();
        $perusahaans = Perusahaan::all();
        $jenis = "edit";
        return view('product.form', compact('companys','perusahaans','jenis', 'product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'prod_id' => 'required|string',
            'name' => 'required|string',
            'category' => 'required|string',
            'supplier' => 'required',
            'stock' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $product = Product::find($id);
            // Informasi Pribadi
            $product->company_id = $request->company_id;
            $product->name = $request->name;
            $product->prod_id = $request->prod_id;
            $product->category = $request->category;
            $product->supplier = $request->supplier;
            $product->stock = $request->stock;
            $product->prod_id_new = $request->prod_id_new;
            // success
            if($product->update()){
                Log::setLog('PRPDU','Update Product ID: '.$request->prod_id);
                return redirect()->route('product.index')->with('status', 'Data berhasil disimpan');
            // fail
            }else{
                return redirect()->back()->withErrors($e);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if($product->delete()){
            Log::setLog('PRPDD','Delete Product ID: '.$id);
            return redirect()->route('product.index')->with('status', 'Data berhasil dihapus');
        // fail
        }else{
            return redirect()->back()->withErrors($e);
        }
    }

    public function manage()
    {
        $prods = Product::select('name', 'prod_id', 'category', 'harga_distributor', 'harga_modal')->orderBy('name', 'asc')->get();
        $i = 0;
        $page = MenuMapping::getMap(session('user_id'),"PRMH");
        return view('product.manageharga', compact('prods','i','page'));
    }

    public function showProdAjx(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        if($bulan==1){
            $bulan = 12;
            $tahun = $tahun-1;
        }
        $prods = Product::select('name', 'prod_id', 'category')->get();
        $i = 0;

        return view('product.showProdAjxLog', compact('prods','bulan','tahun','i'));
    }

    public function controlling(Request $request){
        if($request->ajax()){
            $product = Product::where('prod_id',$request->prod_id)->first();
            $indent = Product::getIndent($request->prod_id);
            $gudang = Product::getGudang($request->prod_id);
            $brg_cust = Product::getBrgCust($request->prod_id);
            $total = $indent+$gudang-$brg_cust;
            $modal = "stock";

            return response()->json(view('product.controlling.modal',compact('indent','gudang','brg_cust','product','total','modal'))->render());
        }else{
            $products = Product::all();
            // $indent = Product::getIndent($request->prod_id);
            // $gudang = Product::getGudang($request->prod_id);
            // $brg_cust = Product::getBrgCust($request->prod_id);

            return view('product.controlling.index',compact('products'));
        }
    }

    public function export(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die();
        ini_set('max_execution_time', 3000);

        $tgl = date('Y-m-d', strtotime(Carbon::today()));

        $filename = "Stock Controlling RWH (".$tgl.")";

        $exportTo = $request['xto'];
        $data = array();

        $product = Product::all();
        $no = 0;

        foreach($product as $p){
            $supplier = $p->supplier()->first()->nama;
            $prod_id = $p->prod_id;
            $name = $p->name;
            $indent = Product::getIndent($p->prod_id);
            $gudang = Product::getGudang($p->prod_id);
            $brgcust = Product::getBrgCust($p->prod_id);
            $nett = $indent + $gudang - $brgcust;
            $no++;

            $array = array(
                // Data Member
                'No' => $no,
                'Supplier' => $supplier,
                'Product ID' => $prod_id,
                'Nama Produk' => $name,
                'Indent' => $indent,
                'di Gudang' => $gudang,
                'milik Customer' => $brgcust,
                'Nett' => $nett,
            );

            array_push($data, $array);
        }

        // Export To 0 == Export to Excel
        if($exportTo == 0){
            $export = new StockControllingExport($data);

            return Excel::download($export, $filename.'.xlsx');
        // Export To 1 == Export To PDF
        }elseif($exportTo == 1){
            $datas = ['product'=>$data, 'tgl'=>$tgl];

            $pdf = PDF::loadview('product.controlling.pdfstock',$datas)->setPaper('a4', 'landscape');

            $pdf->save(public_path('download/stockcontrolling/'.$filename.'.pdf'));
            return $pdf->download($filename.'.pdf');
        }
    }

    public function mutasiBrgIndent(Request $request){
        if($request->ajax()){
            $modal = "mutasi";
            $product = Product::join('tblperusahaan', 'tblproduct.supplier', 'tblperusahaan.id')->where('tblproduct.id', $request->id)->select('tblproduct.id', 'name', 'prod_id', 'tblperusahaan.nama AS supplier')->first();
            $result = array();
            $total = 0;

            // $stockreal = StockReal::join('tbl_receiveitem', 'tbl_stockreal.ri_trx', 'tbl_receiveitem.trx_id')->join('tbl_sales', 'tbl_stockreal.so_trx', 'tbl_sales.trx_id')->where('product_id', $id)->select('tbl_receiveitem.tanggal AS r_tanggal', 'tbl_sales.tanggal AS s_tanggal', 'tbl_receiveitem.trx_id AS ri_trx', 'tbl_sales.trx_id AS so_trx', 'qty', 'hrg_distributor', 'bv')->get();
            $po = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('prod_id', $product->prod_id)->where('jurnal_id', '!=', '0')->select('jurnal_id','tgl', 'qty')->get();

            foreach($po AS $p){
                $tgl = $p['tgl'];
                $trx_id = $p->jurnal_id;
                $status = "IN";
                $total = $total + $p->qty;

                $stock = array(
                    'tanggal' => $tgl,
                    'trx_id'  => $trx_id,
                    'status'  => $status,
                    'qty'     => $p->qty,
                );
                array_push($result, $stock);
            }

            $ri = ReceiveDet::join('tblpotrx', 'tblreceivedet.trx_id', 'tblpotrx.id')->where('prod_id', $product->prod_id)->select('id_jurnal', 'receive_date', 'qty')->get();

            foreach($ri AS $p){
                $tgl = $p['receive_date'];
                $trx_id = $p->id_jurnal;
                $status = "OUT";
                $total = $total - $p->qty;

                $stock = array(
                    'tanggal' => $tgl,
                    'trx_id'  => $trx_id,
                    'status'  => $status,
                    'qty'     => $p->qty,
                );
                array_push($result, $stock);
            }

            $date = array();
            foreach ($result as $key => $row){
                $date[$key] = $row['tanggal'];
            }
            array_multisort($date, SORT_DESC, $result);

            // return view('product.controlling.mutasi_produk', compact('product','result', 'total'));
            return response()->json(view('product.controlling.modal',compact('product','result','total','modal'))->render());
        }else{
            $products = Product::all();

            return view('product.controlling.index',compact('products'));
        }
    }

    public function mutasiBrgGudang(Request $request){
        if($request->ajax()){
            $modal = "mutasi";
            $product = Product::join('tblperusahaan', 'tblproduct.supplier', 'tblperusahaan.id')->where('tblproduct.id', $request->id)->select('tblproduct.id', 'name', 'prod_id', 'tblperusahaan.nama AS supplier')->first();
            $result = array();
            $total = 0;

            // STOCK AWAL, kata bos nggak usah
            // $stock_awal = Product::where('prod_id',$product->prod_id)->first()->stock;
            // $total = $total + $stock_awal;
            // if($stock_awal!=0){
            //     $stockawal = array(
            //         'tanggal'   => "",
            //         'trx_id'    => "Stok Awal",
            //         'status'    => "IN",
            //         'qty'       => $stock_awal,
            //     );
            //     array_push($result, $stockawal);
            // }

            $ri = ReceiveDet::join('tblpotrx', 'tblreceivedet.trx_id', 'tblpotrx.id')->where('prod_id', $product->prod_id)->select('id_jurnal', 'receive_date', 'qty')->get();

            foreach($ri AS $p){
                $tgl = $p['receive_date'];
                $trx_id = $p->id_jurnal;
                $status = "IN";
                $total = $total + $p->qty;

                $stock = array(
                    'tanggal' => $tgl,
                    'trx_id'  => $trx_id,
                    'status'  => $status,
                    'qty'     => $p->qty,
                );
                array_push($result, $stock);
            }

            $do = DeliveryDetail::join('delivery_order', 'delivery_detail.do_id', 'delivery_order.id')->where('product_id', $product->prod_id)->select('jurnal_id', 'date', 'qty')->get();

            foreach($do AS $d){
                $tgl = $d['date'];
                $trx_id = $d->jurnal_id;
                $status = "OUT";
                $total = $total - $d->qty;

                $stock = array(
                    'tanggal' => $tgl,
                    'trx_id'  => $trx_id,
                    'status'  => $status,
                    'qty'     => $d->qty,
                );
                array_push($result, $stock);
            }

            $date = array();
            foreach ($result as $key => $row){
                $date[$key] = $row['tanggal'];
            }
            array_multisort($date, SORT_DESC, $result);

            // return view('product.controlling.mutasi_produk', compact('product','result', 'total'));
            return response()->json(view('product.controlling.modal',compact('product','result','total','modal'))->render());
        }else{
            $products = Product::all();

            return view('product.controlling.index',compact('products'));
        }
    }

    public function mutasiBrgCustomer(Request $request){
        if($request->ajax()){
            $modal = "mutasi";
            $product = Product::join('tblperusahaan', 'tblproduct.supplier', 'tblperusahaan.id')->where('tblproduct.id', $request->id)->select('tblproduct.id', 'name', 'prod_id', 'tblperusahaan.nama AS supplier')->first();
            $result = array();
            $total = 0;

            $so = SalesDet::join('tblproducttrx', 'tblproducttrxdet.trx_id', 'tblproducttrx.id')->where('prod_id', $product->prod_id)->where('jurnal_id', '!=', '0')->select('jurnal_id','trx_date', 'qty')->get();

            foreach($so AS $s){
                $tgl = $s['trx_date'];
                $trx_id = $s->jurnal_id;
                $status = "IN";
                $total = $total + $s->qty;

                $stock = array(
                    'tanggal' => $tgl,
                    'trx_id'  => $trx_id,
                    'status'  => $status,
                    'qty'     => $s->qty,
                );
                array_push($result, $stock);
            }

            $do = DeliveryDetail::join('delivery_order', 'delivery_detail.do_id', 'delivery_order.id')->where('product_id', $product->prod_id)->select('jurnal_id', 'date', 'qty')->get();

            foreach($do AS $d){
                $tgl = $d['date'];
                $trx_id = $d->jurnal_id;
                $status = "OUT";
                $total = $total - $d->qty;

                $stock = array(
                    'tanggal' => $tgl,
                    'trx_id'  => $trx_id,
                    'status'  => $status,
                    'qty'     => $d->qty,
                );
                array_push($result, $stock);
            }

            $date = array();
            foreach ($result as $key => $row){
                $date[$key] = $row['tanggal'];
            }
            array_multisort($date, SORT_DESC, $result);

            // return view('product.controlling.mutasi_produk', compact('product','result', 'total'));
            return response()->json(view('product.controlling.modal',compact('product','result','total','modal'))->render());
        }else{
            $products = Product::all();

            return view('product.controlling.index',compact('products'));
        }
    }

    public function customerStock(Request $request){
        if($request->ajax()){
            $customer = Customer::where('id',$request->customer)->select('apname','id')->first();
            $stocks = Sales::customerStock($request->customer);

            return response()->json(view('product.customer.view',compact('customer','stocks'))->render());
        }else {
            $customers = Customer::all();

            return view('product.customer.index',compact('customers'));
        }
    }
}
