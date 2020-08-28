<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Jurnal;

class BonusBayar extends Model
{
    protected $table ='tblbonusbayar';
    protected $primaryKey = 'id_bonus';
    protected $fillable = [
        'no_rek','tgl','bulan','tahun','bonus', 'creator', 'id_jurnal', 'AccNo', 'supplier'
    ];

    public function coa(){
        return $this->belongsTo('App\Coa','AccNo','AccNo');
    }

    public static function recyclePenerimaanBonus($id_jurnal){
        $total_bonus = 0;
        $bonusbayar = BonusBayar::where('id_jurnal', $id_jurnal)->get();
        $data = BonusBayar::where('id_jurnal', $id_jurnal)->first();

        Jurnal::where('id_jurnal', $id_jurnal)->where('AccPos', 'Debet')->delete();

        foreach($bonusbayar as $bb){
            if($bb->AccNo != "1.1.1.1.000003"){
                $ket = 'penerimaan bonus ke '.$bb->AccNo.' untuk '.$bb->norek.' - bulan '.$bb->bulan.' '.$bb->tahun;
            }else{
                $nama = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('norek', $bb->norek)->first()->nama;
                $ket = 'penerimaan bonus ke Kas Bonus Morinda untuk '.$nama.' - bulan '.$bb->bulan.' '.$bb->tahun;
            }

            // debet kas/bank
            Jurnal::addJurnal($id_jurnal,$bb->bonus,$bb->tgl,$ket,$bb->AccNo,'Debet',session('user_id'));

            // $debet = new Jurnal(array(
            //     'id_jurnal'     => $id_jurnal,
            //     'AccNo'         => $bb->AccNo,
            //     'AccPos'        => "Debet",
            //     'Amount'        => $bb->bonus,
            //     'company_id'    => 1,
            //     'date'          => $bb->tgl,
            //     'description'   => $ket,
            //     'creator'       => session('user_id')
            // ));
            // $debet->save();

            $total_bonus += $bb->bonus;
        }

        $ket = 'penerimaan bonus ke '.$data->AccNo.' - bulan '.$data->bulan.' '.$data->tahun;

        // credit piutang bonus tertahan
        $credit = Jurnal::where('id_jurnal', $id_jurnal)->where('AccNo', '1.1.3.5')->where('AccPos', 'Credit')->first();
        $credit->Amount      = $total_bonus;
        $credit->date        = $data->tgl;
        $credit->description = $ket;
        $credit->creator     = session('user_id');
        $credit->update();
    }

}
