<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public static function getRealsisasiBonus($bulan, $tahun){
        $member = Member::select('ktp','nama')->orderBy('nama','asc')->get();
        $data = collect();

        foreach($member as $m){
            $row = collect();
            $perhitungan = collect();
            $penerimaan = collect();
            $no_perhitungan = 1;
            $no_penerimaan = 1;
            $total_perhitungan = 0;
            $total_penerimaan = 0;

            $perusahaanmember = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('perusahaanmember.ktp', $m->ktp)->select('perusahaanmember.noid', 'tblperusahaan.id', 'tblperusahaan.nama')->get();

            foreach($perusahaanmember as $pm){
                $permem = collect();
                $bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $pm->noid)->sum('bonus');
                $permem->put("noid", $no_perhitungan++.". ".$pm->nama." ".$pm->noid);
                $permem->put("bonus", "Bonus : Rp ".number_format($bonus, 2, ",", "."));
                $perhitungan->push($permem);
                $total_perhitungan += $bonus;
            }
            $ttl_perhitungan = "Total : Rp ".number_format($total_perhitungan, 2, ",", ".");

            $bankmember = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('bankmember.ktp', $m->ktp)->select('bankmember.norek', 'bankmember.bank_id', 'tblbank.nama')->get();

            foreach($bankmember as $bm){
                $bankmem = collect();
                $bb = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->orderBy('tgl', 'desc')->select('tgl')->first();
                $bonus = BonusBayar::where('bulan', $bulan)->where('tahun', $tahun)->where('no_rek', $bm->norek)->sum('bonus');
                $bankmem->put("norek", $no_penerimaan++.". ".$bm->nama." ".$bm->norek);
                $bankmem->put("bonus", "Bonus : Rp ".number_format($bonus, 2, ",", "."));
                $bankmem->put("tgl", "Tgl : ".$bb['tgl']);
                $penerimaan->push($bankmem);
                $total_penerimaan += $bonus;
            }

            $ttl_penerimaan = "Total : Rp ".number_format($total_penerimaan, 2, ",", ".");

            $selisih = $total_perhitungan - $total_penerimaan;
            if($total_perhitungan != 0 || $total_penerimaan != 0){
                $row->put('ktp', $m->ktp);
                $row->put('nama', $m->nama);
                $row->put('perhitungan', $perhitungan);
                $row->put('ttl_perhitungan', $ttl_perhitungan);
                $row->put('penerimaan', $penerimaan);
                $row->put('ttl_penerimaan', $ttl_penerimaan);
                $row->put('selisih', $selisih);
                $data->push($row);
            }
        }
        return $data;
    }

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
