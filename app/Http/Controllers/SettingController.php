<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class SettingController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('setting.index');
    }

    public function setupBackground(){

    }
}