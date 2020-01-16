<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainNotification extends Model
{
    protected $table = 'main_notification';
    protected $fillable = [
        'id',
        'content',
        'href_to',
        'receiver_id',
        'read_not',
        'created_by',
    ];
    public function scopeNotRead($query){
        return $query->where('read_not',0);
    }
    public function getCreatedBy(){
        return $this->belongsTo(MainUser::class,'created_by','user_id');
    }
    public function getReceive(){
        return $this->belongsTo(MainUser::class,'receiver_id','user_id');
    }
}
