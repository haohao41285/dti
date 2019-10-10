<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainTeamType extends Model
{
    protected $table = "main_team_type";
    protected $fillable = ['team_type_name','team_type_status','team_customer_status'];

    public function getTeams(){
        return $this->hasMany(MainTeam::class,'team_type','id');
    }
}
