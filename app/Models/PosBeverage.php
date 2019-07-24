<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosBeverage
 */
class PosBeverage extends Model
{
    protected $table = 'pos_beverage';

    public $timestamps = true;

    protected $fillable = [
        'beverage_id',
        'beverage_place_id',
        'beverage_name',
        'beverage_price',
        'beverage_description',
        'created_at',
        'updated_at',
        'beverage_status'
    ];

    protected $guarded = [];

        
}