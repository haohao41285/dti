<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosWorkerCateservice
 */
class PosWorkerCateservice extends Model
{
    protected $table = 'pos_worker_cateservice';

    public $timestamps = true;

    protected $fillable = [
        'ws_id',
        'ws_place_id',
        'ws_worker_id',
        'ws_cate_id',
        'ws_turn',
        'ws_status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $guarded = [];

        
}
?>