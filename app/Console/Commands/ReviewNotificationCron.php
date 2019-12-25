<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainUserReview;
use App\Models\MainTask;
use App\Models\MainUser;
use App\Models\MainComboService;
use App\Jobs\SendNotification;
use Carbon\Carbon;

class ReviewNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reviewNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = date('d');

        if($date  == 10 || $date == 20){
            $current_month = date('m');
            $current_month = date('m');
            $current_year = date('Y');
            $user_list  = MainUser::active()->get();

            $combo_service_list = MainComboService::select('id')->where('cs_form_type',1)->orWhere('cs_form_type',3)->get()->toArray();
            $combo_service_arr = array_values($combo_service_list);

            foreach($user_list as $user){

                $review_total = MainUserReview::where(function ($query) use ($user){
                    $query->where('user_id',$user->user_id)
                        ->orWhere('user_id','like','%;'.$user->user_id)
                        ->orWhere('user_id','like','%;'.$user->user_id.";%")
                        ->orWhere('user_id','like',$user->user_id.';%');
                })->latest();

                $task_list = MainTask::where(function($query) use ($user){
                    $query->where('assign_to',$user->user_id)
                    ->orWhere('assign_to','like','%;'.$user->user_id)
                    ->orWhere('assign_to','like','%;'.$user->user_id.';%')
                    ->orWhere('assign_to','like',$user->user_id.';%');
                })->whereIn('service_id',$combo_service_arr)->where('content','!=',null)

                ->where(function($query) use($current_year,$current_month){
                    $query->whereDate('date_start','<=',$current_year."-".$current_month."-31")
                    ->whereDate('date_end','>=',$current_year."-".$current_month."-1");
                });

                
                $review_total->whereMonth('updated_at',$current_month)->whereYear('updated_at',$current_year);
                            
                
                $review_total = $review_total->get();
                $task_list = $task_list->select('content','date_start','date_end')->get();

                $failed_total = $review_total->unique('review_id')->where('status',0)->count();
                $successfully_total  = $review_total->unique('review_id')->where('status',1)->count();

                $review_total = 0;
                $percent_complete = 0;
                $review_this_month = 0;

                foreach($task_list as $task){
                    $content = json_decode($task->content,TRUE);

                    if( ((isset($content['order_review']) && !empty($content['order_review']))
                        || (isset($content['number']) && !empty($content['number'])))
                        && ($task->date_start != "" && $task->date_end != "")
                    ){
                        $start_month = Carbon::parse($task->date_start)->format('m');
                        $end_month = Carbon::parse($task->date_end)->format('m');

                        $year_start = Carbon::parse($task->date_start)->format('Y');
                        $year_end = Carbon::parse($task->date_end)->format('Y');

                        $count_year = $year_end - $year_start;

                        if($count_year == 0)
                            $count_month = $end_month - $start_month +1;
                        else
                            $count_month = ($count_year-1)*12+(12-$start_month+1)+$end_month;

                        if(isset($content['order_review']))
                            $review_number = $content['order_review'];
                        elseif(isset($content['number']))
                            $review_number = $content['number'];

                        if($count_month == 0)
                            $review_total += intval($review_number);
                        else{
                            $review_avg_per_month = ceil(intval($review_number)/$count_month);

                            if($current_month == $end_month)
                                $review_this_month = $review_number - $review_avg_per_month*($count_month-1);
                            else
                                $review_this_month = $review_avg_per_month;

                            $review_total += $review_this_month;
                        }
                    }
                }
                if($successfully_total < $review_this_month && $user->user_email != ""){

                    $review_order = $review_this_month - $successfully_total;
                    $message = "You have <span style='color:red'>".$review_order."</span> Reviews <span style='color:red'>must to complete</span> for this month!";
                    $input['subject'] = 'REVIEW NOTIFICATION!';
                    $input['email'] = $user->user_email;
                    $input['name'] = $user->user_nickname;
                    $input['message'] = $message;

                    dispatch(new SendNotification($input))->delay(now()->addSecond(10));
                }
            }
        }
        $this->info('reviewNotification Cron Successfully');
    }
}
