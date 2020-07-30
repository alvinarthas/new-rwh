<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Member;
use App\BonusBayar;
use App\PerusahaanMember;
use App\BankMember;

class Bonus extends Model
{
    protected $table ='tblbonus';
    protected $primaryKey = 'id_bonus';
    protected $fillable = [
        'noid','tgl','bulan','tahun','bonus', 'perusahaan_id','creator', 'id_jurnal'
    ];

    public $timestamps = true;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }

    public static function getRealsisasiBonus(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $draw = $request->draw;
        $row = $request->start;
        $offset = $request->offset;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        $member = BonusBayar::join('bankmember', 'tblbonusbayar.no_rek', 'bankmember.norek')->join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('tblbonusbayar.bulan', $bulan)->where('tblbonusbayar.tahun', $tahun)->select('tblmember.nama', 'tblmember.ktp', DB::raw(DB::raw('(SELECT CASE WHEN SUM(tblbonus.bonus) IS NULL THEN 0 ELSE SUM(tblbonus.bonus) END FROM tblbonus JOIN perusahaanmember ON perusahaanmember.noid=tblbonus.noid WHERE tblbonus.bulan='.$bulan.' AND tblbonus.tahun='.$tahun.' AND perusahaanmember.ktp=tblmember.ktp)').'- SUM(tblbonusbayar.bonus) AS selisih'))->groupBy('tblmember.ktp');

        $totalRecords = 0;
        foreach($member->get() as $count){
            $totalRecords++;
        }

        if($searchValue != ''){
            $member->where('tblmember.nama', 'LIKE', '%'.$searchValue.'%')->orWhere('tblmember.ktp', 'LIKE', '%'.$searchValue.'%');
        }

        $totalRecordwithFilter = 0;

        foreach($member->get() as $count){
            $totalRecordwithFilter++;
        }

        if($columnName == "no"){
            $member->orderBy('tblbonusbayar.id_bonus', $columnSortOrder);
        }else{
            $member->orderBy($columnName, $columnSortOrder);
        }

        $member = $member->offset($row)->limit($rowperpage)->get();
        // $memberb = $member->offset($row)->limit($rowperpage)->toSql();

        // echo $memberb;
        // die();

        $datas = collect();
        $i = 1;

        foreach($member as $m){
            $data = collect();
            $perhitungan = "";
            $penerimaan = "";
            $no_perhitungan = 1;
            $no_penerimaan = 1;
            $total_perhitungan = 0;
            $total_penerimaan = 0;

            $perusahaanmember = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('perusahaanmember.ktp', '=', $m->ktp)->select('perusahaanmember.noid', 'tblperusahaan.id', 'tblperusahaan.nama')->get();

            foreach($perusahaanmember as $pm){
                $bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $pm->noid)->sum('bonus');
                $perhitungan .= $no_perhitungan++.". ".$pm->nama." ".$pm->noid."<br>";
                $perhitungan .= "Bonus : Rp ".number_format($bonus, 2, ",", ".")."<br>";
                $total_perhitungan += $bonus;
            }
            $perhitungan .= "<br><b>Total : Rp ".number_format($total_perhitungan, 2, ",", ".")."</b>";

            $bankmember = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('bankmember.ktp', $m->ktp)->select('bankmember.norek', 'bankmember.bank_id', 'tblbank.nama')->get();

            foreach($bankmember as $bm){
                $bonusbayar = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->orderBy('tgl', 'desc')->select('tgl')->get();
                foreach($bonusbayar as $key){
                    $amount = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->where('tgl', $key->tgl)->sum('bonus');
                    $penerimaan .= $no_penerimaan++.". ".$bm->nama." ".$bm->norek."<br>";
                    $penerimaan .= "Bonus : Rp ".number_format($amount, 2, ",", ".")."<br>";
                    $penerimaan .= "Tgl : ".$key->tgl."<br>";
                    $total_penerimaan += $amount;
                }
            }

            $penerimaan .= "<br><b>Total : Rp ".number_format($total_penerimaan, 2, ",", ".")."</b>";

            $selisih = $total_perhitungan - $total_penerimaan;
            // $selisih = $m->selisih;
            $data->put('no', $i++);
            $data->put('ktp', $m->ktp);
            $data->put('nama', $m->nama);
            $data->put('perhitungan', $perhitungan);
            $data->put('penerimaan', $penerimaan);
            $data->put('selisih', $selisih);
            $datas->push($data);
        }
        // echo "<pre>";
        // print_r($datas);
        // die();

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $datas,
        );

        return $response;
    }

    // public static function getRealsisasiBonus(Request $request){
    //     // echo "<pre>";
    //     // print_r($request->all());
    //     // die();

    //     $bulan = $request->bulan;
    //     $tahun = $request->tahun;

    //     $draw = $request->draw;
    //     $row = $request->start;
    //     $offset = $request->offset;
    //     $awal = $offset;
    //     $rowperpage = $request->length; // Rows display per page
    //     $columnIndex = $request['order'][0]['column']; // Column index
    //     $columnName = $request['columns'][$columnIndex]['data']; // Column name
    //     $columnSortOrder = $request['order'][0]['dir']; // asc or desc
    //     $searchValue = $request['search']['value']; // Search value

    //     $member = Member::select('ktp','nama','id');

    //     $totalRecords = $member->count();

    //     if($searchValue != ''){
    //         $member->where('nama', 'LIKE', '%'.$searchValue.'%')->orWhere('ktp', 'LIKE', '%'.$searchValue.'%');
    //     }

    //     $totalRecordwithFilter = $member->count();

    //     if($columnName == "no" OR $columnName=="row"){
    //         $member = $member->orderBy('id', $columnSortOrder)->offset($offset)->limit(100)->get();
    //     }else{
    //         $member = $member->orderBy($columnName, $columnSortOrder)->offset($offset)->limit(100)->get();
    //     }

    //     // $member = $member->offset($row)->limit($rowperpage)->get();

    //     $datas = collect();
    //     $i = 1;
    //     $countData = 0;

    //     foreach($member as $m){
    //         $data = collect();
    //         $perhitungan = "";
    //         $penerimaan = "";
    //         $no_perhitungan = 1;
    //         $no_penerimaan = 1;
    //         $total_perhitungan = 0;
    //         $total_penerimaan = 0;

    //         $perusahaanmember = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('perusahaanmember.ktp', '=', $m->ktp)->select('perusahaanmember.noid', 'tblperusahaan.id', 'tblperusahaan.nama')->get();

    //         foreach($perusahaanmember as $pm){
    //             $bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $pm->noid)->sum('bonus');
    //             $perhitungan .= $no_perhitungan++.". ".$pm->nama." ".$pm->noid."<br>";
    //             $perhitungan .= "Bonus : Rp ".number_format($bonus, 2, ",", ".")."<br>";
    //             $total_perhitungan += $bonus;
    //         }
    //         $perhitungan .= "<br>Total : Rp ".number_format($total_perhitungan, 2, ",", ".");

    //         $bankmember = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('bankmember.ktp', $m->ktp)->select('bankmember.norek', 'bankmember.bank_id', 'tblbank.nama')->get();

    //         foreach($bankmember as $bm){
    //             $bonusbayar = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->orderBy('tgl', 'desc')->select('tgl')->get();
    //             foreach($bonusbayar as $key){
    //                 $amount = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->where('tgl', $key->tgl)->sum('bonus');
    //                 $penerimaan .= $no_penerimaan++.". ".$bm->nama." ".$bm->norek."<br>";
    //                 $penerimaan .= "Bonus : Rp ".number_format($amount, 2, ",", ".")."<br>";
    //                 $penerimaan .= "Tgl : ".$key->tgl."<br>";
    //                 $total_penerimaan += $amount;
    //             }
    //         }

    //         $penerimaan .= "<br>Total : Rp ".number_format($total_penerimaan, 2, ",", ".");

    //         $selisih = $total_perhitungan - $total_penerimaan;

    //         if($total_perhitungan != 0 OR $total_penerimaan != 0){
    //             $data->put('no', $i++);
    //             $data->put('ktp', $m->ktp);
    //             $data->put('nama', $m->nama);
    //             $data->put('perhitungan', $perhitungan);
    //             $data->put('penerimaan', $penerimaan);
    //             $data->put('selisih', "Rp ".number_format($selisih, 2, ",", "."));
    //             $datas->push($data);
    //             $countData++;
    //             if($countData == $rowperpage){
    //                 break;
    //             }
    //         }
    //         $offset++;
    //     }
    //     // dd($awal,$offset);
    //     $response = array(
    //         'draw' => intval($draw),
    //         'recordsTotal' => $totalRecords,
    //         'recordsFiltered' => $totalRecordwithFilter,
    //         'data' => $datas,
    //         'offset' => $offset,
    //         'awal' => $awal,
    //     );

    //     return $response;
    // }

    public static function exportRealsisasiBonus($bulan, $tahun){
        $member = Member::select('ktp','nama')->orderBy('nama','asc')->get();
        $data = collect();

        foreach($member as $m){
            $row = collect();
            $no_perhitungan = 1;
            $no_penerimaan = 1;
            $perhitungan = "";
            $penerimaan = "";
            $total_perhitungan = 0;
            $total_penerimaan = 0;

            $perusahaanmember = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('perusahaanmember.ktp', $m->ktp)->select('perusahaanmember.noid', 'tblperusahaan.id', 'tblperusahaan.nama')->get();

            foreach($perusahaanmember as $pm){
                $bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $pm->noid)->sum('bonus');
                $perhitungan .= $no_perhitungan++.". ".$pm->nama." ".$pm->noid."<br><b>Bonus : Rp ".number_format($bonus, 2, ",", ".")."</b><br>";
                $total_perhitungan += $bonus;
            }
            $perhitungan .= "<br><b>Total : Rp ".number_format($total_perhitungan, 2, ",", ".")."</b>";

            $bankmember = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('bankmember.ktp', $m->ktp)->select('bankmember.norek', 'bankmember.bank_id', 'tblbank.nama')->get();

            foreach($bankmember as $bm){
                $bonus = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->sum('bonus');
                $penerimaan .= $no_penerimaan++.". ".$bm->nama." ".$bm->norek."<br><b>Bonus : Rp ".number_format($bonus, 2, ",", ".")."</b><br>";
                $total_penerimaan += $bonus;
            }

            $penerimaan .= "<br><b>Total : Rp ".number_format($total_penerimaan, 2, ",", ".")."</b>";

            $selisih = $total_perhitungan - $total_penerimaan;
            if($total_perhitungan != 0 || $total_penerimaan != 0){
                $row->put('ktp', $m->ktp);
                $row->put('nama', $m->nama);
                $row->put('perhitungan', $perhitungan);
                $row->put('penerimaan', $penerimaan);
                $row->put('selisih', $selisih);
                $data->push($row);
            }
        }
        return $data;
    }
}
