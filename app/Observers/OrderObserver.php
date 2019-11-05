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
use OneSignal;

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
        if(count($support_team) != 0){
            $user_list = MainUser::active()->whereIn('user_group_id',$support_team)->get();
            foreach ($user_list as $user){
                $content = $mainComboServiceBought->getCreatedBy->user_nickname." created a order #".$mainComboServiceBought->id;
                //ADD NOTIFICATION TO DATABASE
                $notification_arr = [
                    'content' => $content,
                    'href_to' => route('payment-order',$mainComboServiceBought->id),
                    'receiver_id' => $user->user_id,
                    'read_not' => 0,
                    'created_by' => $mainComboServiceBought->getCreatedBy->user_id,
                ];
                MainNotification::create($notification_arr);
                //SEND NOTIFICATION WITH ONESIGNAL
                OneSignal::sendNotificationUsingTags('New Order',
                    array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" =>$user->user_id]),
                    $url = route('payment-order',$mainComboServiceBought->id)
                );
            }
        }
//        if($mainComboServiceBought->getCustomer->customer_email != ""){
//            $service_list = $mainComboServiceBought->csb_combo_service_id;
//            $service_arrray = explode(";",$service_list);
//            $mainComboServiceBought['combo_service_list'] = MainComboService::whereIn('id',$service_arrray)->get();
//
//            $content = $mainComboServiceBought->present()->getThemeMail;
//
//            $input['subject'] = 'INVOICE';
//            $input['email'] = $mainComboServiceBought->getCustomer->customer_email;
//            $input['name'] = $mainComboServiceBought->getCustomer->customer_firstname. " ".$mainComboServiceBought->getCustomer->customer_lastname;
//            $input['message'] = $content;
//
//            dispatch(new SendNotification($input))->delay(now()->addSecond(5));
//        }
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
            $service_list = $mainComboServiceBought->csb_combo_service_id;
            $service_array = explode(";",$service_list);
            $mainComboServiceBought['combo_service_list'] = MainComboService::whereIn('id',$service_array)->get();

            $content = $mainComboServiceBought->present()->getThemeMail;

            $input['subject'] = 'INVOICE';
            $input['email'] = $mainComboServiceBought->getCustomer->customer_email;
            $input['name'] = $mainComboServiceBought->getCustomer->customer_firstname. " ".$mainComboServiceBought->getCustomer->customer_lastname;
            $input['message'] = $content;

            dispatch(new SendNotification($input))->delay(now()->addSecond(5));
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
