<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\MainTheme;
use DataTables;


class ItToolsController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function cloneWebsite()
    {
        return view('tools.clone-website');
    }
    
    public function updateWebsite()
    {
        return view('tools.update-website');
    }

    


    
}