<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainCategoryNews extends Model
{
    protected $table = 'main_category_news';

    public $timestamps = true;

    protected $fillable = [
        'categorynews_id',
        'categorynews_name',
        'categorynews_description',
        'categorynews_parent_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'categorynews_status'
    ];

    protected $guarded = [];

        
}