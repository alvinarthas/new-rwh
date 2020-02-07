<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Exceptions\Handler;

use App\Employee;
use App\Bank;
use App\DemoUser;
use Carbon\Carbon;
use App\MenuMapping;
use App\Log;

class EmployeeController extends Controller
{
    public function index(){
        $employee = Employee::all();
        $page = MenuMapping::getMap(session('user_id'),"EMEM");
        return view('employee.index',compact('employee','page'));
    }

    public function create(){
        $banks = Bank::all();
        $jenis = "create";
        return view('employee.form',compact('banks','jenis'));
    }

    public function store(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required',
            'nama' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required',
            'ktp' => 'required|string',
            'email' => 'required|email|unique:tblemployee',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'mulai_kerja' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            if($request->nip == ''){
                $rowmember = Employee::where('mulai_kerja',$request->mulai_kerja)->count();
                $tgl = Carbon::createFromFormat('Y-m-d',$request->mulai_kerja)->format('Ymd');
                $nip = "Royal".$tgl.$rowmember+1;
            }else{
                $nip = $request->nip;
            }
            $scanfoto = "noimage.jpg";
            $scanktp = "noimage.jpg";
            $scansima = "noimage.jpg";
            $scansimb = "noimage.jpg";
            $scansimc = "noimage.jpg";
            $scannpwp = "noimage.jpg";
            $scanbpjs = "noimage.jpg";
            // Upload Foto
            if($request->scanfoto <> NULL|| $request->scanfoto <> ''){
                $scanfoto = $request->nip.'.'.$request->scanfoto->getClientOriginalExtension();
                $request->scanfoto->move(public_path('assets/images/employee/foto/'),$scanfoto);
            }

            // Upload KTP
            if($request->scanktp <> NULL|| $request->scanktp <> ''){
                $scanktp = $request->nip.'.'.$request->scanktp->getClientOriginalExtension();
                $request->scanktp->move(public_path('assets/images/employee/ktp/'),$scanktp);
            }

            // Upload SIM A
            if($request->scansima <> NULL|| $request->scansima <> ''){
                $scansima = $request->nip.'.'.$request->scansima->getClientOriginalExtension();
                $request->scansima->move(public_path('assets/images/employee/sima/'),$scansima);
            }

            // Upload SIM B
            if($request->scansimb <> NULL|| $request->scansimb <> ''){
                $scansimb = $request->nip.'.'.$request->scansimb->getClientOriginalExtension();
                $request->scansimb->move(public_path('assets/images/employee/simb/'),$scansimb);
            }

            // Upload SIM C
            if($request->scansimc <> NULL|| $request->scansimc <> ''){
                $scansimc = $request->nip.'.'.$request->scansimc->getClientOriginalExtension();
                $request->scansimc->move(public_path('assets/images/employee/simc/'),$scansimc);
            }

            // Upload NPWP
            if($request->scannpwp <> NULL|| $request->scannpwp <> ''){
                $scannpwp = $request->nip.'.'.$request->scannpwp->getClientOriginalExtension();
                $request->scannpwp->move(public_path('assets/images/employee/npwp/'),$scannpwp);
            }

            // Upload BPJS
            if($request->scanbpjs <> NULL|| $request->scanbpjs <> ''){
                $scanbpjs = $request->nip.'.'.$request->scanbpjs->getClientOriginalExtension();
                $request->scanbpjs->move(public_path('assets/images/employee/bpjs/'),$scanbpjs);
            }

            $employee = new Employee(array(
                // Informasi Pribadi
                'name' => $request->nama,
                'nip' => $nip,
                'address' => $request->alamat,
                'phone' => $request->telepon,
                'email' => $request->email,
                'tmpt_lhr' => $request->tempat_lahir,
                'tgl_lhr' => $request->tanggal_lahir,
                'mulai_kerja' => $request->mulai_kerja,
                'scanfoto' => $scanfoto,
                // Account
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'bck_pass' => $request->password,
                'login_status' => 1,
                // Informasi Tabungan
                'bank' => $request->bank,
                'norek' => $request->rekening,
                // Informasi KTP
                'ktp' => $request->ktp,
                'scanktp' => $scanktp,
                // Informasi SIM A
                'sima' => $request->sima,
                'scansima' => $scansima,
                // Informasi SIM C
                'simc' => $request->simc,
                'scansimc' => $scansimc,
                // Informasi SIM B
                'simb' => $request->simb,
                'scansimb' => $scansimb,
                // Informasi NPWP
                'npwp' => $request->npwp,
                'scannpwp' => $scannpwp,
                // Informasi BPJS
                'bpjs' => $request->bpjs,
                'scanbpjs' => $scanbpjs,
            ));
            // success
            if($employee->save()){

                $fingeruser = new DemoUser(array(
                    'user_id' => $employee->id,
                    'user_name' => $employee->username
                ));

                $fingeruser->save();

                Log::setLog('EMEMC','Create Employee: '.$request->nama);

                return redirect()->route('employee.index')->with('status', 'Data berhasil dibuat');
            // fail
            }else{
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function edit($id){
        $employee = Employee::where('id',$id)->first();
        $banks = Bank::all();
        $jenis = "edit";
        return view('employee.form',compact('banks','jenis','employee'));
    }

    public function update(Request $request, $id){
        // Validate
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'nip' => 'required|string',
            'alamat' => 'required|string',
            'telepon' => 'required',
            'ktp' => 'required|string',
            'email' => 'required|email',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'mulai_kerja' => 'required',
        ]);

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{

        try {
            $employee = Employee::where('id',$id)->first();

            // Upload Foto
            if($request->scanfoto <> NULL|| $request->scanfoto <> ''){

                if (file_exists(public_path('assets/images/employee/foto/').$employee->scanfoto) && $employee->scanfoto != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/foto/').$employee->scanfoto);
                }

                $scanfoto = $request->nip.'.'.$request->scanfoto->getClientOriginalExtension();
                $request->scanfoto->move(public_path('assets/images/employee/foto/'),$scanfoto);
            }else{
                $scanfoto = $employee->scanfoto;
            }

            // Upload KTP
            if($request->scanktp <> NULL|| $request->scanktp <> ''){

                if (file_exists(public_path('assets/images/employee/ktp/').$employee->scanktp)  && $employee->scanktp != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/ktp/').$employee->scanktp);
                }

                $scanktp = $request->nip.'.'.$request->scanktp->getClientOriginalExtension();
                $request->scanktp->move(public_path('assets/images/employee/ktp/'),$scanktp);
            }else{
                $scanktp = $employee->scanktp;
            }

            // Upload SIM A
            if($request->scansima <> NULL|| $request->scansima <> ''){

                if (file_exists(public_path('assets/images/employee/sima/').$employee->scansima) && $employee->scansima != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/sima/').$employee->scansima);
                }

                $scansima = $request->nip.'.'.$request->scansima->getClientOriginalExtension();
                $request->scansima->move(public_path('assets/images/employee/sima/'),$scansima);
            }else{
                $scansima = $employee->scansima;
            }

            // Upload SIM B
            if($request->scansimb <> NULL|| $request->scansimb <> ''){

                if (file_exists(public_path('assets/images/employee/simb/').$employee->scansimb) && $employee->scansimb != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/simb/').$employee->scansimb);
                }

                $scansimb = $request->nip.'.'.$request->scansimb->getClientOriginalExtension();
                $request->scansimb->move(public_path('assets/images/employee/simb/'),$scansimb);
            }else{
                $scansimb = $employee->scansimb;
            }

            // Upload SIM C
            if($request->scansimc <> NULL|| $request->scansimc <> ''){

                if (file_exists(public_path('assets/images/employee/simc/').$employee->scansimc) && $employee->scansimc != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/simc/').$employee->scansimc);
                }

                $scansimc = $request->nip.'.'.$request->scansimc->getClientOriginalExtension();
                $request->scansimc->move(public_path('assets/images/employee/simc/'),$scansimc);
            }else{
                $scansimc = $employee->scansimc;
            }

            // Upload NPWP
            if($request->scannpwp <> NULL|| $request->scannpwp <> ''){

                if (file_exists(public_path('assets/images/employee/npwp/').$employee->scannpwp) && $employee->scannpwp != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/npwp/').$employee->scannpwp);
                }

                $scannpwp = $request->nip.'.'.$request->scannpwp->getClientOriginalExtension();
                $request->scannpwp->move(public_path('assets/images/employee/npwp/'),$scannpwp);
            }else{
                $scannpwp = $employee->scannpwp;
            }

            // Upload BPJS
            if($request->scanbpjs <> NULL|| $request->scanbpjs <> ''){

                if (file_exists(public_path('assets/images/employee/bpjs/').$employee->scanbpjs) && $employee->scanbpjs != "noimage.jpg") {
                    unlink(public_path('assets/images/employee/bpjs/').$employee->scanbpjs);
                }

                $scanbpjs = $request->nip.'.'.$request->scanbpjs->getClientOriginalExtension();
                $request->scanbpjs->move(public_path('assets/images/employee/bpjs/'),$scanbpjs);
            }else{
                $scanbpjs = $employee->scanbpjs;
            }

            // Informasi Pribadi
            $employee->name = $request->nama;
            $employee->nip = $request->nip;
            $employee->address = $request->alamat;
            $employee->phone = $request->telepon;
            $employee->email = $request->email;
            $employee->tmpt_lhr = $request->tempat_lahir;
            $employee->tgl_lhr = $request->tanggal_lahir;
            $employee->mulai_kerja = $request->mulai_kerja;
            $employee->scanfoto = $scanfoto;
            // Informasi Tabungan
            $employee->bank = $request->bank;
            $employee->norek = $request->rekening;
            // Informasi KTP
            $employee->ktp = $request->ktp;
            $employee->scanktp = $scanktp;
            // Informasi SIM A
            $employee->sima = $request->sima;
            $employee->scansima = $scansima;
            // Informasi SIM C
            $employee->simc = $request->simc;
            $employee->scansimc = $scansimc;
            // Informasi SIM B
            $employee->simb = $request->simb;
            $employee->scansimb = $scansimb;
            // Informasi NPWP
            $employee->npwp = $request->npwp;
            $employee->scannpwp = $scannpwp;
            // Informasi BPJS
            $employee->bpjs = $request->bpjs;
            $employee->scanbpjs = $scanbpjs;

            $employee->save();

            Log::setLog('EMEMU','Update Employee: '.$request->nama);

            return redirect()->route('employee.index')->with('status', 'Data berhasil dirubah');;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->errorInfo);
        }
            
        }
    }

    public function destroy($id){
        $employee = Employee::where('id',$id)->first();
        $name = $employee->name;
        if($employee->scanfoto != "noimage.jpg"){
            unlink(public_path('assets/images/employee/foto/').$employee->scanfoto);
        }
        if($employee->scanktp != "noimage.jpg"){
            unlink(public_path('assets/images/employee/ktp/').$employee->scanktp);
        }
        if($employee->scansima != "noimage.jpg"){
            unlink(public_path('assets/images/employee/sima/').$employee->scansima);
        }
        if($employee->scansimb != "noimage.jpg"){
            unlink(public_path('assets/images/employee/simb/').$employee->scansimb);
        }
        if($employee->scansimc != "noimage.jpg"){
            unlink(public_path('assets/images/employee/simc/').$employee->scansimc);
        }
        if($employee->scannpwp != "noimage.jpg"){
            unlink(public_path('assets/images/employee/npwp/').$employee->scannpwp);
        }
        if($employee->scanbpjs != "noimage.jpg"){
            unlink(public_path('assets/images/employee/bpjs/').$employee->scanbpjs);
        }

        $employee->delete();
        Log::setLog('EMEMD','Delete Employee: '.$name);
        return "true";
    }

}
