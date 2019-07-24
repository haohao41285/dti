<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPlace
 */
class PosMenu extends BaseModel
{
    protected $table = 'pos_menu';


	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'menu_id',
        'menu_place_id',
        'menu_parent_id',
        'menu_name',
        'menu_index',
        'menu_url',
        'menu_descript',
        'menu_image',
        'menu_list_image',
        'menu_type',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'menu_status'
    ];

    protected $guarded = [];
        
}