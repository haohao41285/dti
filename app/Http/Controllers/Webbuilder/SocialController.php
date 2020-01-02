<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use yajra\Datatables\Datatables;
use App\Models\PosPlace;
use Session;

class SocialController extends Controller
{
    private $socialNetworkArr = [
        'Facebook',
        'Yelp',
        'Youtube',
        'Google Plus',
        'Linkedin',
        'Printerest',
        'Instagram',
        'VK',
        'Stack Over Flow',
        'Twitter',
        'Stumbleupon',
        'Tumblr',
        'Sound Cloud',
        'Behance',
        'Rss',
        'Flickr',
        'Vine',
        'Reddit',
        'Github'
    ];
    public function index(Request $request){

    	$input = $request->all();
        $social_list = $this->getList($input);

        return Datatables::of($social_list)
        	->make(true);
    }
    public function getList($input){
        $contact_list = PosPlace::where('place_id',$input['place_id'])->first()->place_social_network;

        if($contact_list != ""){

            $social_array = explode(";", $contact_list);

            $social_array = str_replace(";",",", $social_array);
        }
        $social_list = [];
        
        foreach ($this->socialNetworkArr as $key => $social) {

            $social_list[] = [
                'position' =>$key+1,
                'name' => $social,
                'link' => ($contact_list)?$social_array[$key]:""
            ];
        }
        return $social_list;
    }
    public function list(Request $request){

        $input = $request->all();
        $social_list = $this->getList($input);

        if(!isset($social_list))
            return response(['status'=>'error','message'=>'Failed! Get List Social Network Failed!']);

        return response(['social_list'=>$social_list]);
    }
    public function save(Request $request){

        $social_list = implode(';', $request->social_list);

        $place_update = PosPlace::where('place_id',Session::get('place_id'))
                 ->update(['place_social_network'=>$social_list]);

        if(!isset($place_update))
            return response(['status'=>'error','message'=>'Failed! Save Social Changes Failed!']);

        return response(['status'=>'success','message'=>'Successfully! Save Changes Successfully!']);
    }
}
