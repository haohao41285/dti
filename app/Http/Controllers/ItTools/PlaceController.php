<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\PosPlace;
use App\Models\PosUser;
use DataTables;
use App\Helpers\GeneralHelper;
use Validator;
use App\Http\Controllers\ItTools\WebsiteThemeController;

class PlaceController extends Controller
{
    public function index(){
        return view('tools.place');
    }

    public function getPlacesDatatable(){
        $places = PosPlace::select('place_id','place_name','place_address','place_email','place_phone','place_ip_license','created_at')
            ->where('place_status',1)
            ->get();

        return DataTables::of($places)
        ->editColumn('action',function($places){
            return '<a class="btn btn-sm btn-secondary view" data-id="'.$places->place_id.'" href="#"><i class="fas fa-user-cog"></i></a>
            <a class="btn btn-sm btn-secondary detail" data-id="'.$places->place_id.'" href="#"><i class="fas fa-eye"></i></a>
            <a class="btn btn-sm btn-secondary setting" data-license="'.$places->place_ip_license.'" href="#"><i class="fas fa-cogs"></i></a>';
        })
        ->editColumn('created_at',function($places){
            return format_datetime($places->created_at);
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    /**
     * get customer datatable by placeId
     * @param  $request->placeId
     * @return 
     */
    public function getUsersDatatable(Request $request){
        $customers = PosUser::select('user_id','user_nickname','user_phone','user_email','created_at')
                        ->where('user_status',1)
                        ->where('enable_status',1)
                        ->where('user_place_id',$request->placeId)
                        ->get();

        return DataTables::of($customers)
        ->editColumn('created_at',function($customers){
            return format_datetime($customers->created_at);
        })
        ->make(true);
    }   
    /**
     * update new password a user of places
     * @param  $request->placeId
     * @param  $request->userId
     * @param  $request->newPassword
     * @param  $request->confirmPassword
     * @return json
     */
    public function changeNewPassword(Request $request){
        $validate = Validator::make($request->all(),[
            'newPassword' => 'required',
            'confirmPassword' => 'required|same:newPassword',
        ]);
        $errorArr = [];
        if($validate->fails()){
            foreach ($validate->messages()->getMessages() as $messages) {
                $errorArr[] = $messages;
            }
            return response()->json(['status'=>0,'msg'=>$errorArr]);
        }

        $user = PosUser::where('user_id',$request->userId)
                        ->where('user_place_id',$request->placeId)
                        ->update([
                            'user_password' => bcrypt($request->newPassword),
                        ]);

        return response()->json(['status'=>1,'msg'=>"Changed successfully!"]); 

    }
    /**
     * get detail place by placeId
     * @param  $request->placeId
     * @return json
     */
    public function getDetailPlace(Request $request){
        $place = PosPlace::select(
                                    'place_name','place_address','place_taxcode',
                                    'place_email','place_amount','place_description',
                                    'place_actiondate','place_logo','place_favicon',
                                    'hide_service_price','place_worker_mark_bonus',
                                    'place_interest','place_website','place_phone'
                                )
                ->where('place_id',$request->placeId)
                ->where('place_status',1)
                ->first();
        $place_actiondate = json_decode($place->place_actiondate,true);


        $result = [
            'place' =>$place,
            'place_actiondate' => $place_actiondate,
        ];
        
        return response()->json(['status'=>1,'data'=>$result]); 
    }

    public function getThemeDatatable(){
        $theme = new WebsiteThemeController;
        return $theme->datatable();
    }
}