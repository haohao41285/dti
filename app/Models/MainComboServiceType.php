<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainComboServiceType extends Model
{
    protected $table= "main_combo_service_type";
    protected $fillable = [
        'name','status','description','created_by','updated_by','max_discount'
    ];
    public function getComboService(){
        return $this->hasMany(MainComboService::class,'cs_combo_service_type','id');
    }
    public function scopeActive($query){
        return $query->where('status',1);
    }
}
