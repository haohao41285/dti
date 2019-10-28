<?php

namespace App\Http\Controllers\Setting;

use App\Models\MainMenuDti;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainPermissionDti;

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
        MainPermissionDti::insert($permission_arr);
    }
    public function permission(){
        return view('permission.list');
    }

}
