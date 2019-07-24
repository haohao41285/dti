<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosWorker
 */
class PosWelcome extends BaseModel
{
  protected $table = 'pos_welcome';
  protected $prefix = 'welcome_';

  public $timestamps = true;

  public static function boot()
    {
        parent::boot();
    }

  protected $fillable = [
    'welcome_id',
    'welcome_place_id',
    'welcome_customer_id',
    'welcome_promotion_id',
    'welcome_beverage_id',
    'welcome_beverage_price',
    'welcome_datetime',
    'welcome_reason',
    'welcome_coupon_code',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
    'worker_status'
  ];

  protected $guarded = [
    'welcome_id',
    'welcome_place_id'
  ];

  public function getPrefix()
  {
    return $this->prefix;
  }
    
}