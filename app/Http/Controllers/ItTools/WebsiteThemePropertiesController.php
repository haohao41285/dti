<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\MainThemeProperties;
use DataTables;
use App\Helpers\ImagesHelper;

Class WebsiteThemePropertiesController extends Controller
{
    /**
     * @param  $request->theme_id
     * @return json
     */
    public function listThemePropertiesByThemeId(Request $request){
        $properties = MainThemeProperties::where('theme_id',$request->theme_id)->get();

        return response()->json(['status'=>1,'data'=>$properties]);
    }
    /**
     * save theme property
     */
    public function save(Request $request){
        // dd($request->all());
        if($request->hasFile('image')){
            $image = ImagesHelper::uploadImageToAPI($request->image,'theme_properties');
        }

        if($request->action == "Create"){
            //create
            $arr = [
                'theme_id' => $request->theme_id,
                'theme_properties_name' => $request->theme_properties_name ?? '',
                'theme_properties_image' => $image ?? '',
            ];

            MainThemeProperties::create($arr);

        } else {
            //update
            $arr = [
                'theme_properties_name' => $request->theme_properties_name ?? '',
                'theme_properties_image' => $image ?? '',
            ];
            if(!$request->hasFile('image')){
                unset($arr['theme_properties_image']);
            }
            // dd($request->properties_id);
            MainThemeProperties::where('theme_properties_id',$request->theme_properties_id)->update($arr);
        }
        
        return response()->json(['status'=>1,'msg'=>$request->action.'d successfully!']);
    }

    public function edit(Request $request){
        if($request->properties_id){
            $property = MainThemeProperties::where('theme_properties_id',$request->properties_id)->first();

            return response()->json(['status'=>1,'data'=>$property]);
        }
    }

    public function delete(Request $request){
        if($request->properties_id){
            $property = MainThemeProperties::where('theme_properties_id',$request->properties_id)->delete();

            return response()->json(['status'=>1,'msg'=>"Deleted successfully!"]);
        }
    }
    /**
     * @param  $request->propertyId
     * @return json
     */
    public function listValueProperties(Request $request){
        if($request->propertyId){
            $result = [];

            $value = MainThemeProperties::select('theme_properties_value')
                    ->where('theme_properties_id',$request->propertyId)
                    ->first();

            $result = json_decode($value->theme_properties_value,true);   

            return response()->json(['status'=>1,'data'=>$result]);
        }
    }
    /**
     * @param  request->propertyId
     * @param  request->name
     * @param  request->value || request->image
     * @param  request->action
     * @return json
     */
    public function saveValueProperties(Request $request){
        if($request->hasFile('image')){
            $value = ImagesHelper::uploadImageToAPI($request->image,'value_properties');
        } else {
            $value = $request->value;
        }

        $property = MainThemeProperties::where('theme_properties_id',$request->propertyId)
                    ->first();

        $arrValue = json_decode($property->theme_properties_value,true);

        $arrValue[$request->name] = $request->value;

        $jsonValue = json_encode($arrValue);

        $property->theme_properties_value = $jsonValue;

        $property->save();
        
        return response()->json(['status'=>1,'msg'=>'Saved successfully!']);
    }

}