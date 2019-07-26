<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class RecentLogController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('logs.recent-logs');
    }
}