<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table ='tbljurnal';
    protected $fillable = [
        'id_jurnal', 'AccNo','AccPos','Amount','company_id','date','description','creator','status','nama_category','budget_month','budget_year','notes_item'
    ];
}
