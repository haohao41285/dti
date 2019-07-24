<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainRole extends Model
{
    protected $table = 'main_role';

    // public $timestamps = true;

    protected $fillable = [
        'role_id',
        'role_name',
        'role_code',
        'role_parent_code',
        'role_scope',
        'role_link',
        'role_description',
        'role_status'
    ];

    protected $guarded = [];

        
}