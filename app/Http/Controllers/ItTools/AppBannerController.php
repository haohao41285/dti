<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\MainTheme;
use DataTables;
use App\Helpers\ImagesHelper;

class AppBannerController extends Controller
{
    public function index(){
        return view('tools.app-banners');
    }
}