<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Purchase;

class PurchaseDetail extends Model
{
    protected $table ='tblpotrxdet';
    protected $fillable = [
        'trx_id','prod_id','qty','unit','price','price_dist','creator'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Purchase','trx_id');
    }

    public static function avgCost($product,$trx_date=null){
        $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$product);

        $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$product);

        if($trx_date != null){
            $sumprice= $sumprice->where('tblpotrx.tgl','<=',$trx_date);
            $sumqty= $sumqty->where('tblpotrx.tgl','<=',$trx_date);
        }
        

        $sumprice= $sumprice->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));
        $sumqty= $sumqty->sum('tblpotrxdet.qty');
        

        if($sumprice <> 0 && $sumqty <> 0){
            $avcharga = $sumprice/$sumqty;
        }else{
            $avcharga = 0;
        }

        return $avcharga;
    }
}
