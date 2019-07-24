<?php

namespace App\Http\Controllers\DataSetup;

use App\Http\Controllers\Controller;


class ThemeController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('datasetup.theme-list');
    }
    
    public function add()
    {
        return view('datasetup.theme-add');
    }
    
    public function edit()
    {
        return view('datasetup.theme-edit');
    }
}