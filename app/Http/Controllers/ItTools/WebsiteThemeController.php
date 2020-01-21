<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\MainTheme;
use DataTables;
use App\Helpers\ImagesHelper;
use Gate;

Class WebsiteThemeController extends Controller
{
    public function index(){

        if(Gate::denies('permission','website-theme'))
            return doNotPermission();

        return view('tools.website-themes');
    }

    public function datatable(){

        if(Gate::denies('permission','website-theme'))
            return doNotPermission();
        
        return MainTheme::getDatatable();
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

        if($mainTheme->theme_status == 1){
            $status = 0;
        } else {
            $status = 1;
        }

        $mainTheme->update(['theme_status'=>$status]);

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
                'theme_booking_css' => $request->booking_css,
                'theme_booking_js' => $request->booking_js,
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
            $mainTheme = MainTheme::where('theme_id',$request->theme_id)->first(); 
            $arr = [
                'theme_name' => $request->name,
                'theme_name_temp' => $request->code,
                'theme_url' => $request->url,
                'theme_price' => $request->price,
                'theme_booking_css' => $request->booking_css,
                'theme_booking_js' => $request->booking_js,
                'theme_descript' => $request->description,
                'theme_license' => $request->license,
                'theme_status' => $request->status ?? 0,
                'theme_image' => $image ?? $mainTheme->theme_image,
            ];

            //$mainTheme = MainTheme::where('theme_id',$request->theme_id)->first(); 
            $mainTheme->update($arr);
        }
        
        return response()->json(['status'=>1,'msg'=>$request->action.'d successfully!']);
    }
}