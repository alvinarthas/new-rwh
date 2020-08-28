<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Jurnal;

class TopUpBonus extends Model
{
    protected $table ='tbltopupbonus';
    protected $primaryKey = 'id_bonus';
    protected $fillable = [
        'no_rek','tgl','bonus', 'creator', 'id_jurnal', 'AccNo'
    ];

    public $timestamps = true;

    public static function recycleTopUpBonus($id_jurnal){
        $data = TopUpBonus::where('id_jurnal', $id_jurnal)->first();
        $user_id = session('user_id');

        if(Jurnal::where('id_jurnal', $id_jurnal)->count() != 0){
            Jurnal::where('id_jurnal', $id_jurnal)->delete();
        }

        $ket = 'top up bonus '.$data->norek.' - '.$data->tgl;

        // debet estimasi bonus
        Jurnal::addJurnal($id_jurnal,$data->bonus,$data->tgl,$ket,"1.1.3.4","Debet",$user_id);

        // credit kas/bank
        Jurnal::addJurnal($id_jurnal,$data->bonus,$data->tgl,$ket,$data->AccNo,"Credit",$user_id);
    }

    public static function recycleTopUpBonusAll($tgl, $AccNo){
        $datas = TopupBonus::where('tgl', $tgl)->where('AccNo',$AccNo)->get();
        Jurnal::where('id_jurnal', 'LIKE', 'BT.%')->where('date', $tgl)->where('AccNo', $AccNo)->delete();
        $user_id = session('user_id');

        foreach($datas as $data){
            $ket = 'top up bonus '.$data->norek.' - '.$data->tgl;

            // debet estimasi bonus
            Jurnal::addJurnal($data->id_jurnal, $data->bonus, $data->tgl, $ket, "1.1.3.4", "Debet", $user_id);

            // credit kas/bank
            Jurnal::addJurnal($data->id_jurnal, $data->bonus, $data->tgl, $ket, $data->AccNo, "Credit", $user_id);
        }


    }

}
