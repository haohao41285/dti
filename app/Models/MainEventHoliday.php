<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainEventHoliday extends Model
{
    protected $table = 'main_event_holiday';
    protected $fillable = [
        'name',
        'date',
        'created_by',
        'updated_by',
        'image',
        'status'
    ];

    public function scopeActive($query){
    	return $query->where('status',1);
    }
}
