<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MainActivityLog;
use Auth;


class RecentLogController extends Controller {   
    public function __construct()
    {
    	
    }
    
    public function index()
    {
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