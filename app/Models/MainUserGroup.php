<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosUsergroup
 */
class MainUserGroup extends Model
{
    protected $table = 'main_usergroup';

    public $timestamps = true;

    protected $fillable = [
        'usergroup_id',
        'usergroup_name',
        'usergroup_authentication',
        'usergroup_description',
        'usergroup_place_id',
        'usergroup_status'
    ];

    protected $guarded = [];

        
}