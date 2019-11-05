<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenuDti extends Model
{
    protected $table = "main_menu_dti";
    protected $fillable = ['name','icon','link','parent_id','status'];

    public function getMenuChild(){
        return $this->hasMany(self::class,'parent_id','id');
    }
    public function scopeActive($query){
        return $query->where('status',1);
    }
    public function getPermission(){
        return $this->hasMany(MainPermissionDti::class,'menu_id','id');    
    }

}
