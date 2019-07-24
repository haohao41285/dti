<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MainModule
 */
class MainModule extends Model
{
    protected $table = 'main_module';

    protected $primaryKey = 'module_id';

	public $timestamps = false;

    protected $fillable = [
        'module_name'
    ];

    protected $guarded = [];

        
}