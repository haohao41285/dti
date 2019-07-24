<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class MainFaq extends Model
{
    protected $table = 'main_faq';

    public $timestamps = true;

    protected $fillable = [
        'faq_id',
        'faq_title',
        'faq_content',
        'faq_subject',
        'faq_parent_id',
        'faq_user_id',
        'faq_phone',
        'faq_email',
        'faq_link',
        'faq_rate',
        'faq_user_id',
        'faq_date_posted',
        'created_at',
        'updated_at',
        'faq_status'
    ];

    protected $guarded = [];

        
}