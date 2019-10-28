<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTemplate extends Model
{
    protected $table = 'pos_template';

    public $timestamps = true;

    protected $fillable = [
        'template_id',
        'template_place_id',
        'template_title',
        'template_discount',
        'template_type',
        'template_list_service',
        'template_linkimage',
        'created_at',
        'updated_at',
        'template_status',
        'template_type_id',
        'template_table_type',
    ];

    protected $guarded = [];

        
}