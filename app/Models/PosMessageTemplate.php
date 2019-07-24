<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosMessageTemplate
 */
class PosMessageTemplate extends BaseModel
{
    protected $table = 'pos_message_template';

    public $timestamps = true;

    protected $fillable = [
        'mt_id',
        'mt_place_id',
        'mt_name',
        'mt_description',
        'mt_type',
        'mt_status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $guarded = [];

        
}
?>