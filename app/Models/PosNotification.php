<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosNotification extends Model
{
    protected $table = 'pos_notification';
    public $timestamps = true;
    
    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'id',
        'type_notification',
        'name_notification',
        'link_notification',
        'check_notification',
    ];

    protected $guarded = [];
}
