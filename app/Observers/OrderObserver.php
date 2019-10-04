<?php

namespace App\Observers;

use App\Jobs\SendNotification;
use App\Models\MainComboServiceBought;
use App\Models\MainComboService;
use Laracasts\Presenter\PresentableTrait;

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
        if($mainComboServiceBought->getCustomer->customer_email != ""){
            $service_list = $mainComboServiceBought->csb_combo_service_id;
            $service_arrray = explode(";",$service_list);
            $mainComboServiceBought['combo_service_list'] = MainComboService::whereIn('id',$service_arrray)->get();

            $content = $mainComboServiceBought->present()->getThemeMail;

            $input['subject'] = 'INVOICE';
            $input['email'] = $mainComboServiceBought->getCustomer->customer_email;
            $input['name'] = $mainComboServiceBought->getCustomer->customer_firstname. " ".$mainComboServiceBought->getCustomer->customer_lastname;
            $input['message'] = $content;

            dispatch(new SendNotification($input))->delay(now()->addSecond(5));
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
        //
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
