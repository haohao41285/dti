<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainSmsContentTemplate;
use App\Models\MainUser;
use DataTables;
use Auth;
use Validator;

class SetupSmsController extends Controller
{
    public function setupTemplateSms(Request $request)
    {
        return view('setting.template_sms');
    }
    public function smsTemplateDatatable(Request $request)
    {
    	$template_list = MainSmsContentTemplate::all();

    	return DataTables::of($template_list)
    		->addColumn('action',function($row){
    			return '<a class="btn btn-sm btn-secondary edit-template" template_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-sm btn-secondary delete-template" template_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
    		})
    		->rawColumns(['action'])
    		->make(true);
    }
    public function deleteTemplate(Request $request)
    {
    	$template_id = $request->template_id;

    	$template_delete = MainSmsContentTemplate::find($template_id)->delete();

    	if(!$template_delete)
    		return response(['status'=>'error','message'=>'Deletinf Error!']);
    	else
    		return response(['status'=>'success','message'=> 'Deleting Success']);
    }
    public function saveTemplateSms(Request $request)
    {
    	$rule = [
    		'template_title' => 'required',
    		'sms_content_template' => 'required|max:160'
    	];
    	$message = [
    		'template_title.required' => 'Enter Title Template',
    		'sms_content_template.required' => 'Enter Content Template',
    		'sms_content_template.max' => 'Content Template max 160 characters',
    	];

    	$validator = Validator::make($request->all(),$rule,$message);
    	if($validator->fails())
    		return response([
    			'status' =>'error',
    			'message' => $validator->getMessageBag()->toArray()
    		]);
    	
    	$template_id = $request->template_id;
    	$template_title = $request->template_title;
    	$sms_content_template = $request->sms_content_template;

    	if($template_id == 0)
    		$save_template = MainSmsContentTemplate::insert([
    			'template_title' => $template_title,
    			'sms_content_template' => $sms_content_template,
    			'created_by' => Auth::user()->user_id,
    		]);
    	else
    		$save_template = MainSmsContentTemplate::find($template_id)
		    	->update([
		    			'template_title' => $template_title,
		    			'sms_content_template' => $sms_content_template,
		    			'updated_by' => Auth::user()->user_id,
		    		]);
		if(!isset($save_template))
			return response(['status'=>'error','message'=>'Saving Error!']);
		else
			return response(['status'=>'success','message'=>'Saving Success']);
    }
    
}
