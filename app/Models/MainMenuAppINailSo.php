<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenuAppINailSo extends Model
{
    protected $table = "main_menu_app_inailso";
    protected $fillable = ['name','slug','status','position'];

    public function scopeActive($query){
    	
    	return $this->where('status',1);

    }
}
