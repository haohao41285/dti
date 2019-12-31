<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PosCateservice;
use yajra\Datatables\Datatables;

class CateServiceController extends Controller
{
    public function index(Request $request){
    	$cateservice_item = PosCateservice::leftJoin('pos_user',function($join){
                        $join->on('pos_cateservice.created_by','=','pos_user.user_id')
                        ->on('pos_cateservice.cateservice_place_id','=','pos_user.user_place_id');
                    })
                    ->where('pos_cateservice.cateservice_place_id', $request->place_id)
                    ->where('pos_cateservice.cateservice_status',1)
                    ->get();

        return Datatables::of($cateservice_item)
            ->editColumn('cateservice_name',function($row){
                return  "<a href='".route('cateservice',$row->cateservice_id)."'>".$row->cateservice_name." </a>";
            })
            ->editColumn('cateservice_description',function($row){
                $result=substr($row->cateservice_description,0,20);
                $dot="";
                if(strlen($row->cateservice_description)>20)
                {
                    $dot="...";
                }
                return $result."".$dot;
            })
            ->editColumn('cateservice_image',function($row){
                if(!empty($row->cateservice_image))
                return  "<img src=".config('app.url_file_view').$row->cateservice_image." width='100px' alt=''>  ";
                else
                    return "";
            })
            ->editColumn('updated_at',function($row){
                return  format_datetime($row->updated_at)." by ".$row->user_nickname; 
            })
             ->addColumn('action', function($row){
                return '<a href="'.route('cateservice',$row->cateservice_id).'" class="btn btn-sm btn-secondary"><i class="fa fa-edit"></i></a>
                        <a href="#" class="delete-cateservice btn btn-sm btn-secondary" id="'.$row->cateservice_id.'"><i class="fa fa-trash-o"></i></a>';
            })
            ->rawColumns(['cateservice_name','cateservice_image' ,'action','cateservice_description'])
            ->make(true);
    }
    public function edit(Request $request,$id=0) {
        if($id>0){
            $cateservice_item = PosCateservice::where('cateservice_place_id',$this->getCurrentPlaceId())
                                ->where('cateservice_id',$id)
                                ->first();
            $cateservice_date = format_date($cateservice_item->cateservice_date);
            return view('webbuilder.cateservice_edit',compact('cateservice_item','id','cateservice_date'));
        } else {
            return view('webbuilder.cateservice_edit',compact('id'));
        }

    }
}
