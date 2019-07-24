<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainLicense
 */
class MainLicense extends Model
{
    protected $table = 'main_license';

    protected $primaryKey = 'license_id';

	public $timestamps = false;

    protected $fillable = [
        'license_name',
        'license_detail',
        'license_for_service',
        'license_sort'
    ];

    protected $guarded = [];

        
}