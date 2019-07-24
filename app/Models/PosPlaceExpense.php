<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPlace
 */
class PosPlaceExpense extends BaseModel
{
    protected $table = 'pos_place_expense';

    protected $primaryKey = 'pe_id';

	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'pe_id',
        'pe_place_id',
        'pe_name',
        'pe_cost',
        'pe_date',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'pe_status'
    ];

    protected $guarded = [];
    
    public function placeExpenseUser()
    {
        return $this->hasOne('App\Models\PosUser', 'user_id', 'updated_by');
    }
}