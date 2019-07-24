<?php

namespace App\Http\Controllers\DataSetup;

use App\Http\Controllers\Controller;


class LicenseController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('datasetup.license-list');
    }
    
    public function generate()
    {
        return view('datasetup.license-generate');
    }    
}