<?php

namespace App\Observers;

use App\Jobs\SendNotification;
use App\Models\MainComboServiceBought;
use App\Models\MainComboService;
use App\Models\MainGroupUser;
use App\Models\MainNotification;
use App\Models\MainPermissionDti;
use App\Models\MainUser;
use Laracasts\Presenter\PresentableTrait;
//use OneSignal;
use App\Jobs\SendNotificationOrderOnesignal;
use App\Models\MainTermService;

class OrderObserver
{
    use PresentableTrait;
    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';
    /**
     * Handle the main combo service bought "created" event.
     *
     * @param  \App\Models\MainComboServiceBought  $mainComboServiceBought
     * @return void
     */
    public function created(MainComboServiceBought $mainComboServiceBought)
    {
        //GET PERMISSION ID, SUPPORT TEAM
        $permission_id = MainPermissionDti::active()->where('permission_slug','payment-orders')->first()->id;
        $role_list = MainGroupUser::active()->where('gu_id','!=',1)->get();
        $support_team = [];
        foreach ($role_list as $role){
            $permission_list = $role->gu_role_new;
            if($permission_list != ""){
                $permission_arr = explode(';',$permission_list);
                if(in_array($permission_id,$permission_arr)){
                    $support_team[] = $role->gu_id;
                }
            }
        }

        $user_info = MainUser::where('user_id',$mainComboServiceBought->created_by)->first();

        if( isset($user_info->getTeam) 
            && $user_info->getTeam->team_cskh_id != "" 
            && $user_info->getTeam->team_cskh_id != null)
        {
            $user_list = MainUser::where('user_team',$user_info->getTeam->team_cskh_id)->get();

            foreach ($user_list as $user){
                $content = $mainComboServiceBought->getCreatedBy->user_nickname." created a order #".$mainComboServiceBought->id;
                //ADD NOTIFICATION TO DATABASE
                $notification_arr = [
                    'id' => MainNotification::max('id')+1,
                    'content' => $content,
                    'href_to' => route('payment-order',$mainComboServiceBought->id),
                    'receiver_id' => $user->user_id,
                    'read_not' => 0,
                    'created_by' => $mainComboServiceBought->getCreatedBy->user_id,
                ];
                MainNotification::create($notification_arr);
                //SEND NOTIFICATION WITH ONESIGNAL
                $input_onesignal['order_id'] = $mainComboServiceBought->id;
                $input_onesignal['user_id'] = $user->user_id;

                dispatch(new SendNotificationOrderOnesignal($input_onesignal))->delay(now()->addSecond(5));
            }
        }
    }

    /**
     * Handle the main combo service bought "updated" event.
     *
     * @param  \App\MainComboServiceBought  $mainComboServiceBought
     * @return void
     */
    public function updated(MainComboServiceBought $mainComboServiceBought)
    {
        //SEND MAIL FOR CUSTOMER
        if($mainComboServiceBought->getCustomer->customer_email != "" && $mainComboServiceBought->updated_by != ""){
            if($mainComboServiceBought->csb_status == 1){
                $service_list = $mainComboServiceBought->csb_combo_service_id;
                $service_array = explode(";",$service_list);
                $mainComboServiceBought['combo_service_list'] = MainComboService::whereIn('id',$service_array)->get();

                //GET TERM SERVICE
                $input['file_term_service'] = [];
                $term_services = MainTermService::whereIn('service_id',$service_array)->active()->select('file_name')->distinct('file_name')->get();
                foreach ($term_services as $key => $term_service) {
                    if(file_exists($term_service->file_name))
                    $input['file_term_service'][] = $term_service->file_name;
                }
                $input['file_term_service'][] = $mainComboServiceBought->csb_invoice;
                $content = $mainComboServiceBought->present()->getThemeMail_2;

                $input['subject'] = 'INVOICE';
                $input['email'] = $mainComboServiceBought->getCustomer->customer_email;
                $input['name'] = $mainComboServiceBought->getCustomer->customer_firstname. " ".$mainComboServiceBought->getCustomer->customer_lastname;
                $input['message'] = $content;
                $input['mail_username_invoice'] = env('MAIL_USERNAME_INVOICE');
                $input['mail_password_invoice'] = env('MAIL_PASSWORD_INVOICE');

                dispatch(new SendNotification($input))->delay(now()->addSecond(5));
            }
            elseif($mainComboServiceBought->csb_status == 2)
            {
             //SEND SMS   
            }
        }
    }

    /**
     * Handle the main combo service bought "deleted" event.
     *
     * @param  \App\MainComboServiceBought  $mainComboServiceBought
     * @return void
     */
    public function deleted(MainComboServiceBought $mainComboServiceBought)
    {
        //
    }

    /**
     * Handle the main combo service bought "restored" event.
     *
     * @param  \App\MainComboServiceBought  $mainComboServiceBought
     * @return void
     */
    public function restored(MainComboServiceBought $mainComboServiceBought)
    {
        //
    }

    /**
     * Handle the main combo service bought "force deleted" event.
     *
     * @param  \App\MainComboServiceBought  $mainComboServiceBought
     * @return void
     */
    public function forceDeleted(MainComboServiceBought $mainComboServiceBought)
    {
        //
    }
}
