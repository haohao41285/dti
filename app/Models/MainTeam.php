<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainTeam extends Model
{
    protected $table = "main_team";
    protected $fillable = [
    	'team_name',
    	'team_leader',
    	'team_status',
    	'team_customer_status',
    	'team_type',
    	'team_email'
    ];
    public function getTeamType(){
        return $this->belongsTo(MainTeamType::class,'team_type','id');
    }

}
