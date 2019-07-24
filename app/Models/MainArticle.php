<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainArticle extends Model
{
    protected $table = 'main_article';

    public $timestamps = true;

    protected $fillable = [
        'article_id',
        'article_title',
        'article_content',
        'article_dateposted',
        'created_at',
        'updated_at',
        'article_status'
    ];

    protected $guarded = [];

        
}