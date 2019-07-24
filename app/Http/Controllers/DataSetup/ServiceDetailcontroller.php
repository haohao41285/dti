<?php

namespace App\Http\Controllers\DataSetup;

use App\Http\Controllers\Controller;


class ServiceDetailController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('datasetup.servicedetail-list');
    }
    
    public function add()
    {
        return view('datasetup.servicedetail-add');
    }
    
    public function edit()
    {
        return view('datasetup.servicedetail-edit');
    }
}