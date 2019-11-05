<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Gate;

class MainCustomerBought extends Model
{
    protected $table = 'main_customer_bought';

    public $timestamps = true;

    protected $fillable = [
        'cb_id',
        'cb_customer_id',
        'cb_serviceamount_id',
        'cb_paid',
        'cb_amount',
        'cb_charge',
        'cb_cashback',
        'cb_payment_method',
        'cb_card_type',
        'cb_date_start',
        'cb_date_expire',
        'cb_date_end',
        'cb_amount_deal',
        'cb_card_number',
        'created_at',
        'updated_at',
        'cb_status'
    ];

    protected $guarded = [];

    public static function getNearlyExpired(){
        $dateNow = format_date_db(get_nowDate());
        $dateNowAdd = format_date_db(get_nowDate()->addMonth());

        return self::select('cb_id')
                ->whereBetween('cb_date_expire',[$dateNow,$dateNowAdd])
                ->where('cb_status',1)
                ->count();
    }



}
