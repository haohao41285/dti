<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainSmsContentTemplate;
use App\Models\MainSmsSend;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DataTables;
use Validator;
use Auth;


class SmsController extends Controller
{
    public function view()
    {
    	$sms_content_template_list =  MainSmsContentTemplate::select('id','template_title')->get(); 
    	return view('marketing.send-sms',compact('sms_content_template_list'));
    }

    public function getContentTemplate(Request $request){

    	$content_list = MainSmsContentTemplate::where('id',$request->id)
    	                                   ->select('sms_content_template')
    	                                   ->first()
    	                                   ->sms_content_template;
    	return $content_list;
    }
    public function downloadTemplateFile(Request $request){
    	if(file_exists('file/add_receivers_template.xlsx')){
            return response()->download('file/add_receivers_template.xlsx');            
            }
            else 
            return "Error download template";
    }

     public function postSendSMS(Request $request){
    	$rules = [
            'sms_send_event_title' => 'required',
            'sms_send_event_template_id' => 'required',
            'sms_send_event_start_day' => 'required',
            'sms_send_event_start_time' => 'required',
            'upload_list_receiver' => 'required',
        ];
        $messages = [
            'sms_send_event_title.required' => "Please enter Title",
            'sms_send_event_template_id.required' => 'Please select Template',
            'sms_send_event_start_day.required' => 'Please select Start Date',
            'sms_send_event_start_time.required' => 'Please enter Time Send'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();

        }else{
            if($request->sms_send_event_template_id == ""){

                return back()->with("error","Template SMS Empty!");
            }
        	$sms_total = 0;


	        if($request->hasFile('upload_list_receiver')){

	        $path = $request->file('upload_list_receiver')->getRealPath();

	        $data = \Excel::load($path)->toArray();

	                if(!empty($data)){

	                    $arr = [];
	                    $receiver_total = [];

	                    foreach($data as $key => $value){
	                            
	                                $receiver_total[] = [
	                                    'name' =>$value['name'],
	                                    'phone'=>$value['phone'],
	                                    'birthday'=>$value['birthday'],
	                                ];
	                                $arr[] = $value['phone'];
	                            }

	                            $upload_list_receiver = implode(";", $arr);

	                            $sms_total = $key+1 ;
	                }else{
	                    $upload_list_receiver = "";
	                    $request->session()->flash("error","Upload List Receiver Empty!");
	                    return back();
	                }
	        }
	        else
	        {
	            $upload_list_receiver = "";
	            $sms_total = 0;
	        }
        	$arr = [
        		'sms_send_event_title' => $request->sms_send_event_title,
				'sms_send_event_template_id' => $request->sms_send_event_template_id,
				'sms_send_event_start_day' => Carbon::parse($request->sms_send_event_start_day)->format('Y-m-d'),
				'sms_send_event_start_time' => $request->sms_send_event_start_time,
				'sms_send_event_status' => 1,
				'sms_total'=>$sms_total,
				'upload_list_receiver' => $upload_list_receiver,
				'created_by' => Auth::user()->user_id,
				'updated_by' => Auth::user()->user_id,
				'sms_send_event_enable' => 1
        	];
        	//SmsSend::create($arr);
        	$date = now()->format('Y_m_d_His');

            $file_name = "receiver_sms_list_".$date;

            \Excel::create($file_name,function($excel) use ($receiver_total,$request){

                $excel ->sheet($request->sms_send_event_title, function ($sheet) use ($receiver_total)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('phone');   });
                    $sheet->cell('B1', function($cell) {$cell->setValue('{p3}');   });
                    $sheet->cell('C1', function($cell) {$cell->setValue('{p2}');   });

                    if (!empty($receiver_total)) {
                        foreach ($receiver_total as $key => $value) {
                            $i= $key+2;
                            if($value['phone'] != ""){
                                $sheet->cell('A'.$i, $value['phone']);
                                $sheet->cell('B'.$i, $value['birthday']);
                                $sheet->cell('C'.$i, $value['name']); 
                            }
                        }
                    }
                });
            })->store('xlsx', false, true);

            $file_url = storage_path('exports/'.$file_name.".xlsx");

            $input = $request->all();

            $sms_content_template = MainSmsContentTemplate::where('id',$request->sms_send_event_template_id)
                                                      ->first()
                                                      ->sms_content_template;

            $input['sms_content_template'] = $sms_content_template;

            $input['id'] = MainSmsSend::max('id')+1;

        	$result = $this->PushApiSMS($input,$file_url);

        	$result = json_decode($result,true);

        	if($result['status'] == 1){

                MainSmsSend::create($arr);

        		return back()->with('success',$result['messages']);
            }
        	else
        		return back()->with('error',$result['messages']);

        }
    }
    private function PushApiSMS($input,$file_url,$url = ""){
        
        $url_event = 'pushsms';

        $url = env('SMS_API_URL').$url_event.$url;

        $header = array('Authorization'=>'Bearer ' .env("SMS_API_KEY"));
        //$url="http://user.tag.com/api/v1/receiveTo";
        $client = new Client([
            // 'timeout'  => 5.0,            
        ]);

        $sms_content_template = str_replace("{name}","{p2}",$input['sms_content_template']);
        // $sms_content_template = str_replace("{phone}","{p2}",$sms_content_template);
        $sms_content_template = str_replace("{birthday}","{p3}",$sms_content_template);

        $date_time_send = Carbon::parse($input['sms_send_event_start_day'])->format('d-m-Y')." 00:00:00";
        $date_time_end =  Carbon::parse($input['sms_send_event_start_day'])->format('d-m-Y')." 23:59:59";

        $response = $client->request('POST', $url ,[
                    'multipart' => [
                            [
                                'name' => 'content',
                                'contents' => $sms_content_template,
                            ],
                            [
                                'name' => 'title',
                                'contents' => $input['sms_send_event_title'],
                            ],
                            [
                                'name' => 'merchant_id',
                                'contents' => Auth::user()->user_id,
                            ],
                            [
                                'name' => 'start',
                                'contents' => $date_time_send,
                            ],
                            [
                                'name' => 'date_before',
                                'contents' => '0',
                            ],
                            [
                                'name' => 'repeat',
                                'contents' => '0',
                            ],
                            [
                                'name' => 'repeat_on',
                                'contents' => '0',
                            ],
                            [
                                'name' => 'timesend',
                                'contents' => $input['sms_send_event_start_time'],
                            ],
                            [
                                'name' => 'type_event',
                                'contents' => 1,
                            ],
                            [
                                'name' => 'event_id',
                                'contents' => $input['id'],
                            ],
                            [
                                'name' => 'end',
                                'contents' => $date_time_end,
                            ],
                            [
                                'name'     => 'upfile',
                                'contents' => fopen($file_url,'r'),
                            ],
                            [
                                'name' => 'status',
                                'contents' => 1,
                            ]
                        
                    ],
                    'headers' => [
                        'Authorization' => 'Bearer ' .env("SMS_API_KEY"),
                                ],
                ]);
                
        //$response = $client->put($url, array('headers' => $header));
        // Call external API
        // $response = $client->post("http://d29u17ylf1ylz9.cloudfront.net/phuler-v4/index.html", ['form_params' => $smsData]);
        //$response = $client->get("http://d29u17ylf1ylz9.cloudfront.net/phuler-v4/index.html");
        // Check whether API call was successfull or not...
        //$zonerStatusCode = $response->getStatusCode();
        $resp =  (string)$response->getBody();
        //echo $resp;
        return $resp;
    }
    public function trackingHistory()
    {
        return view('marketing.tracking-history');
    }
    public function trackingHistoryDatatable(Request $request)
    {
        $history_list = MainSmsSend::join('main_sms_content_template',function($join){
            $join->on('main_sms_send.sms_send_event_template_id','main_sms_content_template.id');
            })
            ->join('main_user',function($join){
                $join->on('main_sms_send.created_by','main_user.user_id');
            })
            ->select('main_sms_send.*','main_user.user_nickname','main_sms_content_template.sms_content_template');

        return DataTables::of($history_list)
            ->editColumn('created_by',function($row){
                return Carbon::parse($row->created_at)->format('m/d/Y H:i:s')." by ".$row->user_nickname;
            })
            ->addColumn('action',function($row){
                return '<a class="btn btn-sm btn-secondary view-sms" event_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['action','sms_content_template','sms_send_event_title'])
            ->make(true);
    }
     public function eventDetail(Request $request){

        $event_id = $request->event_id;
        $data_sum = [];

        if($event_id != 0){

            $url_api = "history?merchant_id=1&storage_event_id=".$event_id;

            $url = env("SMS_API_URL").$url_api;

            $header = array('Authorization'=>'Bearer ' .env("SMS_API_KEY"));
            $client = new Client([
                // 'timeout'  => 5.0,            
            ]);
            $response = $client->get($url, array('headers' => $header));

            $resp=  (string)$response->getBody();
            // return $resp;

            $data_arr = json_decode($resp);

                foreach($data_arr->data as $data){
                    $data_sum[] = [
                        'phone' => $data->phone,
                        'content' => $data->content,
                        'date_time' => $data->updated_at,
                    ];
                }
        }else{
            $data_sum[] = [
                        'phone' => "",
                        'content' => "",
                        'date_time' => "",
                    ];
        }
        
        return Datatables::of($data_sum)
                           ->make(true);
    }
     public function calculateSms(Request $request){

        $event_id = $request->event_id;

        $url_api = "history?merchant_id=1&storage_event_id=".$event_id;

        $url = env("SMS_API_URL").$url_api;
        // return $url;

        $header = array('Authorization'=>'Bearer ' .env("SMS_API_KEY"));
        $client = new Client([
            // 'timeout'  => 5.0,            
        ]);
        $response = $client->get($url, array('headers' => $header));

        $resp=  (string)$response->getBody();

        $data_arr = json_decode($resp,TRUE);

        $calculate = [];
        $success = 0;
        $fail = 0;
        //TOTAL SMS 
        $sms_total = MainSmsSend::where('id',$event_id)->first()->sms_total;
        $data_sum = [];

        if(count($data_arr['data']) > 0)
            foreach($data_arr->data as $data){

                if($data->status ==1) $success++;
                if($data->status ==0) $fail++;

                $calculate = [

                    'success' => $success,
                    'fail' => $fail,
                    'total' => $sms_total,
                    'balance' => $sms_total - $success
                ];
            }
        else
            $calculate = [

                    'success' => "",
                    'fail' => "",
                    'total' => $sms_total,
                    'balance' => ""
                ];
        if(!isset($calculate))
            return response(['status'=>'error','message'=>'Error!']);
        else
            return  response(['status'=>'success','calculate'=>$calculate]);
    }
}
