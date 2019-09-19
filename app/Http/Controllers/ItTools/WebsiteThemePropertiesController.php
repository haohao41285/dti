<?php

namespace App\Http\Controllers\ItTools;

use Illuminate\Routing\Controller;
use App\Models\MainTheme;
use DataTables;

Class WebsiteThemePropertiesController extends Controller
{
    public function index(){
        return view('tools.website-themes-properties');
    }

    public function datatable(){
        // $data = MainTheme::where('theme_status',1)->get();

        // return DataTables::of($data)
        // ->editColumn('created_at',function($data){
        //     return format_datetime($data->created_at);
        // })
        // ->addColumn('action', function ($data){
        //             return '<a class="btn btn-sm btn-secondary view"  href="#"><i class="fas fa-eye"></i></a> <a class="btn btn-sm btn-secondary edit-customer"  href="#"><i class="fas fa-edit"></i></a>
        //                 <a class="btn btn-sm btn-secondary delete"  href="#"><i class="fas fa-trash"></i></a>';
        //     })
        // ->rawColumns(['action'])
        // ->make(true);
    }

}