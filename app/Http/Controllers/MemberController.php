<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use App\Member;
use App\DataKota;
use App\Koordinator;
use App\SubKoordinator;
use App\Perusahaan;
use App\Bank;

use DataTables;

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
        return view('member.index',compact('keyword','perusahaan','bank'));
    }

    public function ajxmember(Request $request){
        $keyword = $request->get('search');
        $jenis = $request->get('jenis');
        $perusahaan = $request->get('perusahaan');
        $bank = $request->get('bank');
        
        if($jenis == 0){
            $datas = Member::select('tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak')->join('perusahaanmember','tblmember.ktp','=','perusahaanmember.ktp')->where('perusahaanmember.perusahaan_id',$perusahaan)->where('tblmember.nama','LIKE',$keyword.'%')->where('tblmember.ktp','LIKE',$keyword.'%')->orderBy('tblmember.nama')->paginate(10);
        }elseif ($jenis == 1) {
            $datas = Member::select('tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak')->join('perusahaanmember','tblmember.ktp','=','perusahaanmember.ktp')->whereNotIn('tblmember.ktp',DB::raw("SELECT m.ktp FROM perusahaanmember p INNER JOIN tblmember m ON m.ktp = p.ktp WHERE p.id = $perusahaan"))->where('tblmember.nama','LIKE',$keyword.'%')->where('tblmember.ktp','LIKE',$keyword.'%')->orderBy('tblmember.nama')->paginate(10);
        }elseif($jenis==2){
            $datas = Member::select('ktp','nama','scanktp','cetak')->where('tblmember.nama','LIKE',$keyword.'%')->where('tblmember.ktp','LIKE',$keyword.'%')->orderBy('tblmember.nama')->paginate(2);
        }elseif ($jenis == 3) {
            $datas = Member::select('tblmember.ktp','tblmember.nama','tblmember.scanktp','tblmember.cetak')->join('bankmember','tblmember.ktp','=','bankmember.ktp')->where('bankmember.bank_id',$bank)->where('tblmember.nama','LIKE',$keyword.'%')->where('tblmember.ktp','LIKE',$keyword.'%')->orderBy('tblmember.nama')->paginate(10);
        }
        $datas->withPath('yourPath');
        $datas->appends($request->all());

        if ($request->ajax()) {
            return response()->json(view('member.list',compact('datas'))->render());
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
            'tgl_lahir' => 'required|string',
            'prov' => 'required|email|unique:tblemployee',
            'city' => 'required',
            'ibu' => 'required',
            'koordinator' => 'required',
            'subkoor' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{

            //setup member id
            $month=	date("m");
            $year=	date("y");
            $last_member = Member::lastMember();

            $member_id=substr($last_member,12);
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

            // Upload KTP
            $scanktp = "noimage.jpg";

            if($request->scanktp <> NULL|| $request->scanktp <> ''){
                $scanktp = $request->ktp.'.'.$request->scanktp->getClientOriginalExtension();
                $request->scanktp->move(public_path('assets/images/member/ktp/'),$scanktp);
            }

            $member = new Employee(array(
                'member_id' => $member_id,
                'koordinator' => $request->koordinator,
                'scanktp' => $scanktp,
                'ktp' => $request->ktp,
                'subkoor' => $request->subkoor,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'ibu' => $request->ibu,
                'creator' => session('user_id'),
                'status' => 'RWH',
                'cetak' => 0,
                'prov' => $request->prov,
                'city' => $request->city,

            ));
            // success
            if($member->save()){
                return redirect()->route('employee.index')->with('status', 'Data berhasil dibuat');
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
        $jenis = "edit";
        $provinsi = DataKota::getProvinsi();
        $koordinator = Koordinator::all();
        $subkoor = SubKoordinator::all();
        $member = Member::where('id',$id)->first();
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
            'tgl_lahir' => 'required|string',
            'prov' => 'required|email|unique:tblemployee',
            'city' => 'required',
            'ibu' => 'required',
            'koordinator' => 'required',
            'subkoor' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{

            $datamember = Member::where('id',$id)->first();

            // Upload KTP
            if($request->scanktp <> NULL|| $request->scanktp <> ''){

                if (file_exists(public_path('assets/images/member/ktp/').$datamember->scanktp)) {
                    unlink(public_path('assets/images/member/ktp/').$datamember->scanktp);
                }

                $scanktp = $request->ktp.'.'.$request->scanktp->getClientOriginalExtension();
                $request->scanktp->move(public_path('assets/images/member/ktp/'),$scanktp);
            }

                $datamember->koordinator = $request->koordinator;
                $datamember->scanktp = $scanktp;
                $datamember->ktp = $request->ktp;
                $datamember->subkoor = $request->subkoor;
                $datamember->nama = $request->nama;
                $datamember->alamat = $request->alamat;
                $datamember->telp = $request->telp;
                $datamember->tempat_lahir = $request->tempat_lahir;
                $datamember->tgl_lahir = $request->tgl_lahir;
                $datamember->ibu = $request->ibu;
                $datamember->creator = session('user_id');
                $datamember->prov = $request->prov;
                $datamember->city = $request->city;
            // success
            if($member->save()){
                return redirect()->route('employee.index')->with('status', 'Data berhasil diubah');
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
        //
    }
}
