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
            MainActivityLog::createActivityLog('Create',$model->table);
        });

        static::updated(function($model){
            MainActivityLog::createActivityLog('Update',$model->table);
        });

        static::deleted(function($model){
            MainActivityLog::createActivityLog('Delete',$model->table);
        });

    }

}
