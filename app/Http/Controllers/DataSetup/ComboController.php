<?php

namespace App\Http\Controllers\DataSetup;

use App\Http\Controllers\Controller;


class ComboController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function index()
    {
        return view('datasetup.combo-list');
    }
    
    public function add()
    {
        return view('datasetup.combo-add');
    }
    
    public function edit()
    {
        return view('datasetup.combo-edit');
    }
}