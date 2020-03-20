<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainDiscount extends Model
{
    protected $table = "main_discount";
    protected $fillable = [
        'code',
        'date_start',
        'date_end',
        'description',
        'amount',
        'type',  // 0: percent ,1 : amount
        'document', // implode path of document
        'status', // 0: disable, 1: enable
    ];
}
