<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosTempmail
 */
class PosTempmail extends BaseModel
{
    protected $table = 'pos_tempmail';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'tm_id',
        'tm_place_id',
        'tm_special_id',
        'tm_subject',
        'tm_content',
        'tm_type',
        'created_at',
        'updated_at',
        'tm_status'
    ];

    protected $guarded = [];

        
}