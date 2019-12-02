<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainUserReview extends Model
{
    protected $table = 'main_user_reviews';
    protected $fillable = [
        'task_id',
        'review_id',
        'user_id',
        'place_id',
        'note',
        'status'
    ];
}
