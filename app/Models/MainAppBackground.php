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
        'updated_at'
    ];

    protected $guarded = [];

    public function datatable(){ 
    	$data = $this->getAll();

    	return DataTables::of($data)
        ->addColumn('action', function ($data){
                    return '<a class="btn btn-sm btn-secondary edit-data" data-id="'.$data->id.'" href="#"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-data" data-id="'.$data->id.'" href="#"><i class="fas fa-trash"></i></a>';
            })
        ->editColumn('image',function($data){
            return "<img style='height: 5rem;' src='".env('URL_FILE_VIEW').$data->image."'>";
        })
        ->rawColumns(['action','image'])
        ->make(true);
    }

    public function getAll(){
        return $this->all();
    }

    public function getById($id){
        return $this->find($id);
    }

    public function createByArr($arr){
        return $this->create($arr);
    }

    public function deleteById($id){
        return $this->getById($id)->delete();
    }

    public function updateById($id,$arr){
        return $this->find($id)->update($arr);
    }


        
}