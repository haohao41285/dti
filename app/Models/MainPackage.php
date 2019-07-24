<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainPackage extends BaseModel
{
    protected $table = 'main_package';

    public $timestamps = true;

    protected $fillable = [
        'package_id',
        'package_name',
        'package_listservice_id',
        'package_listservicedetail_id',
        'package_description',
        'created_at',
        'updated_at',
        'package_status'
    ];

    protected $guarded = [];

        
}