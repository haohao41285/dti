<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainCategory extends Model
{
    protected $table = 'main_category';

    public $timestamps = true;

    protected $fillable = [
        'category_id',
        'category_name',
        'category_description',
        'created_at',
        'updated_at',
        'category_status'
    ];

    protected $guarded = [];

        
}