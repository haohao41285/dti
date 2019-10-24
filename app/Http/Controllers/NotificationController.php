<?php

namespace App\Http\Controllers;

use App\Helpers\ImagesHelper;
use App\Jobs\SendNotification;
use App\Models\MainNotification;
use App\Models\MainTeam;
use App\Models\MainUser;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use Validator;
use OneSignal;


class NotificationController extends Controller
{
    public function index(){
        $data['teams'] = MainTeam::active()->get();
        return view('notification.notification-list',$data);
    }
    public function notificationReceiveDatatable(Request $request){

        $notification_list_receive = MainNotification::where('receiver_id',Auth::user()->user_id);

        return DataTables::of($notification_list_receive)
            ->editColumn('content',function($row){
                return  cutString( $row->content,100);
            })
            ->editColumn('created_at',function($row){
                if($row->created_by ==0)
                    $created_by = "System";
                else
                    $created_by = $row->getCreatedBy['user_nickname'];

                return format_datetime($row->created_at) ." by ". $created_by;
            })
            ->addColumn('status',function ($row){
                if($row->read_not == 0)
                    return '<i title="Not read yet" class="fas fa-envelope"></i>';
                else
                    return '<i title="read" class="fas fa-envelope-open"></i>';
            })
            ->addColumn('check',function ($row){
                return '';
            })
            ->rawColumns(['status','content'])
            ->make(true);
    }
    public function notificationMarkRead(Request $request){

        $notification_arr = $request->notification_arr;

        $user_id = Auth::user()->user_id;

        $notification_update = MainNotification::where('receiver_id',$user_id)->whereIn('id',$notification_arr)->update(['read_not'=>1]);
        $notification_count = MainNotification::where('receiver_id',$user_id)->notRead()->count();

        if(!isset($notification_update))
            return response(['status'=>'error','message'=>'Failed!']);
        else
            return response(['status'=>'success','message'=>'Successfully!','notification_count'=>$notification_count]);
    }
    public function notificationSentDatatable(Request $request){

        $notification_list_sent = MainNotification::where('created_by',Auth::user()->user_id);

        return DataTables::of($notification_list_sent)
            ->editColumn('content',function($row){
                return  cutString($row->content,100);
            })
            ->editColumn('created_at',function($row){
                return format_datetime($row->created_at);
            })
            ->addColumn('sent_to',function ($row){
                return $row->getReceive['user_nickname']."(".ucwords($row->getReceive['user_firstname'])." ".ucwords($row->getReceive['user_lastname']).")";
            })
            ->rawColumns(['sent_to','content'])
            ->make(true);
    }
    public function sendNotification(Request $request){
        $rule = [
            'content' => 'required',
            'receiver_id' => 'required'
        ];
        $validator = Validator::make($request->all(),$rule);
        if($validator->fails()){
            return response([
                'status' => 'error',
                'message' => $validator->getMessageBag()->toArray(),
            ]);
        }
        $receiver_id = $request->receiver_id;
        $notification_arr = [];
        $input = [];
        $max_notification_id = MainNotification::max('id')+1;

        //IF CHOOSE SEND FOR ALL STAFF
        if(in_array('all',$receiver_id))
            $user_list = MainUser::active()->where('user_id','!=',Auth::user()->user_id)->get();
        //IF CHOOSE SEND WITH TEAM
        else
            $user_list = MainUser::active()->whereIn('user_team',$receiver_id)->where('user_id','!=',Auth::user()->user_id)->get();

        foreach ($user_list as $user){

            $notification_arr[] = [
                'content' => $request->content,
                'created_by' => Auth::user()->user_id,
                'read_not' => 0,
                'receiver_id' => $user->user_id,
                'href_to' => route('view-notification',$max_notification_id)
            ];
            $user_id = $user->user_id;
            //SEND NOTIFICATION WITH ONESIGNAL
            OneSignal::sendNotificationUsingTags('Notification from admin',
                array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" =>$user_id]),
                $url = route('view-notification',$max_notification_id)
            );
            $max_notification_id++;

            if($user->user_email != ""){
                $input['email'] = $user->user_email;
                $input['name'] = $user->getFullname();
                $input['email_arr'][] = $user->user_email;
            }
        }
        //SEND NOTIFICATION WITH EMAIL
        $input['subject'] = 'Notification from Admin';
        $input['message'] = $request->content;
        dispatch(new SendNotification($input))->delay(now()->addSecond(3));

        $notification_insert = MainNotification::insert($notification_arr);
        if(!isset($notification_insert))
            return response(['status'=>'error','message'=>'Failed!']);
        else
            return response(['status'=>'success','message'=>'Successfully!']);

        return $request->all();
    }
    public function viewNotification($id){

        //CHECK EXIST IN DATATABLE
        $data['notification_info'] = MainNotification::find($id);

        if(!$id || !isset($data['notification_info']))
            return back()->with(['error'=>'Get Notification Error!']);

        return view('notification.view-notification',$data);
    }
}
