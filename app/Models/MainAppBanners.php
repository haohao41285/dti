<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainAppBanners extends BaseModel
{
    protected $table = 'main_app_banners';

    protected $primaryKey = 'app_banner_id';

    public $timestamps = false;

    protected $fillable = [
        'app_banner_id',
        'app_id',
        'app_banner_image',
        'app_banner_link',
    ];

    protected $guarded = [];

    protected $created_by = false;
    protected $updated_by = false;

        
}