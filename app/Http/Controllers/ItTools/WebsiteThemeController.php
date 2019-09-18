<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\MainTheme;
use DataTables;

Class WebsiteThemeController extends Controller
{
    public function index(){
        return view('tools.website-themes');
    }

    public function datatable(){
        $data = MainTheme::all();

        return DataTables::of($data)
        ->editColumn('theme_image',function($data){
            return "<img  src='".$data->theme_image."' alt=''>";
        })
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->editColumn('theme_status',function($data){
            $checked = null;
            if($data->theme_status == 1)
                $checked = "checked";
            return '<input type="checkbox" class="js-switch-datatable" '.$checked.' />';
        })
        ->addColumn('action', function ($data){
                    return '<a class="btn btn-sm btn-secondary" target="_blank" href="'.$data->theme_url.'"><i class="fas fa-link"></i> DEMO</a>
                    <a class="btn btn-sm btn-secondary edit" data="'.$data->theme_id.'" href="#"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete" data="'.$data->theme_id.'" href="#"><i class="fas fa-trash"></i></a>
                    <a class="btn btn-sm btn-secondary setup-properties" data="'.$data->theme_id.'" href="#"><i class="fas fa-link"></i> Setup Properties</a>';
            })
        ->rawColumns(['theme_image','theme_status','action'])
        ->make(true);
    }
    /**
     * get theme by id
     * @param  Request $request->id
     * @return json
     */
    public function getById(Request $request){
        $mainTheme = MainTheme::where('theme_id',$request->id)->first();

        return response()->json(['status'=>1,'data'=>$mainTheme]);
    }
}