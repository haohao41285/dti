<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainContactSale
 */
class MainContactSale extends Model
{
    protected $table = 'main_contact_sale';

    public $timestamps = true;

    protected $fillable = [
        'cs_id',
        'cs_customer_id',
        'cs_firstname',
        'cs_lastname',
        'cs_phone',
        'cs_email',
        'cs_iso',
        'cs_experience',
        'cs_curent_job',
        'cs_content',
        'cs_country_code',
        'created_by',
        'updated_by',
        'cs_status'
    ];

    protected $guarded = [];

        
}