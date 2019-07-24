<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosExpenseTemplate extends Model
{
    protected $table = 'pos_expense_template';

	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'ex_template_id',
        'ex_template_place_id',
        'ex_template_name',
        'ex_template_cost',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];
}
