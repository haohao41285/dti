<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DataTables;
use App\Helpers\GeneralHelper;
use Validator;


class BuildCodeController extends Controller
{
    public function index(){
        return view('tools.build-code');
    }

    public function getThemeDatatable(){
        return MainTheme::getDatatable();
    }
}