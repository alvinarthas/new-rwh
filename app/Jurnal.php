<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table ='tbljurnal';
    protected $fillable = [
        'id_jurnal', 'AccNo','AccPos','Amount','company_id','date','description','creator','status','nama_category','budget_month','budget_year','notes_item'
    ];

    public function coa(){
        return $this->belongsTo('App\Coa','AccNo','AccNo');
    }

    public static function getJurnalID(){
        $count_jurnal = Jurnal::distinct('id_jurnal')->count('id_jurnal');
        if($count_jurnal == 0){
            $id_jurnal = 1;
        }else{
            $id_jurnal = $count_jurnal+1;
        }
        return $id_jurnal;
    }

    public static function viewJurnal($start,$end,$coa,$position){
        $jurnal = Jurnal::whereBetween('date',[$start,$end]);
        $jurdebet = Jurnal::whereBetween('date',[$start,$end]);
        $jurcredit = Jurnal::whereBetween('date',[$start,$end]);
        if($coa <> "all"){
            $jurnal->where('AccNo',$coa);
            $jurdebet->where('AccNo',$coa);
            $jurcredit->where('AccNo',$coa);
        }

        $ttl_debet = $jurdebet->where('AccPos','Debet')->sum('Amount');
        $ttl_credit = $jurcredit->where('AccPos','Credit')->sum('Amount');

        if($position <> "all"){
            $jurnal->where('AccPos',$position);
        }
        
        $data = collect();
        $data->put('data',$jurnal->orderBy('date','asc')->get());
        $data->put('ttl_debet',$ttl_debet);
        $data->put('ttl_credit',$ttl_credit);
        
        return $data;
    }

    public static function addJurnal($id_jurnal,$amount,$date,$desc,$coa,$position){
        $jurnal = new Jurnal(array(
            'id_jurnal' => $id_jurnal,
            'AccNo' => $coa,
            'AccPos' => $position,
            'Amount' => $amount,
            'company_id' => 1,
            'date' => $date,
            'description' => $desc,
            'creator' => session('user_id'),
        ));

        $jurnal->save();
    }
}
