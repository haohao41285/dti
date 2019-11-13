<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainUserCustomerPlace extends Model
{
    protected $table = 'main_user_customer_places';
    protected $fillable = [
        'user_id',
        'team_id',
        'customer_id',
        'place_id',
    ];
    public function getPlace(){
        return $this->belongsTo(PosPlace::class,'place_id','place_id')->withDefault();
    }
    public function getUser(){
        return $this->belongsTo(MainUser::class,'user_id','user_id')->withDefault();
    }

}
