<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\GeneralHelper;
use Auth;
use DataTables;

class MainActivityLog extends Model
{
    protected $table = 'main_activity_log';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'user_id',
        'type',
        'message',
        'ip_address',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public static function createActivityLog($type = null, $message = null){
        $userId = Auth::user()->user_id;
        $id = self::getMaxId($userId);
        $ipAddress = GeneralHelper::getIpAddress();

        $arr = [
            'id' => $id + 1,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'ip_address' => $ipAddress,
        ];

        self::create($arr);
    }

    public static function getMaxId($userId){
        return self::select('id')->where('user_id',$userId)->max('id');
    }

    public static function getDatatable(){
        $userId = Auth::user()->user_id;

        $activityLog = MainActivityLog::select('main_user.user_nickname','main_activity_log.*')
                        ->where('main_activity_log.user_id',$userId)
                        ->join('main_user','main_user.user_id','main_activity_log.user_id')
                        ->get();

        return DataTables::of($activityLog)
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->make(true);
    }


        
}