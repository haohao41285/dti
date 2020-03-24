@extends('layouts.app')
@section('content-title')
    NEW ORDER
@endsection
@push('styles')
<style>
    .form-group {
        margin-bottom: .5rem;
    }
    .card-header{
        padding: 0.5rem 0.75rem;
    }
    .select2-container .select2-selection--single{
        height:34px !important;
    }
    .select2-container--default .select2-selection--single{
             border: 1px solid #ccc !important; 
         border-radius: 0px !important; 
    }
    .select2-container {
        width: 100%!important;
    }
    .custom-checkbox:hover,.custom-control:hover{
        background-color: #858796;
        color: white;
    }
</style>
@endpush
@section('content')
    <div class="">
    <form action="{{route('orders.old_order.save')}}" method="post">
        @csrf()

    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Order By</b></label>
        <div class="col-md-4">
            <select name="created_by" id="created_by" class=" form-control form-control-sm select2">
                @foreach($user_list as $user)
                    <option {{ \Auth::user()->user_id==$user->user_id?"selected":"" }} value="{{ $user->user_id }}">{{ $user->user_lastname." ".$user->user_firstname." (".$user->user_nickname." )" }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Search With</b></label>
        <div class="col-md-4">
            <select name="" id="search-with" class=" form-control form-control-sm">
                <option value="1">Business</option>
                <option value="2">Customer</option>
            </select>
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Business</b></label>
        <div class="col-md-4 business">
            <select name="place_id" id="place_id" class="place_id form-control form-control-sm select2">
                @foreach($place_list as $place)
                    <option  value="{{ $place->place_id }}">{{ $place->place_name." - ".$place->place_phone." - ".$place->place_address }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 customer-box customer" style="display: none">
            <select name="customer_id" id="customer_id" class="place_id form-control form-control-sm select2">
                @foreach($main_customer as $customer)
                    <option  value="{{ $customer->customer_id }}">{{ $customer->customer_firstname." ".$customer->customer_lastname." - ".$customer->customer_phone." - ".$customer->customer_address }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm new-business-customer" type="button">New Customer && Business</button>
        </div>
    </div>
    <div class="col-md-12  form-group row">
        <div class="col-md-2">
        </div>
        <div class="search-tip col-md-4">
            
        </div>
    </div>
    <div class="col-md-12 row new_business_customer_box">
        
    </div>
    <div class="col-md-12 form-group row">
        <div class="col-md-2 customer-tip"></div>
        <div class="col-md-4 business-customer-box"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Date Order</b></label>
        <div class="col-md-4">
            <input type="text" id="date_order" value="{{ today()->format('m/d/Y') }}" name="date_order" class="form-control form-control-sm">
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Amout</b></label>
        <div class="col-md-4">
            <input type="text" id="" onkeypress="return isNumberKey(event)" value="0" name="csb_amount" class="form-control form-control-sm">
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Discount</b></label>
        <div class="col-md-4">
            <input type="text" id="" value="0" onkeypress="return isNumberKey(event)" name="csb_amount_deal" class="form-control form-control-sm">
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2"><b>Charge</b></label>
        <div class="col-md-4">
            <input type="text" id="" value="0" onkeypress="return isNumberKey(event)" name="csb_charge" class="form-control form-control-sm">
        </div>
    </div>
    <hr>
    <div class="col-md-12 row">
        <div class="col-md-2"><b>Service List Choose</b></div>
        <div class="col-md-10 cs_name_list"></div>
    </div>
	<div class="col-12 row mb-5">
        <div class="col-md-2">
            <label class="required"><b>Services</b></label>
        </div>
        <div class="col-md-10">
            <div id="accordion">
                @foreach($service_list as $key => $services)
                    <div class="card">
                        <div class="card-header">
                            <a class="card-link" data-toggle="collapse" href="#{{ $key }}">
                                <div class="text-uppercase text-info">{{ $key }}</div>
                            </a>
                        </div>
                            <div id="{{ $key }}" class="collapse " data-parent="#accordion">
                                <div class="card-body row">
                                    @foreach($services as $service)
                                        {{-- <label class="col-md-6"><input style="width:20px;height: 20px" type="checkbox" max_discount="" class="combo_service" cs_price="" name="cs_id[]"  value="">{{ $service['cs_name'] }}</label> --}}
                                        <div class="custom-control custom-checkbox col-md-6 service">
                                            <input type="checkbox" class="custom-control-input service_id" id="{{ $service['id'] }}" name="cs_id[]" cs_name="{{ $service['cs_name'] }}" value="{{ $service['id'] }}">
                                            <label class="custom-control-label" for="{{ $service['id'] }}">{{ $service['cs_name'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="form-group col-md-12 mb-1 text-center" style="position: fixed;bottom: 0">
        <button type="button" class="btn btn-primary btn-sm submit-btn">Submit</button>
    </div>
    </form>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {

    var cs_id = [];
    var cs_name = [];
    getCustomer();

    $("#date_order").datepicker({
        todayHighlight: true,
        setDate: new Date(),
    });
    $('.select2').select2();

    $(".service_id").click(function(){
        var service_id = $(this).val();
        var service_name = $(this).attr('cs_name');
        if($(this).is(":checked")){
            cs_id.push(service_id);
            cs_name.push(service_name);
        }else{
            cs_id.splice( $.inArray(service_id, cs_id), 1 );
            cs_name.splice($.inArray(service_name, cs_name), 1 );
        }
        getServiceList();
    });
    function getServiceList(){
        var cs_name_list = cs_name.join('//');
        $(".cs_name_list").text(cs_name_list);
    }

    $("#place_id").change(function(){
        getCustomer();
    })
    $("#customer_id").change(function(){
        getBusiness();
    });
    $("#search-with").change(function(){
        $(".new_business_customer_box").html("");
        var search = $(this).val();
        if(search == 1){
            $(".business").css('display','');
            $(".customer").css('display','none');
            getCustomer();
        }else{
            $(".business").css('display','none');
            $(".customer").css('display','');
            getBusiness();
        }
    });

    $(".new-business-customer").click(function(){
        $(".search-tip").html("");
        $(".customer-tip").html("");
        $(".business-customer-box").html("");
        var business_customer = '';
        business_customer = `
        <div class="col-md-2">
            <b>Customer Information</b>
        </div>
        <div class="col-md-4">
            <input type="hidden" name="customer_id" value="0">
            <input type="text" name="customer_firstname" class="form-control form-control-sm mt-1" value="" placeholder="Customer Firstname">
            <input type="text" name="customer_lastname" class="form-control form-control-sm mt-1" value="" placeholder="Customer Lastname">
            <input type="text" name="customer_email" class="form-control form-control-sm mt-1" value="" placeholder="Customer Email">
            <input type="text" name="customer_phone" onkeypress="return isNumberKey(event)" required class="form-control form-control-sm mt-1" value="" placeholder="Customer Phone">
            <input type="text" name="customer_address" class="form-control form-control-sm mt-1" value="" placeholder="Customer Address">
        </div>
        <div class="col-md-2">
            <b>Business Information</b>
        </div>
        <div class="col-md-4">
            <input type="hidden" name="place_id" value="0">
            <input type="text" name="place_name" class="form-control form-control-sm mt-1" value="" placeholder="Business">
            <input type="text" name="place_phone" onkeypress="return isNumberKey(event)" required class="form-control form-control-sm mt-1" value="" placeholder="Business Phone">
            <input type="text" name="place_email" class="form-control form-control-sm mt-1" value="" placeholder="Email">
            <input type="text" name="place_address" class="form-control form-control-sm mt-1" value="" placeholder="Address">
            <input type='button' class="btn btn-danger btn-sm mt-1 float-right cancle-btn" value='Cancle'> 
        </div>
        `;
        $(".new_business_customer_box").html(business_customer);
    });
    $(document).on("click",".cancle-btn",function(){
        $(".new_business_customer_box").html("");
    });
    function getCustomer(){
        $(".new_business_customer_box").html("");
        let place_id = $('#place_id').val();
        $.ajax({
            url: '{{ route('orders.old_order.search_business') }}',
            type: 'GET',
            dataType: 'html',
            data: {place_id: place_id},
        })
        .done(function(data) {
            data = JSON.parse(data);

            var customer_tip = "";
            var customer_firstname = '';
            var customer_lastname = '';
            var customer_phone = '';
            var customer_address = '';
            var customer_email = '';
            var disabled = "";
            var customer_id = 0;


            if(data.message === 'empty'){
                customer_tip = 'Customer\'s Not Existed. You Can Enter Information to CREATE NEW CUSTOMER in below box:';
                
            }else{
                customer_firstname = data.customer_info.customer_firstname;
                customer_lastname = data.customer_info.customer_lastname;
                customer_phone = data.customer_info.customer_phone;
                customer_address = data.customer_info.customer_address;
                customer_email = data.customer_info.customer_email;
                customer_id = data.customer_info.customer_id;
                disabled = 'disabled';

            }
            var html = `
                    <input type="hidden" name="customer_id" value="`+customer_id+`">
                    <input type="text" name="customer_firstname" `+disabled+` class="form-control form-control-sm mt-1" value="`+customer_firstname+`" placeholder="Customer Firstname">
                    <input type="text" name="customer_lastname" `+disabled+` class="form-control form-control-sm mt-1" value="`+customer_lastname+`" placeholder="Customer Lastname">
                    <input type="text" name="customer_email" `+disabled+` class="form-control form-control-sm mt-1" value="`+customer_email+`" placeholder="Customer Email">
                    <input type="text" name="customer_phone" onkeypress="return isNumberKey(event)" required `+disabled+` class="form-control form-control-sm mt-1" value="`+customer_phone+`" placeholder="Customer Phone">
                    <input type="text" name="customer_address" `+disabled+` class="form-control form-control-sm mt-1" value="`+customer_address+`" placeholder="Customer Address">
            `;
            $(".search-tip").html(customer_tip);
            $(".customer-tip").html("<b>Customer Information</b>");
            $(".business-customer-box").html(html);
            console.log(data);
        })
        .fail(function() {
            console.log("error");
        });
    }
    function getBusiness(){
        $(".new_business_customer_box").html("");
        let customer_id = $('#customer_id').val();
        $.ajax({
            url: '{{ route('orders.old_order.search_customer') }}',
            type: 'GET',
            dataType: 'html',
            data: {customer_id: customer_id},
        })
        .done(function(data) {
            data = JSON.parse(data);

            var business_html = '';
            var customer_tip = '';
            console.log(data);


            if(data.message === 'empty'){
                customer_tip = 'Business\'s Not Existed. You Can Enter Information to CREATE NEW BUSINESS in below box:';
                business_html = `
                    <input type="hidden" name="place_id" value="0">
                    <input type="text" name="place_name" class="form-control form-control-sm mt-1" value="" placeholder="Business">
                    <input type="text" name="place_phone" onkeypress="return isNumberKey(event)" required class="form-control form-control-sm mt-1" value="" placeholder="Business Phone">
                    <input type="text" name="place_email" class="form-control form-control-sm mt-1" value="" placeholder="Email">
                    <input type="text" name="place_address" class="form-control form-control-sm mt-1" value="" placeholder="Address">
                    `;
            }
            else{
                $.each(data.business_info, function(index, val) {
                    // console.log(val);
                    business_html += `<div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="business_`+val.place_id+`" name="place_id" value="`+val.place_id+`">
                        <label class="custom-control-label" for="business_`+val.place_id+`">`+val.place_name+`-`+val.place_address+`-`+val.place_phone+`</label>
                    </div>`;
                });
            }
            $(".search-tip").html(customer_tip);
            $(".customer-tip").html("<b>Business Information</b>");
            $(".business-customer-box").html(business_html);
            // console.log(business_html);
        })
        .fail(function() {
            console.log("error");
        });
    }
    $(".submit-btn").click(function(){

        if(cs_id.length == 0){
            toastr.error('Choose Service');
            return;
        }else{
            $(this).parents('form')[0].submit();
        }
    })

});
</script>
@endpush
