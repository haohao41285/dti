<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainMenu extends Model
{
    protected $table = 'main_menu';

    public $timestamps = true;

    protected $fillable = [
        'menu_id',
        'menu_name',
        'menu_link',
        'menu_parent_id',
        'menu_description',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'menu_status'
    ];

    protected $guarded = [];

        
}