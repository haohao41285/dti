<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainBanner extends Model
{
    protected $table = 'main_banner';

    public $timestamps = true;

    protected $fillable = [
        'banner_id',
        'banner_name',
        'banner_description',
        'banner_link_image',
        'banner_service_id',
        'created_at',
        'updated_at',
        'banner_status'
    ];

    protected $guarded = [];

        
}