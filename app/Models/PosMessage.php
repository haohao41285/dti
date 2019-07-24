<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPlace
 */
class PosMessage extends BaseModel
{
    protected $table = 'pos_message';


	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'message_id',
        'message_place_id',
        'message_description',
        'message_date_start',
        'message_date_end',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'message_status'
    ];

    protected $guarded = [];
        
}