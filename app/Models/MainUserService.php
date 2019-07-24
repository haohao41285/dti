<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainUserService
 */
class MainUserService extends Model
{
    protected $table = 'main_userservice';

    public $timestamps = true;

    protected $fillable = [
        'userservice_id',
        'userservice_user_id',
        'userservice_place_id',
        'userservice_service_id',
        
        'userservice_date_end',
        'userservice_type',

        'userservice_status'
    ];

    protected $guarded = [];

        
}