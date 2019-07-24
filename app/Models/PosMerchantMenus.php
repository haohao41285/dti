<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosUsergroup
 */
class PosMerchantMenus extends BaseModel
{
    protected $table = 'pos_merchant_menus';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'mer_menu_id',
        'mer_menu_parent_id',
        'mer_menu_index',
        'mer_menu_text',
        'mer_menu_class',
        'mer_menu_url'
    ];

    protected $guarded = [];      
}