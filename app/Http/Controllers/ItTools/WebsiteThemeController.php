<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\MainTheme;
use DataTables;
use App\Helpers\ImagesHelper;

Class WebsiteThemeController extends Controller
{
    public function index(){
        return view('tools.website-themes');
    }

    public function datatable(){
        $data = MainTheme::all();

        return DataTables::of($data)
        ->editColumn('theme_image',function($data){
            return "<img style='height: 4rem;' src='".env('URL_FILE_VIEW').$data->theme_image."' alt=''>";
        })
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->editColumn('theme_status',function($data){
            $checked = null;
            if($data->theme_status == 1)
                $checked = "checked";
            return '<input type="checkbox" class="checkboxToggleDatatable changeStatus" '.$checked.' data="'.$data->theme_id.'" />';
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

    public function delete(Request $request){
        $mainTheme = MainTheme::where('theme_id',$request->id)->first();

        $mainTheme->delete();

        return "success";
    }

    public function changeStatusThemes(Request $request){
        $mainTheme = MainTheme::where('theme_id',$request->id)->first();

        $mainTheme->update(['theme_status'=>$request->check]);

        return "success";
    }

    public function save(Request $request){

        if($request->hasFile('image')){
            $image = ImagesHelper::uploadImageToAPI($request->image,'theme');
        }

        if($request->action == "Create"){
            //create
            $themeId = MainTheme::select('theme_id')->max('theme_id')+1;
            
            $arr = [
                'theme_id' => $themeId,
                'theme_name' => $request->name,
                'theme_name_temp' => $request->code,
                'theme_url' => $request->url,
                'theme_price' => $request->price,
                'theme_descript' => $request->description,
                'theme_license' => $request->license,
                'theme_status' => $request->status ?? 0,
                'theme_image' => $image ?? '',
                'created_by' => 0,
                'updated_by' => 0,
            ];

            MainTheme::create($arr);

        } else {
            //update
            $arr = [
                'theme_name' => $request->name,
                'theme_name_temp' => $request->code,
                'theme_url' => $request->url,
                'theme_price' => $request->price,
                'theme_descript' => $request->description,
                'theme_license' => $request->license,
                'theme_status' => $request->status ?? 0,
                'theme_image' => $image ?? '',
            ];

            MainTheme::where('theme_id',$request->theme_id)->update($arr);
        }
        
        return response()->json(['status'=>1,'msg'=>$request->action.'d successfully!']);
    }
}