<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenuDti extends Model
{
    protected $table = "main_menu_dti";
    protected $fillable = ['name','icon','link','parent_id','status'];
}
