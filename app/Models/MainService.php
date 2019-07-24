<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainService extends Model
{
    protected $table = 'main_service';

    public $timestamps = true;

    protected $fillable = [
        'service_id',
        'service_name',
        'service_parent_id',
        'service_description',
        'service_price',
        'service_listservicedetail_id',
        'created_at',
        'updated_at',
        'service_status'
    ];

    protected $guarded = [];

        
}