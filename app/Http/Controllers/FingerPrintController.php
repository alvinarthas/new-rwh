<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\DemoDevice;
use App\DemoFinger;
use App\DemoLog;
use App\DemoUser;
use App\Purchase;
use App\Sales;
use App\Employee;
use App\Jurnal;
use App\PurchaseDetail;
use App\TempPO;
use App\TempSales;

class FingerPrintController extends Controller
{
    public function register(Request $request){
        $time_limit_reg = "50";

        return "$request->user_id;SecurityKey;".$time_limit_reg.";".route('fingerProcessRegister').";".route('fingerGetAc');
    }

    public function process_register(Request $request){
        if (isset($request->RegTemp) && !empty($request->RegTemp)) {

            $data = explode(";",$request->RegTemp);
            $vStamp     = $data[0];
            $sn         = $data[1];
            $user_id    = $data[2];
            $regTemp    = $data[3];
            $device = DemoDevice::where('sn',$sn)->first();
            $i = 0;

            $salt = md5($device->ac.$device->vkey.$regTemp.$sn.$user_id);

            if (strtoupper($vStamp) == strtoupper($salt)) {
                $fid = DemoFinger::where('user_id',$user_id)->max('finger_id');

                if($fid == 0){

                    $insFinger = new DemoFinger(array(
                        'user_id' => $user_id,
                        'finger_id' => $fid+1,
                        'finger_data' => $regTemp
                    ));

                    if($insFinger->save()){
                        $res['result'] = true;
                        $page = route('employee.index');
                        $sec = "10";
                        header("Refresh: $sec; url=$page");
                    }else{

                        $res['server'] = "Error insert registration data!";
                    }
                }else{
                    $res['result'] = false;
                    $res['user_finger_'.$user_id] = "Template already exist.";
                }
                echo "empty";
            }else{
                $msg = "Parameter invalid..";

                echo route('fingerMessage',['msg' => $mgs]);
            }
        }
    }

    public function get_ac(Request $request){
        $device = DemoDevice::where('vc',$request->vc)->first();
        $i 	= 0;

        echo $device->ac.$device->sn;
    }

    public function messages(Request $request){
        if(isset($request->msg) && !empty($request->msg)){

        }elseif (isset($request->user_name) && !empty($request->user_name) && isset($request->time) &&!empty($request->time)) {
            $time = date('Y-m-d H:i:s', strtotime($request->time));

            echo $request->user_name." login success on ".date('Y-m-d H:i:s', strtotime($time));
        }else{
            $msg = "Parameter invalid..";

            echo "$msg";
        }
    }

    public function checkreg(Request $request){
        $countfinger     =  DemoFinger::where('user_id',$request->user_id)->count();
		if (intval($countfinger) > intval($request->current)) {
			$res['result'] = true;
			$res['current'] = intval($countfinger);
		}
		else
		{
			$res['result'] = false;
        }
		echo json_encode($res);
    }

    public function verification(Request $request){
        if (isset($request->user_id) && !empty($request->user_id)) {
            $time_limit_ver = 50;

            $finger = DemoFinger::where('user_id',$request->user_id)->first();

            echo "$request->user_id;".$finger->finger_data.";SecurityKey;".$time_limit_ver.";".route('fingerProcessVerification',['keterangan'=>$request->keterangan]).";".route('fingerGetAc').";extraParams";
        }

    }

    public function process_verification(Request $request){
        if (isset($request->VerPas) && !empty($request->VerPas)) {
            // initialize
            $data 		= explode(";",$request->VerPas);
            $user_id	= $data[0];
            $vStamp 	= $data[1];
            $time 		= $data[2];
            $sn 		= $data[3];

            // Get Finger Data
            $finger = DemoFinger::where('user_id',$user_id)->first();

            // Get Device
            $device = DemoDevice::where('sn',$sn)->first();
            // Verification
            $username = DemoUser::where('user_id',$user_id)->first()->user_name;

            $salt = md5($sn.$finger->finger_data.$device->vc.$time.$user_id.$device->vkey);
            if (strtoupper($vStamp) == strtoupper($salt)) {
                // Insert to Log
                $log = new DemoLog(array(
                    'user_name' => $username,
                    'keterangan' => $request->keterangan,
                    'data' => date('Y-m-d H:i:s', strtotime($time))." (PC Time) | ".$sn." (SN)",
                ));

                $log->save();
            }
        }

    }

    public function ajxlog(Request $request){
        $username = DemoUser::where('user_id',$request->user_id)->first()->user_name;
        $logs = DemoLog::where('user_name',$username)->orderBy('log_time','desc')->limit(10)->get();
        return view('absensi.ajxLog',compact('logs','username'));
    }

    public function ajxfulllog(Request $request){
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        $logs = DemoLog::whereBetween(DB::raw('DATE(log_time)'), array($tanggal_awal, $tanggal_akhir))->latest()->get();

        return view('absensi.ajxFullLog',compact('logs','tanggal_awal','tanggal_akhir'));
    }

    // Purchase
    public function purchaseApprove(Request $request){
        if (isset($request->user_id) && !empty($request->user_id)) {
            $time_limit_ver = 50;

            $finger = DemoFinger::where('user_id',$request->user_id)->first();
            echo "$request->user_id;".$finger->finger_data.";SecurityKey;".$time_limit_ver.";".route('purchaseApproveProcess',['trx_id'=>$request->trx_id,'role'=>$request->role]).";".route('fingerGetAc').";extraParams";
        }
    }

    public function purchaseApproveProcess(Request $request){
        if (isset($request->VerPas) && !empty($request->VerPas)) {
            // initialize
            $data 		= explode(";",$request->VerPas);
            $user_id	= $data[0];
            $vStamp 	= $data[1];
            $time 		= $data[2];
            $sn 		= $data[3];
            $role = $request->role;
            // Get Finger Data
            $finger = DemoFinger::where('user_id',$user_id)->first();

            // Get Device
            $device = DemoDevice::where('sn',$sn)->first();
            // Verification
            $username = DemoUser::where('user_id',$user_id)->first()->user_name;

            $salt = md5($sn.$finger->finger_data.$device->vc.$time.$user_id.$device->vkey);
            if (strtoupper($vStamp) == strtoupper($salt)) {
                if($role == "Superadmin" || $role == "Direktur Utama" || $role == "General Manager" || $role == "Manager Operasional" || $role == "Manager Keuangan" || $role == "Assistant General Manager"){
                    // Insert to Log
                    try{
                        $approve = Purchase::where('id',$request->trx_id)->select('approve')->first()->approve;
                        $count_temp = TempPO::where('purchase_id',$request->trx_id)->count('purchase_id');
                        $status_temp = TempPO::where('purchase_id',$request->trx_id)->where('status',1)->count('purchase_id');

                        if($approve == 0){
                            if($count_temp > 0 && $status_temp == 1){
                                Purchase::updatePurchase($request->trx_id,$user_id);
                            }else{
                                Purchase::setJurnal($request->trx_id,$user_id);
                            }

                        }else{
                            Purchase::updatePurchase($request->trx_id,$user_id);
                        }

                        echo route('purchase.index');

                    }catch(\Exception $e){
                        echo "<pre>";
                        print_r($e->getMessage());
                    }
                }else{
                    echo route('purchase.index')->with('warning', 'Akun anda tidak bisa menggaprove transaksi ini');
                }
            }
        }

    }

    // Sales
    public function salesApprove(Request $request){
        if (isset($request->user_id) && !empty($request->user_id)) {
            $time_limit_ver = 50;

            $finger = DemoFinger::where('user_id',$request->user_id)->first();

            echo "$request->user_id;".$finger->finger_data.";SecurityKey;".$time_limit_ver.";".route('salesApproveProcess',['trx_id'=>$request->trx_id,'role'=>$request->role]).";".route('fingerGetAc').";extraParams";
        }
    }

    public function salesApproveProcess(Request $request){
        if (isset($request->VerPas) && !empty($request->VerPas)) {
            // initialize
            $data 		= explode(";",$request->VerPas);
            $user_id	= $data[0];
            $vStamp 	= $data[1];
            $time 		= $data[2];
            $sn 		= $data[3];
            $role = $request->role;
            // Get Finger Data
            $finger = DemoFinger::where('user_id',$user_id)->first();

            // Get Device
            $device = DemoDevice::where('sn',$sn)->first();
            // Verification
            $username = DemoUser::where('user_id',$user_id)->first()->user_name;

            $salt = md5($sn.$finger->finger_data.$device->vc.$time.$user_id.$device->vkey);
            if (strtoupper($vStamp) == strtoupper($salt)) {
                if($role == "Superadmin" || $role == "Direktur Utama" || $role == "General Manager" || $role == "Manager Keuangan" || $role == "Manager Operasional" || $role == "Assistant General Manager"){
                    // Insert to Log
                    try{
                        $approve = Sales::where('id',$request->trx_id)->select('approve')->first()->approve;
                        $count_temp = TempSales::where('trx_id',$request->trx_id)->count('trx_id');
                        $status_temp = TempSales::where('trx_id',$request->trx_id)->where('status',1)->count('trx_id');

                        if($approve == 0){
                            if($count_temp > 0 && $status_temp == 1){
                                Sales::updateSales($request->trx_id,$user_id);
                            }else{
                                Sales::setJurnal($request->trx_id,$user_id);
                            }

                        }else{
                            Sales::updateSales($request->trx_id,$user_id);
                        }
                        echo route('sales.index');
                    }catch(\Exception $e){
                        echo "<pre>";
                        print_r($e->getMessage());
                    }
                }else{
                    echo route('sales.index')->with('warning', 'Akun anda tidak bisa menggaprove transaksi ini');
                }

            }
        }

    }
}
