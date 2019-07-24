<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosWorker
 */
class PosWelcomeDetail extends BaseModel
{
  protected $table = 'pos_welcome_detail';
  protected $prefix = 'wd_';

  public $timestamps = true;

  public static function boot()
    {
        parent::boot();
    }

  protected $fillable = [
    'wd_id',
    'wd_place_id',
    'wd_welcome_id',
    'wd_service_id',
    'wd_package_id',
    'wd_packagedetail_id',
    'wd_worker_id',
    'wd_datetime',
    'wd_price',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
    'wd_status'
  ];

  protected $guarded = [
    'wd_id',
    'wd_place_id'
  ];

  public function getPrefix()
  {
    return $this->prefix;
  }
    
}