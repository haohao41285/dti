<?php

namespace App\Http\Controllers\WebBuilder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use yajra\Datatables\Datatables;
use App\Models\PosMenu;
use App\Helpers\ImagesHelper;
use Session;
use Validator;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $menu_item = PosMenu::join('main_user',function($join){
            $join->on('pos_menu.created_by','=','main_user.user_id');
        })
          ->where('pos_menu.menu_place_id',$request->place_id)
          ->where('menu_status',1)
          ->select('main_user.user_nickname','pos_menu.*')
          ->get();

        return Datatables::of($menu_item)
            ->editColumn('menu_name',function($row){
                return "<a href='".route('places.menus.edit',[Session::get('place_id'),$row->menu_id])."'>".$row->menu_name."</a>";
            })
            ->addColumn('parent_name',function($row){
                $parent_item = PosMenu::where('menu_id',$row->menu_parent_id)
                                ->where('menu_place_id',Session::get('place_id') )
                                ->first();
                if(isset($parent_item->menu_name)){
                    return $parent_item->menu_name ;
                }else { return ""; }
            })
            ->addColumn('enable_status',function($menu_item){
                     $checked = "";
                if ($menu_item->enable_status == 1) {
                    $checked = 'checked';
                }
                    return '<div class="custom-control custom-switch">
                          <input type="checkbox" value="'.$menu_item->menu_id.'" name="menu_status" status="'.$menu_item->enable_status.'" id="menu_status_'.$menu_item->menu_id.'" class="custom-control-input show_id" data='.$menu_item->enable_status.' '.$checked.'/>
                          <label class="custom-control-label" for="menu_status_'.$menu_item->menu_id.'"></label>
                        </div>';
                
                })
            ->editColumn('updated_at',function($row){
                return format_datetime($row->updated_at)." by ".$row->user_nickname;
            })
            ->addColumn('action',function($row){
                return '<a href="'.route('places.menus.edit',[Session::get('place_id'),$row->menu_id]).'"  class="btn btn-sm btn-secondary" ><i class="fa fa-edit"></i></a>
                        <a href="#" class="delete-menu btn btn-sm btn-secondary" id="'.$row->menu_id.'"><i class="fa fa-trash"></i></a>';
            })
            ->rawColumns(['menu_name','enable_status','action'])
            ->make(true);
    }
    
    public function edit(Request $request,$place_id,$id = 0) {
        $list_menu = PosMenu::where('menu_place_id',Session::get('place_id'))->where('menu_status',1)->get();
        if($id>0){
            $menu_item = PosMenu::where('menu_place_id',Session::get('place_id'))
                                ->where('menu_id',$id)
                                ->where('menu_status',1)
                                ->first();
            // $menu_date = format_date($menu_item->menu_date);
            return view('tools.partials.menu_edit',compact('list_menu','menu_item','id'));
        } else {
            return view('tools.partials.menu_edit',compact('list_menu','id'));
        }    
    }
    
     public function save(Request $request) {

        $menu_descript = $request->menu_descript == "<p><br></p>" ? "" : $request->menu_descript;
        // dd($request->all());
        $menu_id = $request->menu_id;
        // dd($menu_id);

        $menu_name = $request->menu_name;

        $images="";
        //dd($menu_name);
        if($menu_id >0){ // CHECK EXIST WHEN EDIT
             $check_exist = PosMenu::where('menu_place_id',Session::get('place_id'))
                                    ->where('menu_id','!=',$menu_id)
                                    ->where('menu_name',$menu_name)
                                    ->where('menu_status',1)
                                    ->count();
        } else {
                $check_exist = PosMenu::where('menu_place_id',Session::get('place_id'))
                                            ->where('menu_name',$menu_name)
                                            ->where('menu_status',1)
                                            ->count();
            }
            $rules = [
                'menu_name'                 => 'required',
                'menu_index'                => 'required',
                'menu_image'                => 'mimes:jpeg,jpg,png,gif|max:1024',

            ];
            $messages = [
                'menu_name.required'        => 'Please enter title',
                'menu_index.required'        => 'Please enter index',
                'menu_image.mimes' => 'Uploaded image is not in image format',
                'menu_image.max' => 'max size image 1Mb',
            ];
            $validator = Validator::make($request->all(),$rules,$messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{

                    $image_list = "";
                    if($request->multi_image && !$request->multi_image_add)
                    {
                        $image_list = $request->multi_image;
                    }
                    if($request->multi_image_add && !$request->multi_image)
                    {
                        $image_list = implode(";",$request->multi_image_add);
                    }
                    if($request->multi_image_add  && $request->multi_image)
                    {
                        $image_list = $request->multi_image.";".implode(";",$request->multi_image_add);
                    }


                    if ($request->hasFile('menu_image')) {
                        
                        $images =  ImagesHelper::uploadImageWebbuilder($request->file('menu_image'),"menu",Session::get('place_ip_license'));
                    }else{
                        $images = $request->menu_image_old;
                    }

                $list_menu = PosMenu::where('menu_place_id',Session::get('place_id'))->get();
                if($menu_id>0){// UPDATE MENU
                    $imgs = PosMenu::where('menu_place_id',Session::get('place_id'))
                                ->where('menu_id',$menu_id)->first()->menu_list_image;
                   
                   $PosMenu = PosMenu::where('menu_place_id',Session::get('place_id'))
                                ->where('menu_id',$menu_id)
                                ->update([
                                    'menu_name'         => $request->menu_name,
                                    'menu_parent_id'    => $request->menu_parent_id,
                                    'menu_url'          => $request->menu_url,
                                    'menu_index'        => $request->menu_index,
                                    'menu_image'        => $images,
                                    'menu_list_image'   => $image_list,
                                    'menu_descript'     => $menu_descript,
                                    'menu_type'         => $request->menu_type,
                                ]);
                        $request->session()->flash('message','Edit Menu Success');

                    return redirect()->route("place.webbuilder",Session::get('place_id'));
                }else{ // ADD NEW
                    $idPosMenu = PosMenu::where('menu_place_id',"=",Session::get('place_id'))->max('menu_id') +1;

                    $PosMenu = new PosMenu;
                                $PosMenu->menu_id           = $idPosMenu;
                                $PosMenu->menu_place_id     = Session::get('place_id');
                                $PosMenu->menu_name         = $request->menu_name;
                                $PosMenu->menu_parent_id    = $request->menu_parent_id;
                                $PosMenu->menu_url          = $request->menu_url;
                                $PosMenu->menu_index        = $request->menu_index;
                                $PosMenu->menu_image        = $images;
                                $PosMenu->menu_list_image   = $image_list;
                                $PosMenu->menu_descript     = $menu_descript;
                                $PosMenu->menu_status       = 1;
                                $PosMenu->menu_type         = $request->menu_type;
                                $PosMenu->save();
                        if ($PosMenu) {
                            $request->session()->flash('message','Insert Menu Success');
                        }else{
                            $request->session()->flash('error','Insert Menu Error');
                        }
                        return redirect()->route("place.webbuilder",Session::get('place_id'));
                }
            }
    }
    public function delete(Request $request){

       $menu = PosMenu::where('menu_place_id',Session::get('place_id'))
                    ->where('menu_id',$request->param_id)
                    ->update(['menu_status'=> 0]);

        if(!isset($menu))
            return response(['status'=>'error','message'=>'Failed! Delete Failed!']);
        return response(['status'=>'success','message'=>'Successfully! Delete Successfully!']);
    }
     

    public function changeStatus(Request $request)
    {   
        $checked = $request->checked;      
        $menu_type=0;
        $menu_id = $request->id;
        
        if($checked == "checked"){
            $menu_type = 1;
        }
        PosMenu::where('menu_place_id',Session::get('place_id'))
                    ->where('menu_id',$menu_id)
                    ->update(['enable_status'=>$menu_type]);

        return "Update Status Success!";
    }

     public function uploadMultiImages(Request $request)
    {
        if ($request->hasFile('file')) {

                $imageFiles = $request->file('file');

                $image_name = [];

                foreach ($request->file('file') as $fileKey => $fileObject ) {

                    if ($fileObject->isValid()) {

                        $image_name[] = ImagesHelper::uploadImageDropZone($fileObject,'menu',Session::get('place_ip_license'));
                    }
                }
                return $image_name;
            }
                return "upload error";
    }

    public function removeMenu(Request $request)
    {
        $menu_list_image = PosMenu::where('menu_place_id',Session::get('place_id'))
                 ->where('menu_id',$request->menu_id)
                 ->first()->menu_list_image;

        $menu_list_image = str_replace(";",",",$menu_list_image);

        $menu_list_image = explode(",",$menu_list_image);

        foreach (array_keys($menu_list_image, $request->src_image) as $key) {
                            unset($menu_list_image[$key]);
                        }
        $menu_list_image = implode(";", $menu_list_image);

        PosMenu::where('menu_place_id',Session::get('place_id'))
                 ->where('menu_id',$request->menu_id)
                 ->update(['menu_list_image'=>$menu_list_image]);

        return $menu_list_image;
    }

    public function import(){
        return view('tools.partials.import_menus');
    }

    public function export(){

        $date = format_date(now());
        return \Excel::create('menus_table_'.$date,function($excel) {

            $excel ->sheet('Menus Table', function ($sheet) 
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('Menu Name');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Menu Index');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Menu Url');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Menu Descript');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Menu Enable');   });
            });
        })->download("xlsx");
    }

    public function postImport(Request $request)
    {    
        try {
             if($request->hasFile('fileImport')){
                $path = $request->file('fileImport')->getRealPath();
                $data = \Excel::load($path)->toArray();
                $insert=0;
                $update=0;
                // dd($data);                
                foreach ($data as $value) {      

                        $check_menu_exist=PosMenu::where('menu_place_id',Session::get('place_id'))
                                                ->where('menu_name',$value['menu_name'])->count();

                        if($check_menu_exist==0)                 
                        {
                            $arr = [];
                            $menu_id = PosMenu::where('menu_place_id',Session::get('place_id'))->max('menu_id')+1;
                            $arr['menu_id'] = $menu_id;
                            $arr['menu_place_id'] = Session::get('place_id');
                            $arr['menu_name'] = $value['menu_name'];
                            $arr['menu_index'] = $value['menu_index'];
                            $arr['menu_url'] = $value['menu_url'];
                            $arr['menu_descript'] = $value['menu_descript']?$value['menu_descript']:"";
                            $arr['menu_type'] = $value['menu_enable']?$value['menu_enable']:1;

                            PosMenu::create($arr);
                            $insert++;
                        }
                        else
                        {
                            $arr = [];
                            $idmenu=PosMenu::where('menu_place_id',Session::get('place_id'))
                                                ->where('menu_name',$value['menu_name'])->first();
                            $menu_id=$idmenu->menu_id;
                            $arr['menu_id'] = $menu_id;
                            $arr['menu_place_id'] = Session::get('place_id');
                            $arr['menu_name'] = $value['menu_name'];
                            $arr['menu_index'] = $value['menu_index'];
                            $arr['menu_url'] = $value['menu_url'];
                            $arr['menu_descript'] = $value['menu_descript']?$value['menu_descript']:"";
                            $arr['menu_type'] = $value['menu_enable']?$value['menu_enable']:1;
                            $arr['menu_status']    =1;

                            $p=PosMenu::where('menu_place_id',Session::get('place_id'))
                                                ->where('menu_id',$menu_id)
                                                ->update($arr);
                            $update++;
                        }
                    
                }
            }
            return redirect()->route('menus')->with('message',"Import Menus Success!, update: ".$update." row, inserted: ".$insert."row");     

        } catch (\Exception $e) {
            return back()->with('error','Import Menus Error!');           
        }           
        
    }
}

