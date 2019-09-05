<?php

namespace App\Exports;

use App\Koordinator;
use App\Subkoordinator;
use App\Member;
use App\BankMember;
use App\Bank;
use App\Perusahaan;
use App\PerusahaanMember;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MemberExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array():array
    {
        $i=0;
        $member = Member::all();
        $data = array();
        foreach($member as $m){
            $i++;
            if(!empty($m->koordinator)){
                $koor = Koordinator::where('id', $m->koordinator)->first()->nama;
            }else{
                $koor = "";
            }

            if(!empty($m->subkoor)){
                $subkoor = SubKoordinator::where('id', $m->subkoor)->first()->nama;
            }else{
                $subkoor ="";
            }

            $bankmember = BankMember::where('ktp', $m->ktp)->get();
            $rekenings = "";
            foreach($bankmember as $bm){
                $bank = Bank::where('id', $bm->bank_id)->first()->nama;
                $rekening = $bank.' '.$bm->norek;
                $rekenings = $rekenings."".$rekening.", \n";
            }

            $perusahaanmember = PerusahaanMember::where('ktp', $m->ktp)->get();
            $perusahaans = "";
            foreach($perusahaanmember as $pm){
                if($pm->perusahaan_id!=0){
                    $perusahaan = Perusahaan::where('id', $pm->perusahaan_id)->first()->nama;
                    $noid = $perusahaan.' '.$pm->noid;
                }else{
                    $noid = "";
                }
                $perusahaans = $perusahaans."".$noid.", \n";
            }

            $ttl = $m->tempat_lahir.", ".$m->tgl_lahir;
            $array = array(
                // Data Member
                'No' => $i,
                'Nama' => $m->nama,
                'ID_Member' => $m->member_id,
                'No_KTP' => $m->ktp,
                'alamat' => $m->alamat,
                'TTL' => $ttl,
                'Koordinator' => $koor,
                'Sub Koordinator' => $subkoor,
                'Bank Member' => $rekenings,
                'Perusahaan Member' => $perusahaans,
            );
            array_push($data, $array);
        }

        return $data;
    }

    public function startCell()
    {
        return 'A2';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'ID Member',
            'No KTP',
            'Alamat',
            'Tempat Tanggal Lahir',
            'Koordinator',
            'Sub Koordinator',
            'Bank Member',
            'Perusahaan Member',
        ];
    }
}
