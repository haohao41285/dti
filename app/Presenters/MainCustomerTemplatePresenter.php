<?php

namespace App\Presenters;
use Laracasts\Presenter\Presenter;

class MainCustomerTemplatePresenter extends Presenter{

    public function getFullname(){
        return $this->customer_firstname." ".$this->customer_lastname;
    }
}
