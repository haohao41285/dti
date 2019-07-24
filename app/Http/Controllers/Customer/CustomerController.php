<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class CustomerController extends Controller {
   
    public function __construct()
    {
        
    }
    
    public function listCustomer()
    {
        return view('customer.all-customers');
    }
    
    public function listMerchant()
    {
        return view('customer.all-merchants');
    }
    
    public function addCustomer()
    {
        return view('customer.customer-add');
    }
    
    public function editCustomer()
    {
        return view('customer.customer-edit');
    }
}