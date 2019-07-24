<?php

namespace App\Http\Controllers\DataSetup;

use App\Http\Controllers\Controller;


class ServiceController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('datasetup.service-list');
    }
    
    public function add()
    {
        return view('datasetup.service-add');
    }
    
    public function edit()
    {
        return view('datasetup.service-edit');
    }
}