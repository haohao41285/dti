<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainGroupUser extends Model
{
    protected $table = "main_group_user";
    protected $fillable = [
        'gu_id',
        'gu_name',
        'gu_descript',
        'gu_role_new',
        'created_by',
        'updated_by',
        'gu_status'
    ];
    public function scopeActive($query){
        return $query->where('gu_status',1);
    }
}
