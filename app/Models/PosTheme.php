<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class PosTheme extends Model
{
    protected $table = 'pos_theme';

    public $timestamps = true;

    protected $fillable = [
        'theme_id',
        'theme_place_id',
        'theme_price',
        'theme_date_buy',
        'theme_main_theme_id',
        'theme_active',
        'theme_descript',
        'created_at',
        'updated_at',
        'theme_status'
    ];

    protected $guarded = [];

        
}