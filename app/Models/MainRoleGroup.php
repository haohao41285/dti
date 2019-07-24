<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainRoleGroup extends Model
{
    protected $table = 'main_role_group';

    public $timestamps = false;

    protected $fillable = [
        'gr_id',
        'gr_usergroup_id',
        'gr_role_id',
        'gr_place_id'
    ];

    protected $guarded = [];

        
}