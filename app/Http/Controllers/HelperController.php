<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\DB;

use App\DataKota;

class HelperController extends Controller
{
    public function getDataKota(Request $request){
        $kota = DataKota::where('kode_pusdatin_prov',$request->prov)->select('kode_pusdatin_kota','kab_kota')->get();

        $html = '<option value="#" disabled selected>Pilih Kab/Kota</>';
        foreach ($kota as $key) {
            $html.='<option value="'.$key->kode_pusdatin_kota.'">'.$key->kab_kota.'</option>';
        }
        echo $html;
    }
}
