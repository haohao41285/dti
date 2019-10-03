<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DataTables;
use Validator;
use App\Models\MainTheme;
use App\Helpers\ImagesHelper;
use App\Models\MainApp;
use App\Models\MainAppBanners;

class AppBannerController extends Controller
{
    public function index(){
        return view('tools.app-banners');
    }

    public function appDataTable(){
        $app = MainApp::all();

        return DataTables::of($app)
        ->addColumn('action', function ($app){
                    return '<a class="btn btn-sm btn-secondary edit-app" data-id="'.$app->app_id.'" href="#"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-app" data-id="'.$app->app_id.'" href="#"><i class="fas fa-trash"></i></a>';
            })
        ->rawColumns(['action'])
        ->make(true);
    }
    /**
     * load app banners datatable by app_id
     * @param  $request->appId || null
     * @return 
     */
    public function appBannerDataTable(Request $request){
       $app = MainAppBanners::where('app_id',$request->appId)->get();

        return DataTables::of($app)
        ->addColumn('action', function ($app){
                    return '<a class="btn btn-sm btn-secondary edit-app-banner" data-id="'.$app->app_banner_id.'" href="#"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-app-banner" data-id="'.$app->app_banner_id.'" href="#"><i class="fas fa-trash"></i></a>';
            })
        ->editColumn('app_banner_image',function($app){
            return "<img style='height: 5rem;' src='".env('URL_FILE_VIEW').$app->app_banner_image."'>";
        })
        ->rawColumns(['action','app_banner_image'])
        ->make(true); 
    }
    /**
     * save main_app
     * @param  $request->action
     * @param  $request->appId || null
     * @param  $request->name
     * @param  $request->desc
     * @return json
     */
    public function saveApp(Request $request){
        $arr = [
            'app_name' => $request->name,
            'app_desc' => $request->desc,
        ];

        if($request->action == "Create"){   
            $app = MainApp::create($arr);
        } else {
            $app = MainApp::where('app_id',$request->appId)->update($arr);
        }
        
        return response()->json(['status'=>1,"msg"=>"Saved successfully!"],200);

    }
    /**
     * save main_app
     * @param  $request->action
     * @param  $request->appId || null
     * @param  $request->name
     * @param  $request->desc
     * @return json
     */
    public function saveAppBanner(Request $request){
        $validate = Validator::make($request->all(),[
            'image' => 'image|max:4096',
        ]);
        // dd($request->image);
        $error_array = [];

        if($validate->fails()){
            foreach ($validate->messages()->getMessages() as $messages) {
                $error_array[] = $messages;
            }
            return response()->json(['status'=>0,"msg"=>$error_array]);
        }

        if($request->hasFile('image')){
            $image = ImagesHelper::uploadImageToAPI($request->image,'theme');
        }

        $arr = [
            'app_id' => $request->appId,
            'app_banner_image' => $image ?? '',
            'app_banner_link' => $request->link,
        ];
        // dd($image);

        if($request->action == "Create"){   
            $app = MainAppBanners::create($arr);
        } else {
            if(empty($image)){
                unset($arr['app_banner_image']);
            }
            $app = MainAppBanners::where('app_banner_id',$request->appBannerId)->update($arr);
        }
        
        return response()->json(['status'=>1,"msg"=>"Saved successfully!"],200);

    }

    public function deleteApp(Request $request){
        if($request->id){
            MainApp::where('app_id',$request->id)->delete();
            return response()->json(['status'=>1,'msg'=>"Deleted successfully!"]);
        }
    }

    public function deleteAppBanner(Request $request){
        if($request->id){
            MainAppBanners::where('app_banner_id',$request->id)->delete();
            return response()->json(['status'=>1,'msg'=>"Deleted successfully!"]);
        }
    }
}