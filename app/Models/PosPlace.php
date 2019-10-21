<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosPlace
 */
class PosPlace extends BaseModel
{
    protected $table = 'pos_place';

    protected $primaryKey = 'place_id';

	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'place_customer_id',
        'place_logo',
        'place_favicon',
        'place_code',
        'place_name',
        'place_address',
        'place_count_turn',
        'place_email',
        'place_email_password',
        'place_email_driver',
        'place_email_host',
        'place_email_port',
        'place_email_encryption',
        'place_phone',
        'place_website',
        'place_taxcode',
        'place_country_id',
        'place_worker_mark_bonus',
        'place_actiondate',
        'place_actiondate_option',
        'place_period_overtime',
        'place_hour_overtime',
        'place_breaktime',
        'place_money_overtime',
        'place_interest',
        'place_description',
        'place_social_network',
        'place_id_license',
        'place_root_image',
        'place_url_plugin',
        'created_at',
        'updated_at',
        'place_status',
        'place_ip_license',
        'place_timezone',
        'place_auto_print',
        'place_orderservice_price',
        'place_theme_code'
    ];

    protected $guarded = [];

    public static function getPlaceIdByLicense($license){
        return self::select('place_id','place_ip_license','place_theme_code')
                    ->where('place_ip_license',$license)
                    ->where('place_status',1)
                    ->first();
    }
        
}