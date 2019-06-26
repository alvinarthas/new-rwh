<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
