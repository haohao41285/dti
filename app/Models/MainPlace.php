<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 


/**
 * Class PosPlace
 */
class MainPlace extends Model
{
    protected $table = 'pos_place';

    protected $primaryKey = 'place_id';

	public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'place_logo',
        'place_favicon',
        'place_code',
        'place_name',
        'place_address',
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
        
        /*'place_time_start',
        'place_time_end',*/
        
        /*'place_electric_cost',
        'place_water_cost',
        'place_rent_house',
        'place_supply',*/

        'place_interest',
        'place_description',
        'created_at',
        'updated_at',
        'place_status',
        'place_ip_license',
        'place_timezone',
        'place_auto_print',
        'place_orderservice_price'
    ];

    protected $guarded = [];
        
}