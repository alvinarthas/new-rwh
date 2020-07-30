<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;
use Excel;
use PDF;
use App\Exports\MemberExport2;
use App\Member;
use App\DataKota;
use App\Koordinator;
use App\SubKoordinator;
use App\Perusahaan;
use App\Bank;
use App\PerusahaanMember;
use App\BankMember;
use App\StatusRek;
use App\MenuMapping;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perusahaan = Perusahaan::orderBy('nama')->get();
        $bank = Bank::bankMember();
        $page = MenuMapping::getMap(session('user_id'),"MBMM");

        return view('member.index',compact('keyword','perusahaan','bank','page'));
    }

    public function index_new(Request $request)
    {
        if ($request->ajax()) {
            $jenis = $request->jenis;
            $perusahaan = $request->perusahaan;
            $bank = $request->bank;
            return response()->json(view('member.list_baru',compact('jenis','perusahaan','bank'))->render());
        }else{
            $perusahaan = Perusahaan::orderBy('nama')->get();
            $bank = Bank::bankMember();
            $statusrek = StatusRek::all();
            $page = MenuMapping::getMap(session('user_id'),"MBMM");

            return view('member.index_baru',compact('statusrek','perusahaan','bank','page'));
        }
    }

    public function getDataMember(Request $request){
        if($request->ajax()){
            $datas = Member::viewMember($request);
            echo json_encode($datas);
        }
    }

    public function ajxmember(Request $request){
        $keyword = $request->get('search');
        $jenis = $request->get('jenis');
        $perusahaan = $request->get('perusahaan');
        $bank = $request->get('bank');
        $key = substr($keyword, 0, 4);
        $word = substr($keyword, 5);

        // echo $key." . ".$word;
        // die();

        if($key == "nrek"){
            if($jenis == 1){
                $array = array_values(array_column(DB::select("SELECT ktp FROM perusahaanmember WHERE perusahaan_id = $perusahaan"),'ktp'));

                $datas = Member::join('bankmember', 'tblmember.ktp', 'bankmember.ktp')->whereIn('tblmember.ktp',$array)->select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak', 'bankmember.norek');
            }elseif ($jenis == 2) {
                $array = array_values(array_column(DB::select("SELECT ktp FROM perusahaanmember WHERE perusahaan_id = $perusahaan"),'ktp'));

                $datas = Member::join('bankmember', 'tblmember.ktp', 'bankmember.ktp')->whereNotIn('tblmember.ktp',$array)->select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak', 'bankmember.norek');
            }elseif($jenis==3 || $jenis==null){
                // $datas = Member::select('id','ktp','nama','scanktp','cetak');
                $datas = Member::join('bankmember', 'tblmember.ktp', 'bankmember.ktp')->select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak','bankmember.norek');

            }elseif ($jenis == 4) {
                $array = array_values(array_column(DB::select("SELECT ktp FROM bankmember WHERE bank_id =$bank"),'ktp'));

                // $datas = Member::whereIn('ktp',$array)->select('id','ktp','nama','scanktp','cetak');

                $datas = Member::join('bankmember', 'tblmember.ktp', 'bankmember.ktp')->whereIn('tblmember.ktp',$array)->select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak', 'bankmember.norek');
            }

            if($keyword <> ''){
                // $datas = $datas->where('nama','LIKE',$keyword.'%')->OrWhere('ktp','LIKE',$keyword.'%');
                $datas = $datas->where('tblmember.nama','LIKE',$word.'%')->OrWhere('tblmember.ktp','LIKE',$word.'%')->orWhere('bankmember.norek', 'LIKE', $word.'%');
            }
        }else{
            if($jenis == 1){
                $array = array_values(array_column(DB::select("SELECT ktp FROM perusahaanmember WHERE perusahaan_id = $perusahaan"),'ktp'));

                $datas = Member::whereIn('ktp',$array)->select('id','ktp','nama','scanktp','cetak');
            }elseif ($jenis == 2) {
                $array = array_values(array_column(DB::select("SELECT ktp FROM perusahaanmember WHERE perusahaan_id = $perusahaan"),'ktp'));

                $datas = Member::whereNotIn('ktp',$array)->select('id','ktp','nama','scanktp','cetak');
            }elseif($jenis==3 || $jenis==null){
                $datas = Member::select('id','ktp','nama','scanktp','cetak');
            }elseif ($jenis == 4) {
                $array = array_values(array_column(DB::select("SELECT ktp FROM bankmember WHERE bank_id =$bank"),'ktp'));

                $datas = Member::whereIn('ktp',$array)->select('id','ktp','nama','scanktp','cetak');
            }

            if($keyword <> ''){
                $datas = $datas->where('nama','LIKE',$word.'%')->OrWhere('ktp','LIKE',$word.'%');
                // $datas = $datas->where('tblmember.nama','LIKE',$keyword.'%')->OrWhere('tblmember.ktp','LIKE',$keyword.'%')->orWhere('bankmember.norek', 'LIKE', $keyword.'%');
            }
        }

        $datas = $datas->orderBy('nama')->paginate(10);

        $datas->withPath('yourPath');
        $datas->appends($request->all());
        if ($request->ajax()) {
            return response()->json(view('member.list',compact('datas','jenis','perusahaan','bank'))->render());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        $provinsi = DataKota::getProvinsi();
        $koordinator = Koordinator::all();
        $subkoor = SubKoordinator::all();
        return view('member.form',compact('provinsi','koordinator','subkoor','jenis'));
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
            'nama' => 'required|string',
            'alamat' => 'required',
            'telp' => 'required|string',
            'ktp' => 'required|string',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|string',
            'ibu' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                //setup member id
                $month=	date("m");
                $year=	date("y");
                $last_member = Member::lastMember();
                if($last_member){
                    $member_id=substr($last_member->member_id,12);
                    $member_id=intval($member_id);
                    $member_id=$member_id+2;

                    $leng = strlen($member_id);

                    switch ($leng)
                    {
                        case 4:
                        $member_id="RWHMB".$month.$year."000".$member_id;
                        break;
                        case 5:
                        $member_id="RWHMB".$month.$year."00".$member_id;
                        break;
                        case 6:
                        $member_id="RWHMB".$month.$year."0".$member_id;
                        break;
                        case 7:
                        $member_id="RWHMB".$month.$year.$member_id;
                        break;
                    }
                }else{
                    $member_id="RWHMB".$month.$year."0001001";
                }


                // Upload KTP
                $scanktp = "noimage.jpg";

                if($request->scanktp <> NULL|| $request->scanktp <> ''){
                    $scanktp = $request->ktp.'.'.$request->scanktp->getClientOriginalExtension();
                    $request->scanktp->move(public_path('assets/images/member/ktp/'),$scanktp);
                }

                $member = new Member(array(
                    'member_id' => $member_id,
                    'koordinator' => $request->koordinator,
                    'scanktp' => $scanktp,
                    'ktp' => $request->ktp,
                    'subkoor' => $request->subkoor,
                    'nama' => $request->nama,
                    'alamat' => $request->alamat,
                    'telp' => $request->telp,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir' => $request->tanggal_lahir,
                    'ibu' => $request->ibu,
                    'creator' => session('user_id'),
                    'status' => 'RWH',
                    'cetak' => 0,
                    'prov' => $request->prov,
                    'city' => $request->city,

                ));
                // success
                    $member->save();
                    return redirect()->route('member.index')->with('status', 'Data berhasil diubah');
            }catch(\Exception $e){
                return redirect()->back()->withErrors($e->getMessage());
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
        $member = Member::where('ktp',$id)->first();
        $provinsi = DataKota::getProvinsi();
        $koordinator = Koordinator::all();
        $subkoor = SubKoordinator::all();
        $bankmember = BankMember::where('ktp',$id)->get();
        $perusahaanmember = PerusahaanMember::where('ktp',$id)->get();
        $page = MenuMapping::getMap(session('user_id'),"MBMM");

        return view('member.show',compact('provinsi','koordinator','subkoor','member','bankmember','perusahaanmember','page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jenis = "edit";
        $provinsi = DataKota::getProvinsi();
        $koordinator = Koordinator::all();
        $subkoor = SubKoordinator::all();
        $member = Member::where('ktp',$id)->first();
        return view('member.form',compact('provinsi','koordinator','subkoor','member','jenis'));
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
            'nama' => 'required|string',
            'alamat' => 'required',
            'telp' => 'required|string',
            'ktp' => 'required|string',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'ibu' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{

            $datamember = Member::where('ktp',$id)->first();

            // Upload KTP
            if($request->scanktp <> NULL|| $request->scanktp <> ''){

                if (file_exists(public_path('assets/images/member/ktp/').$datamember->scanktp)) {
                    unlink(public_path('assets/images/member/ktp/').$datamember->scanktp);
                }

                $scanktp = $request->ktp.'.'.$request->scanktp->getClientOriginalExtension();
                $request->scanktp->move(public_path('assets/images/member/ktp/'),$scanktp);
            }else{
                $scanktp = $datamember->scanktp;
            }

                $datamember->koordinator = $request->koordinator;
                $datamember->scanktp = $scanktp;
                $datamember->ktp = $request->ktp;
                $datamember->subkoor = $request->subkoor;
                $datamember->nama = $request->nama;
                $datamember->alamat = $request->alamat;
                $datamember->telp = $request->telp;
                $datamember->tempat_lahir = $request->tempat_lahir;
                $datamember->tgl_lahir = $request->tanggal_lahir;
                $datamember->ibu = $request->ibu;
                $datamember->creator = session('user_id');
                $datamember->prov = $request->prov;
                $datamember->city = $request->city;

            // success
            try{
                $datamember->save();
                return redirect()->route('member.index')->with('status', 'Data berhasil diubah');
            }catch(\Exception $e){
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
        //
    }

    public function ajxMemberOrder(Request $request){
        $keyword = strip_tags(trim($request->keyword));
        $key = $keyword.'%';
        $perusahaan = $request->perusahaanid;
        $bank = $request->bankid;
        $jenis = $request->jenis;

        if($jenis == 1){
            $datas = Member::select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak')->join('perusahaanmember','tblmember.ktp','=','perusahaanmember.ktp')->where('perusahaanmember.perusahaan_id',$perusahaan)->where('tblmember.nama','LIKE',$key)->orWhere('tblmember.ktp','LIKE',$key)->orderBy('tblmember.nama')->distinct()->limit(5)->get();
        }elseif ($jenis == 2) {
            $dd = DB::select("SELECT m.ktp FROM perusahaanmember p INNER JOIN tblmember m ON m.ktp = p.ktp WHERE p.id = $perusahaan");
            $dd = json_decode(json_encode($dd), true);

            $datas = Member::select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak')->join('perusahaanmember','tblmember.ktp','=','perusahaanmember.ktp')->whereNotIn('tblmember.ktp',$dd)->where('tblmember.nama','LIKE',$key)->orWhere('tblmember.ktp','LIKE',$key)->orderBy('tblmember.nama')->distinct()->limit(5)->get();
        }elseif($jenis==3){
            $datas = Member::select('id','ktp','nama','scanktp','cetak')->where('tblmember.nama','LIKE',$key)->orWhere('tblmember.ktp','LIKE',$key)->orderBy('tblmember.nama')->distinct()->limit(5)->get();
        }elseif ($jenis == 4) {
            $datas = Member::select('tblmember.id','tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak')->join('bankmember','tblmember.ktp','=','bankmember.ktp')->where('bankmember.bank_id',$bank)->where('tblmember.nama','LIKE',$key.'%')->orWhere('tblmember.ktp','LIKE',$key.'%')->orderBy('tblmember.nama')->distinct()->limit(5)->get();
        }

        $data = array();
        $array = json_decode( json_encode($datas), true);
        foreach ($array as $a) {
            $arrayName = array('id' =>$a['id'],'ktp' => $a['ktp'], 'nama' => $a['nama']);
            array_push($data,$arrayName);
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function ajxAddRowCetak(Request $request){
        $id_member = $request->id_member;
        $count = $request->count;

        $member = Member::where('id', $id_member)->first();
        $nama = $member->nama;
        $member_id = $member->member_id;
        $ktp = $member->ktp;
        $alamat = $member->alamat;
        $ttl = $member->tempat_lahir.", ".$member->tgl_lahir;
        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td><input type="hidden" name="nama[]" id="nama'.$count.'" value="'.$nama.'">'.$nama.'</td>
        <td><input type="hidden" name="id_member[]" id="id_member'.$count.'" value="'.$member_id.'">'.$member_id.'</td>
        <td><input type="hidden" name="ktp[]" id="ktp'.$count.'" value="'.$ktp.'">'.$ktp.'</td>
        <td><input type="hidden" name="alamat[]" id="alamat'.$count.'" value="'.$alamat.'">'.$alamat.'</td>
        <td><input type="hidden" name="ttl[]" id="ttl'.$count.'" value="'.$ttl.'">'.$ttl.'</td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect waves-danger" onclick="deleteItem('.$count.')" >x</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
        );

        return response()->json($data);
    }

    public function exportMember(Request $request)
    {
        ini_set('max_execution_time', 3000);
        $i = 0;
        $jenis = $request['jns'];
        $perusahaan = $request['prs'];
        $bank = $request['bnk'];
        $menu = $request['menu'];
        $exportTo = $request['xto'];

        if($menu==0){
            if($jenis == 1){
                $datas = PerusahaanMember::where('perusahaan_id', $perusahaan)->join('tblmember','tblmember.ktp', '=', 'perusahaanmember.ktp')->leftJoin('tblkoordinator', 'tblkoordinator.id', '=', 'tblmember.koordinator')->leftJoin('tblsubkoordinator', 'tblsubkoordinator.id', '=', 'tblmember.subkoor')->select('tblmember.member_id','tblmember.ktp','tblmember.nama as namaMember','tblmember.alamat','tblmember.tempat_lahir', 'tblmember.tgl_lahir', 'tblkoordinator.nama AS namaKoor', 'tblsubkoordinator.nama AS namaSubkoor')->orderBy('tblmember.nama')->get();
            }elseif ($jenis == 2) {
                $datas = PerusahaanMember::where('perusahaan_id', '!=', $perusahaan)->join('tblmember','tblmember.ktp', '=', 'perusahaanmember.ktp')->leftJoin('tblkoordinator', 'tblkoordinator.id', '=', 'tblmember.koordinator')->leftJoin('tblsubkoordinator', 'tblsubkoordinator.id', '=', 'tblmember.subkoor')->select('tblmember.member_id','tblmember.ktp','tblmember.nama as namaMember','tblmember.alamat','tblmember.tempat_lahir', 'tblmember.tgl_lahir', 'tblkoordinator.nama AS namaKoor', 'tblsubkoordinator.nama AS namaSubkoor')->orderBy('tblmember.nama')->get();
            }elseif($jenis == 3 || $jenis == null){
                $datas = Member::leftJoin('tblkoordinator', 'tblkoordinator.id', '=', 'tblmember.koordinator')->leftJoin('tblsubkoordinator', 'tblsubkoordinator.id', '=', 'tblmember.subkoor')->select('tblmember.member_id','tblmember.ktp','tblmember.nama as namaMember','tblmember.alamat','tblmember.tempat_lahir', 'tblmember.tgl_lahir', 'tblkoordinator.nama AS namaKoor', 'tblsubkoordinator.nama AS namaSubkoor')->orderBy('tblmember.nama')->get();
            }elseif ($jenis == 4) {
                $datas = BankMember::where('bank_id', $bank)->join('tblmember', 'tblmember.ktp', '=', 'bankmember.ktp')->leftJoin('tblkoordinator', 'tblkoordinator.id', '=', 'tblmember.koordinator')->leftJoin('tblsubkoordinator', 'tblsubkoordinator.id', '=', 'tblmember.subkoor')->select('tblmember.member_id','tblmember.ktp','tblmember.nama as namaMember','tblmember.alamat','tblmember.tempat_lahir', 'tblmember.tgl_lahir', 'tblkoordinator.nama AS namaKoor', 'tblsubkoordinator.nama AS namaSubkoor')->orderBy('tblmember.nama')->get();
            }
        }

        if($exportTo == 0){
            $data = array();
            if($menu==0){
                foreach($datas as $m){
                    $i++;
                    $perusahaanmember = PerusahaanMember::where('ktp', $m->ktp)->get();
                    $perusahaans = "";
                    foreach($perusahaanmember as $pm){
                        if($pm->perusahaan_id!=0){
                            $perusahaan = Perusahaan::where('id', $pm->perusahaan_id)->first()->nama;
                            $noid = $perusahaan.' '.$pm->noid;
                        }else{
                            $noid = "";
                        }
                        $perusahaans = $perusahaans."".$noid.", \n";
                    }

                    $bankmember = BankMember::where('ktp', $m->ktp)->get();
                    $rekenings = "";
                    foreach($bankmember as $bm){
                        $bank = Bank::where('id', $bm->bank_id)->first()->nama;
                        $rekening = $bank.' '.$bm->norek;
                        $statusrek = $bm->status;
                        $rekenings = $rekenings."".$rekening."(".$statusrek."), \n";
                    }

                    $ttl = $m['tempat_lahir'].", ".$m['tgl_lahir'];

                    $array = array(
                        // Data Member
                        'No' => $i,
                        'Nama' => $m['namaMember'],
                        'ID_Member' => $m['member_id'],
                        'No_KTP' => $m['ktp'],
                        'alamat' => $m['alamat'],
                        'TTL' => $ttl,
                        'Koordinator' => $m['namaKoor'],
                        'Sub Koordinator' => $m['namaSubkoor'],
                        'Bank Member' => $rekenings,
                        'Perusahaan Member' => $perusahaans,
                    );
                    array_push($data, $array);
                }
            }elseif($menu==1){
                $data = array();
                foreach($request['id_member'] as $idm){
                    $i++;
                    $member = Member::where('member_id', $idm)->first();
                    $perusahaanmember = PerusahaanMember::where('ktp', $member->ktp)->get();
                    $perusahaans = "";
                    foreach($perusahaanmember as $pm){
                        if($pm->perusahaan_id!=0){
                            $perusahaan = Perusahaan::where('id', $pm->perusahaan_id)->first()->nama;
                            $noid = $perusahaan.' '.$pm->noid;
                        }else{
                            $noid = "";
                        }
                        $perusahaans = $perusahaans."".$noid.", \n";
                    }

                    $bankmember = BankMember::where('ktp', $member->ktp)->get();
                    $rekenings = "";
                    foreach($bankmember as $bm){
                        $bank = Bank::where('id', $bm->bank_id)->first()->nama;
                        $rekening = $bank.' '.$bm->norek;
                        $statusrek = $bm->status;
                        $rekenings = $rekenings."".$rekening."(".$statusrek."), \n";
                    }

                    if(!empty($member->koordinator)){
                        $namaKoor = Koordinator::where('id', $member->koordinator)->first()->nama;
                    }else{
                        $namaKoor = "";
                    }

                    if(!empty($member->subkoor)){
                        $namaSubkoor = Subkoordinator::where('id', $member->subkoor)->first()->nama;
                    }else{
                        $namaSubkoor = "";
                    }

                    $ttl = $member->tempat_lahir.", ".$member->tgl_lahir;
                    $array = array(
                        // Data Member
                        'No' => $i,
                        'Nama' => $member->nama,
                        'ID_Member' => $idm,
                        'No_KTP' => $member->ktp,
                        'alamat' => $member->alamat,
                        'TTL' => $ttl,
                        'Koordinator' => $namaKoor,
                        'Sub Koordinator' => $namaSubkoor,
                        'Bank Member' => $rekenings,
                        'Perusahaan Member' => $perusahaans,
                    );
                    // echo "<pre>";
                    // print_r($array);
                    // die();
                    array_push($data, $array);
                }
            }
            $export = new MemberExport2($data);
            return Excel::download($export, 'Member.xlsx');
        }elseif($exportTo == 1){
            if($menu == 0){
                $data = array();
                $count = 0;
                $pdfke = 0;

                if($datas->count() > 100){
                    foreach($datas as $d){
                        $memb = Member::where('member_id', $d['member_id'])->first();
                        array_push($data, $memb);
                        $count++;
                        if($count == 100){
                            $pdfke++;
                            $namafile = 'daftar-member-'.$pdfke.'.pdf';
                            $pdf = PDF::loadview('member.pdfmember',['member'=>$data])->setPaper('a4', 'landscape');
                            $pdf->save(public_path('download/'.$namafile));
                            $data = array();
                            $count = 0;
                            // echo 'Download File: <a href="'.public_path('download/'.$namafile).'">'.$namafile.'</a><br>';
                        }
                    }
                    $files = glob(public_path('download/*'));
                    \Zipper::make(public_path('daftar-member.zip'))->add($files)->close();
                    return response()->download(public_path('daftar-member.zip'));
                }else{
                    $pdf = PDF::loadview('member.pdfmember',['member'=>$datas])->setPaper('a4', 'landscape');
                    return $pdf->download('daftar-member.pdf');
                }
            }elseif($menu==1){
                $data = array();
                foreach($request['id_member'] as $idm){
                    $memb = Member::where('member_id', $idm)->first();
                    array_push($data, $memb);
                }
                $pdf = PDF::loadview('member.pdfmember',['member'=>$data])->setPaper('a4', 'landscape');
                return $pdf->download('daftar-member.pdf');
            }
        }
    }

    public function makeSynch()
    {
        $member = Member::select('nama', 'tgl_lahir', 'member_id', 'koordinator', 'subkoor')->where('status', 'RWH')->orderBy('nama', 'asc')->get();

        return view('member.synch',compact('member'));
    }
}
