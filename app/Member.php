<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\BankMember;

class Member extends Model
{
    protected $table ='tblmember';
    protected $fillable = [
        'member_id', 'koordinator','ktp','subkoor','nama','alamat','telp','tempat_lahir','tgl_lahir','ibu','creator','status','cetak','prov','city','scanktp'
    ];

    public static function lastMember(){
        return Member::orderBy('id','desc')->first();
    }

    public static function indexMember(){
        $people = Member::select('ktp','nama','scanktp','cetak')->paginate();
        $data = collect();
        $i=1;
        foreach($people as $key){
            $memcollect = collect();
            $memcollect->put('no',$i);
            $memcollect->put('ktp',$key->ktp);
            $memcollect->put('nama',$key->nama);

            if($key->scanktp == "noimage.jpg"){
                $memcollect->put('scanktp','Empty');
            }else{
                $memcollect->put('scanktp','Have Filled');
            }

            if($key->cetak == 0){
                $memcollect->put('cetak','Belum Cetak');
            }else{
                $memcollect->put('cetak','Sudah Cetak');
            }

            $tabungan = BankMember::getData($key->ktp);
            if($tabungan <> NULL){
                if($tabungan->scantabungan == "noimage.jpg"){
                    $memcollect->put('scantabungan','Empty');
                }else{
                    $memcollect->put('scantabungan','Have Filled');
                }
                if($tabungan->scanatm == "noimage.jpg"){
                    $memcollect->put('scanatm','Empty');
                }else{
                    $memcollect->put('scanatm','Have Filled');
                }
                $memcollect->put('scanrekening','Aktif');
            }else{
                $memcollect->put('scantabungan','No Primary');
                $memcollect->put('scanatm','No Primary');
                $memcollect->put('scanrekening','Tidak Aktif');
            }
            $data->push($memcollect);
            $i++;
        }
        $data->push($people->links());
        return $data;
    }

    public static function viewMember(Request $request){
        $jenis = $request->jenis;
        $perusahaan = $request->perusahaan;
        $bank = $request->bank;
        $statusrek = $request->statusrek;

        $draw = $request->draw;
        $row = $request->start;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        $member = Member::select('id','ktp','nama','scanktp','cetak');

        if($jenis == 1){
            $array = array_values(array_column(DB::select("SELECT ktp FROM perusahaanmember WHERE perusahaan_id = $perusahaan"),'ktp'));
            $member->whereIn('ktp',$array);
        }elseif($jenis == 2){
            $array = array_values(array_column(DB::select("SELECT ktp FROM perusahaanmember WHERE perusahaan_id = $perusahaan"),'ktp'));
            $member->whereNotIn('ktp',$array);
        }elseif($jenis == 4){
            $array = array_values(array_column(DB::select("SELECT ktp FROM bankmember WHERE bank_id =$bank"),'ktp'));
            $member->whereIn('ktp',$array);
        }
        
        if($statusrek != "#"){
            $array = array_values(array_column(DB::select("SELECT ktp FROM bankmember where status=$statusrek"),'ktp'));
            $member->whereIn('ktp', $array);
        }

        $totalRecords = $member->count();

        if($searchValue != ''){
            if($jenis == 1){
                $noid = PerusahaanMember::select('ktp')->where('perusahaan_id', $perusahaan)->where('noid', 'LIKE', '%'.$searchValue.'%')->get();
            }elseif($jenis == 2){
                $noid = PerusahaanMember::select('ktp')->where('perusahaan_id', '!=', $perusahaan)->where('noid', 'LIKE', '%'.$searchValue.'%')->get();
            }else{
                $noid = PerusahaanMember::select('ktp')->where('noid', 'LIKE', '%'.$searchValue.'%')->get();
            }
            if($jenis == 4){
                $norek = BankMember::select('ktp')->where('bank_id', $bank)->where('norek', 'LIKE', '%'.$searchValue.'%')->get();
            }else{
                $norek = BankMember::select('ktp')->where('norek', 'LIKE', '%'.$searchValue.'%')->get();
            }

            // echo $raw;
            $member->where('nama', 'LIKE', '%'.$searchValue.'%')->orWhere('ktp', 'LIKE', '%'.$searchValue.'%')->orWhereIn('ktp', $norek)->orWhereIn('ktp', $noid);
        }

        $totalRecordwithFilter = $member->count();

        if($columnName == "no"){
            $member->orderBy('id', $columnSortOrder);
        }else{
            $member->orderBy($columnName, $columnSortOrder);
        }

        $member = $member->offset($row)->limit($rowperpage)->get();

        $data = collect();
        $i = 1;

        foreach($member as $key){
            $detail = collect();

            $button = '<a href="'.route('member.show',['id'=>$key->ktp]).'">'.$key->nama.'</a>';

            if($key->scanktp == "noimage.jpg" OR $key->scanktp == ''){
                $scanktp = "Empty";
            }else{
                $scanktp = "Have Filled";
            }

            $tabungan = BankMember::getData($key->ktp, $statusrek);
            $scantabungan = "";

            if($tabungan <> NULL){
                if($tabungan->scantabungan == "noimage.png"){
                    $scantabungan = "Empty";
                }else{
                    $scantabungan = "Have Filled";
                }

                if($tabungan->scanatm == "noimage.png"){
                    $scanatm = "Empty";
                }else{
                    $scanatm = "Have Filled";
                }

                $status = $tabungan->statusrek->status;
            }else{
                $scantabungan = "Empty";
                $scanatm = "Empty";
                $status = "Empty";
            }

            if($key->cetak == 1){
                $cetak = "Sudah dicetak";
            }else{
                $cetak = "Belum dicetak";
            }

            $detail->put('no', $i++);
            $detail->put('nama', $button);
            $detail->put('gambar_ktp', $scanktp);
            $detail->put('gambar_tabungan', $scantabungan);
            $detail->put('gambar_atm', $scanatm);
            $detail->put('status_rekening', $status);
            $detail->put('status_cetak',$cetak);
            $data->push($detail);
        }

        // echo "<pre>";
        // print_r($data);
        // die();

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );

        return $response;
    }
}
