<?php

namespace App\Http\Controllers\WebBuilder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use yajra\Datatables\Datatables;
use App\Models\PosWebSeo;
use Session;

class WebSeoController extends Controller
{
    public function save(Request $request){
        $webSeo = PosWebSeo::where('web_seo_place_id',Session::get('place_id'))
                        ->first();
        if(!$webSeo){
            $webSeo = new PosWebSeo;
            $webSeo->web_seo_place_id = Session::get('place_id');   
        }
        $webSeo->web_seo_descript = $request->description;
        $webSeo->web_seo_meta = $request->keywords;
        $webSeo->save();

        if(!isset($webSeo))
        	return response(['status'=>'error','message'=>'Failed! Save Failed!']);
        
        return response(['status'=>'success','message'=>'Successfully!']);
    }
}
