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
    	'team_email',
        'service_permission',
        'team_cskh_id',
        'sale_date',
        'other_date'
    ];
    public function getTeamType(){
        return $this->belongsTo(MainTeamType::class,'team_type','id');
    }
    public function scopeActive($query){
        return $query->where('team_status',1);
    }
    public function getLeader(){
        return $this->belongsTo(MainUser::class,'team_leader','user_id');
    }
    public function getCskhTeam(){
        return $this->belongsTo(self::class,'team_cskh_id','id');
    }
    public function getUserOfTeam(){
        return $this->hasMany(MainUser::class,'user_team','id');
    }

}
