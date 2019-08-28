<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Helpers\Option;
use DataTables;
use App\Models\MainCustomer;

class CustomerController extends Controller 
{
    public function listCustomer()
    {
        $data['state'] = Option::state();
        $data['status'] = Option::status();
        return view('customer.all-customers',$data);
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

    public function listMyCustomer(){
        $data['state'] = Option::state();
        $data['status'] = Option::status();
        return view('customer.my-customers',$data);
    }

    public function customersDatatable(){
        $customers = MainCustomer::where('customer_status',1)->get();

        return Datatables::of($customers)        
        ->addColumn('customer_fullname',function($customers){
            return $customers->customer_firstname." ".$customers->customer_lastname;
        })
        ->addColumn('customer_status',function($customers){
            return "status";
        })
        ->addColumn('action', function ($customers){
            return '<a class="btn btn-sm btn-secondary view" data="'.$customers->customers_id.'" href="#"><i class="fas fa-eye"></i></a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
}