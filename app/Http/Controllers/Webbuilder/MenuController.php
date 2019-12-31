<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PosMenu;
use yajra\Datatables\Datatables;

class MenuController extends Controller
{
     public function index(Request $request)
    {
        $menu_item = PosMenu::join('pos_user',function($join){
            $join->on('pos_menu.created_by','=','pos_user.user_id')->on('pos_menu.menu_place_id','=','pos_user.user_place_id');
        })
          ->where('pos_menu.menu_place_id',$request->place_id)
          ->where('menu_status',1)
          ->select('pos_user.user_nickname','pos_menu.*')
          ->get();

          // echo $menu_item; die();

        return Datatables::of($menu_item)
            ->editColumn('menu_name',function($row){
                return "<a href='".route('menu',$row->menu_id)."'>".$row->menu_name."</a>";
            })
            ->addColumn('parent_name',function($row){
                $parent_item = PosMenu::where('menu_id',$row->menu_parent_id)
                                ->where('menu_place_id',$request->place_id)
                                ->first();
                if(isset($parent_item->menu_name)){
                    return $parent_item->menu_name ;
                }else { return ""; }
            })
            ->addColumn('menu_type',function($menu_item){
                     $checked = "";
                if ($menu_item->menu_type == 1) {
                    $checked = 'checked';
                }else {
                    // $checked = 'checked';
                }
                    return "<input type='checkbox' value='".$menu_item->menu_id."' id='".$menu_item->menu_id."' class='js-switch show_id' data=".$menu_item->menu_type." ".$checked."/>";
                
                })
            ->editColumn('updated_at',function($row){
                return format_datetime($row->updated_at)." by ".$row->user_nickname;
            })
            ->addColumn('action',function($row){
                return '<a href="'.route('menu',$row->menu_id).'"  class="btn btn-sm btn-secondary" ><i class="fa fa-edit"></i></a>
                        <a href="#" class="delete-menu btn btn-sm btn-secondary" id="'.$row->menu_id.'"><i class="fa fa-trash-o"></i></a>';
            })
            ->rawColumns(['menu_name','menu_type','action'])
            ->make(true);
    }
}
