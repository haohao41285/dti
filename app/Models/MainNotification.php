<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainNotification extends Model
{
    protected $table = 'main_notification';
    protected $fillable = [
        'content',
        'href_to',
        'receiver_id',
        'read_not',
        'created_by',
    ];
    public function scopeNotRead($query){
        return $query->where('read_not',0);
    }
}
