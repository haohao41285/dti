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
        'host',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public static function createActivityLog($type = null, $message = null){
        $userId = Auth::user()->user_id;
        $id = self::getMaxId($userId);
        $host = GeneralHelper::getIpAddress();

        $arr = [
            'id' => $id + 1,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'host' => $host,
        ];

        self::create($arr);
    }

    public static function getMaxId($userId){
        return self::select('id')->where('user_id',$userId)->max('id');
    }

    public static function getDatatable(){
        $userId = Auth::user()->user_id;

        $activityLog = MainActivityLog::where('user_id',$userId)->get();

        return DataTables::of($activityLog)
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->make(true);
    }


        
}