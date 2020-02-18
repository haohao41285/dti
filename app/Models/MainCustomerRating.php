<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCustomerRating extends Model
{
    protected $table = "main_customer_rating";
    protected $fillable = [
    	'order_id',
    	'note',
    	'created_at',
    	'rating_level'
    ];
}
