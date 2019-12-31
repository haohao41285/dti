<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use yajra\Datatables\Datatables;
use App\Models\PosPlace;

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
    	$contact_list = PosPlace::where('place_id',$request->place_id)->first()->place_social_network;

        if($contact_list != ""){

            $social_array = explode(";", $contact_list);

            $social_array = str_replace(";",",", $social_array);
        }
        $socialNetworkArr = [];
        
        foreach ($this->socialNetworkArr as $key => $social) {

            $socialNetworkArr[] = [
            	'position' =>$key+1,
            	'name' => $social,
            	'link' => ($contact_list)?$social_array[$key]:""
            ];
        }
        return Datatables::of($socialNetworkArr)
        	->make(true);
    }
}
