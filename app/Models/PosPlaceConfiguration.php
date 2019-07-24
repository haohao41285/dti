<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPlace
 */
class PosPlaceConfiguration extends BaseModel
{
    protected $table = 'pos_place_configuration';

    protected $primaryKey = 'place_id';

	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'pc_id',
        'pc_place_id',
        'pc_name',
        'pc_value',
        'created_at',
        'updated_at',
        'pc_status'
    ];

    protected $guarded = [];
        
}