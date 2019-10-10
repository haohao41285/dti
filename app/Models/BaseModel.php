<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\MainActivityLog;

/**
 * Class BaseModel
 */
class BaseModel extends Model {

    public static function boot() {
        parent::boot();        
        static::creating(function($model) {
            //change to Auth::user() if you are using the default auth provider
            //$user = \JWTAuth::parseToken()->authenticate();
            if (isset($model->created_at))
                $model->created_at = date("Y-m-d H:i:s");
            if (isset($model->updated_at))
                $model->updated_at = date("Y-m-d H:i:s");
            $model->created_by = Auth::user()->user_id;
            $model->updated_by = Auth::user()->user_id;
            /*if ($model->created_by)
                $model->created_by = Auth::user()->user_id;
            if ($model->updated_by)
                $model->updated_by = Auth::user()->user_id;*/            
        });

        static::updating(function($model) {
            //change to Auth::user() if you are using the default auth provider
            //$user = \JWTAuth::parseToken()->authenticate();
            // if (isset($model->updated_at))
                if (isset($model->updated_at)){
                    $model->updated_at = date("Y-m-d H:i:s");
                }     
            // if (isset($model->updated_by))
                $model->updated_by = Auth::user()->user_id;
                // dd('updating');
                // $keyName = $model->getKeyName();
                // dd($model->$keyName);    
        });

        static::created(function($model){
            $string = (string)$model;
            $string = substr($string,1, 50)."..............";

            MainActivityLog::createActivityLog('Create',$model->table. " | create: ".$string);
        });

        static::updated(function($model){
            $string = (string)$model;
            $string = substr($string,1, 50)."..............";

            // echo ($message); die();
            MainActivityLog::createActivityLog('Update',$model->table. " | update: ".$string);
        });

        static::deleted(function($model){
            $string = (string)$model;
            $string = substr($string,1, 50)."..............";

            MainActivityLog::createActivityLog('Delete',$model->table. " | delete: ".$string);
        });

    }

}
