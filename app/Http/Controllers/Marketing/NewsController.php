<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainNews;
use App\Models\MainNewsType;
use DataTables;
use Validator;
use Auth;
use App\Helpers\ImagesHelper;

class Newscontroller extends Controller
{
	public function index(){
		return view('marketing.news');
	}

	public function getNewsTypeDatatable(){
		return MainNewsType::getDatatable(); 
	}

	public function getNewsDatatable(Request $request){
		return MainNews::getDatatableByNewsTypeId($request->newsTypeId);
	}
	/**
	 * save news type
	 * @param  $request->title
	 * @param  $request->newsTypeId || null
	 * @return json
	 */
	public function saveNewsType(Request $request){
		$arr = [
			'title' => $request->title,
			'slug' => str_slug($request->title),
		];

		if($request->action == 'Create'){
			MainNewsType::create($arr);
		} else {
			$newsType = MainNewsType::getById($request->newsTypeId);
			$newsType->update($arr);
		}

		return response()->json(['status'=>1,'msg'=>'Saved successfully']);
	}

	public function deleteNewsType(Request $request){
		if($request->id){
			$newsType = MainNewsType::getById($request->id);
			$newsType->update(['news_type_status'=>0]);

			return response()->json(['status'=>1,'msg'=>'Deleted successfully']);
		}
	}

	public function saveNews(Request $request){
		$validate = Validator::make($request->all(),[
            'image' => 'image|max:4096',
        ]);
        // dd($request->image);

        if($validate->fails()){
            return response()->json(['status'=>0,"msg"=>$validate->messages()->getMessages()]);
        }

		if($request->hasFile('image')){
            $image = ImagesHelper::uploadImageToAPI($request->image,'news');
        }

		$arr = [
			'title' => $request->title,
			'slug' => str_slug($request->title),
			'image' => $image ?? '',
			'short_content' => $request->short_content,
			'content' => ImagesHelper::uploadImageSummerNote($request->content),
			'news_type_id' => $request->newsTypeId,
		];
		
		if(empty($request->content)){
			unset($arr['content']);
		}

		if(empty($image)){
			unset($arr['image']);
		}

		if($request->action == 'Create'){
			MainNews::create($arr);
		} else {
			$newsType = MainNews::getById($request->newsId);
			$newsType->update($arr);
		}

		return response()->json(['status'=>1,'msg'=>'Saved successfully']);
	}

	public function deleteNews(Request $request){
		if($request->id){
			$newsType = MainNews::getById($request->id);
			$newsType->update(['news_status'=> 0]);

			return response()->json(['status'=>1,'msg'=>'Deleted successfully']);
		}
	}

	public function getNewsbyId(Request $request){
		if($request->id){
			$newsType = MainNews::getById($request->id);

			return response()->json(['status'=>1,'data'=>$newsType]);
		}
	}
}