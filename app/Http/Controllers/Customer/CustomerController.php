<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
use Auth;
use DataTables;
use DB;

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
        $customers = MainCustomerTemplate::where('ct_status','!=',2)->get();

        return Datatables::of($customers)        
            ->addColumn('action', function ($row){
                return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getCustomerDetail(Request $request){

        $customer_id = $request->customer_id;

        $customer_list = MainCustomerTemplate::where('id',$customer_id)->first();

        if(!isset($customer_list))
            return 0;
        else
            return $customer_list;
    }
}