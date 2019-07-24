<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosWorkerAdvertise
 */
class PosWorkerAdvertise extends Model
{
    protected $table = 'pos_worker_advertise';

    public $timestamps = true;

    protected $fillable = [
    	'wa_id',
    	'wa_place_id',
    	'wa_worker_id',
    	'wa_lstservice_id',
    	'wa_description',
    	'wa_image',
    	'wa_date_start',
    	'wa_date_end',
    	'created_at',
    	'updated_at',
    	'created_by',
    	'updated_by',
    	'wa_status'
    ];

    protected $guarded = [];

        
}
?>