<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DataTables;

class MainAppBackground extends Model
{
    protected $table = 'main_app_background';

    protected $fillable = [
        'id',
        'image',
        'created_at',
        'updated_at',
        'address_status'
    ];

    protected $guarded = [];

    public function datatable(){ 
    	$data = $this->getAll();

    	return DataTables::of($data)
        ->addColumn('action', function ($data){
                    return '<a class="btn btn-sm btn-secondary edit-data" data-id="'.$data->app_id.'" href="#"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-data" data-id="'.$data->app_id.'" href="#"><i class="fas fa-trash"></i></a>';
            })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function getAll(){
        return $this->all();
    }

        
}