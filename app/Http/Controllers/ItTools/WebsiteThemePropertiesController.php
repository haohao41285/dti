<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\MainThemeProperties;
use DataTables;

Class WebsiteThemePropertiesController extends Controller
{
    // public function index(){
    //     return view('tools.website-themes-properties');
    // }

    public function listThemePropertiesByThemeId(Request $request){
        $properties = MainThemeProperties::where('theme_id',$request->theme_id)->get();

        return response()->json(['status'=>1,'data'=>$properties]);
    }

    public function save(Request $request){
        if($request->hasFile('image')){
            $image = ImagesHelper::uploadImageToAPI($request->image,'theme_properties');
        }

        if($request->action == "Create"){
            //create
            
            $arr = [
                'theme_id' => $request->theme_id,
                'theme_properties_image' => $image ?? '',
            ];

            MainThemeProperties::create($arr);

        } else {
            //update
            $arr = [
                'theme_properties_image' => $image ?? '',
            ];

            MainThemeProperties::where('theme_properties_id',$request->theme_properties_id)->update($arr);
        }
        
        return response()->json(['status'=>1,'msg'=>$request->action.'d successfully!']);
    }

}