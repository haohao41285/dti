<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainCustomerNote extends Model
{
    protected $table = 'main_customer_note';
    protected $fillable = [
        'customer_id',
        'user_id',
        'team_id',
        'content'
    ];
}
