<?php

namespace App\Http\Controllers\Setting;

use App\Models\MainMenuDti;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainPermissionDti;

use Yajra\DataTables\DataTables;
use Validator;


class MenuController extends Controller
{
    public function index(){

        $menus =  \App\Helpers\MenuHelper::getMenuList();
        $menu_arr = [];
        $stt = 1;

        foreach($menus as $menu){
            $menu_arr[] = [
                'name' => $menu['text'],
                'icon' => $menu['icon'],
                'link' => $menu['link'],
                'parent_id' => 0,
                'status' => 1
            ];
            if(isset($menu['childrens'])){

                foreach ($menu['childrens'] as $child){

                    $menu_arr[] = [
                        'name' => $child['text'],
                        'icon' => "",
                        'link' => $child['link'],
                        'parent_id' => $stt,
                        'status' => 1
                    ];
                }
            }
            $stt++;
        }
//        return $menu_arr;
        MainMenuDti::truncate();
        MainMenuDti::insert($menu_arr);
    }
    public function setPermission(){

        $menus = MainMenuDti::all();
        $permissions = ['Read','Create','Update','Delete'];

        foreach ($menus as $menu){
            foreach ($permissions as $permission) {

                $permission_name = $menu->name." ".$permission;

                $permission_arr[] = [
                    'permission_slug' => str_slug($permission_name),
                    'permission_name' => $permission_name,
                    'menu_id' => $menu->id,
                    'status' => 1,
                ];
            }
        }
        MainPermissionDti::truncate();
        MainPermissionDti::insert($permission_arr);
    }
    public function permission(){
        $data['menu_list'] = MainMenuDti::all();
        return view('permission.list',$data);
    }
    public function permissionDatatable(Request $request){

        $permission_list = MainPermissionDti::with('getMenu');
        $permission_list = $permission_list->orderBy('menu_id','asc');

         return DataTables::of($permission_list)

             ->addColumn('action',function($row){
                 return '<a class="btn btn-sm  delete-permission"  title="Delete Permission"><i class="fas fa-trash"></i></a>';
             })
             ->addColumn('menu_name',function($row){
                 if($row->menu_id != "")
                     return $row->getMenu->name;
                 else
                     return "";

             })
             ->editColumn('status',function($row){
                 if($row->status == 1) $checked='checked';
                 else $checked="";
                 return '<input type="checkbox" id="'.$row->id.'" status="'.$row->status.'" class="js-switch"'.$checked.'/>';
             })
             ->rawColumns(['status','action'])
             ->make(true);
    }
    public function savePermission(Request $request){

        $permission_id = $request->permission_id;
        $menu_id = $request->menu_id;
        $input = $request->all();
        unset($input['_token']);
        $input['status'] = 1;
        unset($input['permission_id']);

        if($permission_id == 0){
            $validator = Validator::make($request->all(),[
                'permission_name' => 'required|unique:main_permission_dti,permission_name',
            ]);
            if($validator->fails())
                return response([
                    'status' => 'error',
                    'message' => $validator->getMessageBag()->toArray(),
                ]);
        }
        else{
            $check = MainPermissionDti::where([
                ['permission_name',$input['permission_name']],
                ['menu_id',$input['menu_id']]
            ])->first();
            if($check){
                return response(['status'=>'error','message'=>'This Permission Has Existed!']);
            }
        }
        $input['permission_slug'] = str_slug($input['permission_name']);
        if($permission_id == 0)
            $update_permission = MainPermissionDti::insert($input);
        else {
            $update_permission = MainPermissionDti::where('id',$permission_id)->update($input);
        }
        if(!$update_permission)
            return response(['status'=>'error','message'=>'Save Permission Failed!']);
        else
            return response(['status'=>'success','message'=>'Save Permission Successfully!']);
    }
    public function changeStatusPermission(Request $request){
        $permission_id = $request->permission_id;

        if($request->status == 1)
            $status = 0;
        else
            $status = 1;

        $update_permission = MainPermissionDti::find($permission_id)->update(['status'=>$status]);

        if(!$update_permission)
            return response(['status'=>'error','message'=>'Change Failed!']);
        else
            return response(['status'=>'success','message'=>'Change Successfully!']);
    }
    public function deletePermission(Request $request){

        $permission_id = $request->permission_id;

        $delete_permission = MainPermissionDti::find($permission_id)->delete();

        if(!$delete_permission)
            return response(['status'=>'error','message'=>'Delete Failed!']);
        else
           return response(['status'=>'success','message'=>'Delete Successfully!']);    }


}
