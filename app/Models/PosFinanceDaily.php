<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomerWeb
 */
class PosFinanceDaily extends Model
{
    protected $table = 'pos_finance_daily';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'rf_id',
        'rf_place_id',
        'rf_order_date',
        'rf_hour',
        'rf_weekday',
        'rf_total_ticket',
        'rf_total_walkin',
        'rf_total_new_customer',
        'rf_total_services',
        'rf_total_promo',
        'rf_total_tips',
        'rf_total_product',
        'rf_total_gross',
        'rf_total_giftcard',
        'rf_total_rs',
        'rf_total_expense',
        'rf_total_net',
        'rf_total_payment_method_cash',
        'rf_total_payment_method_credit_card',
        'rf_total_payment_check',
        'rf_total_payment_method_giftcard'
    ];

    protected $guarded = [];

        
}