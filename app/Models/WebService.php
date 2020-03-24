<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebService extends Model
{
    protected $table = 'web_services';
    protected $fillable = [
        'web_service_id',
        'web_service_type_id',
        'web_service_image',
        'web_service_status',
        'web_service_descript'
    ];
}
