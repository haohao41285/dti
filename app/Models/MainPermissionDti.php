<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainPermissionDti extends Model
{
    protected $table = "main_permission_dti";
    protected $fillable = [
        'permission_slug',
        'permission_name',
        'menu_id',
        'status'
    ];
    public function scopeActive($query){
        return $query->where('status',1);
    }
}
