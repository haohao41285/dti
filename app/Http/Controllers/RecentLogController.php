<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MainActivityLog;
use Auth;
use Gate;


class RecentLogController extends Controller {
    public function __construct()
    {

    }

    public function index()
    {
        if(Gate::denies('permission','recent-logs'))
            return doNotPermission();

        return view('logs.recent-logs');
    }

    public function datatable(){
    	return MainActivityLog::getDatatable();
    }

    public function activityLog(){
        return view('logs.activity-log');
    }

    public function activityLogDatatable(){
        return MainActivityLog::getActivityLogDatatable();
    }

}
