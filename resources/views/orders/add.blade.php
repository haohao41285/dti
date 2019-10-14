@extends('layouts.app')
@section('title')
@endsection
@push('styles')
<style>
    .form-group {
    margin-bottom: .5rem;
    }
</style>
@endpush
@section('content')
    <h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info">NEW ORDER</h4>

    <div class="">
    <form action="{{route('authorize')}}" method="post">
        @csrf()
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label class="required">Customer phone:</label>
        </div>
        @if(empty($customer_info))
        <div class="col-md-4" >
            <input type="text" class="input-sm form-control form-control-sm" id="customer_phone"  name="customer_phone" />
            <input type="hidden" class="input-sm form-control form-control-sm" id="customer_id" value=""  name="customer_id" />
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-secondary btn-search" type="button">Search</button>
        </div>
        @else
        <div class="col-md-4" >
            <input type="text" disabled class="input-sm form-control form-control-sm" id="customer_phone" value="{{$customer_info->ct_business_phone}}" name="customer_phone" />
            <input type="hidden"  class="input-sm form-control form-control-sm" id="customer_phone" value="{{$customer_info->ct_business_phone}}" name="customer_phone" />
            <input type="hidden" class="input-sm form-control form-control-sm" id="customer_id" value="{{!empty($customer_info)?$customer_info->id:""}}"  name="customer_id" />
        </div>
        @endif
    </div>
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label class="required">Business:</label>
        </div>
        <div class="col-md-4" >
            <input type="text" class="input-sm form-control form-control-sm" disabled id="customer_bussiness" value="{{!empty($customer_info)?$customer_info->ct_salon_name:""}}"  name=""/>
        </div>
    </div>
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label class="required">FullName:</label>
        </div>
        <div class="col-md-4" >
            <input type="text" class="input-sm form-control form-control-sm" value="{{!empty($customer_info)?$customer_info->ct_fullname:""}}" disabled id="customer_fullname"  name=""/>
        </div>
    </div>
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label class="required">Places:</label>
        </div>
        <div class="col-md-10 row">
            <label class="ml-3 text-uppercase text-dark"><input style="width:20px;height: 20px" type="radio" class="place_id"  name="place_id" value="0">New Place</label>
        </div>
    </div>
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
        </div>
        <div class="col-md-10 row"  id="salon_list">
            @if(isset($place_list))
            @foreach($place_list as $place)
            <div class="col-md-3">
                <label class="ml-3 text-uppercase text-dark">
                    <input style="width:20px;height: 20px" type="radio" class="place_id"  name="place_id" value="{{$place->place_id}}"><b>{{$place->place_name}}</b>
                </label>
            </div>
            @endforeach
            @endif
        </div>
    </div>
    <hr>
	<div class="col-12 row">
        <div class="col-md-2">
            <label class="required">Services</label>
        </div>
        <div class="col-md-10">
            <div id="accordion">
                @foreach($combo_service_type as $key =>  $type)
                <div class="card">
                    <div class="card-header">
                        <a class="card-link" data-toggle="collapse" href="#t{{$type->id}}">
                            <div class="text-uppercase text-info">{{$type->name}}</div>
                        </a>
                    </div>
                    <div id="t{{$type->id}}" class="collapse " data-parent="#accordion">
                        <div class="card-body row">
                             @foreach($type->getComboService as $service)
                                <label class="col-md-6"><input style="width:20px;height: 20px" type="checkbox" class="combo_service" cs_price="{{$service->cs_price}}" name="cs_id[]"  value="{{$service->id}}"> {{$service->cs_name}}{{$service->cs_type==1?"(Combo)":"(Service)"}} - ${{$service->cs_price}}</label><br>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
                    <div class="card">
                        <div class="card-header">
                            <a class="card-link" data-toggle="collapse" href="#other">
                                <div class="text-uppercase text-info">Others</div>
                            </a>
                        </div>
                        <div id="other" class="collapse " data-parent="#accordion">
                            <div class="card-body row">
                                @foreach($combo_service_orther as $service)
                                    <label class="col-md-6"><input style="width:20px;height: 20px" type="checkbox" class="combo_service" cs_price="{{$service->cs_price}}" name="cs_id[]"  value="{{$service->id}}"> {{$service->cs_name}}{{$service->cs_type==1?"(Combo)":"(Service)"}} - ${{$service->cs_price}}</label><br>
                                @endforeach
                            </div>
                        </div>
                    </div>
            </div>
        </div>
{{--             <div class="col-md-5">--}}
{{--                @foreach($combo_service_list as $key => $cs)--}}
{{--                @if($key == $count)--}}
{{--                </div>--}}
{{--                <div class="col-md-5">--}}
{{--                @endif--}}
{{--                <label><input style="width:20px;height: 20px" type="checkbox" class="combo_service" cs_price="{{$cs->cs_price}}" name="cs_id[]"  value="{{$cs->id}}"> {{$cs->cs_name}}{{$cs->cs_type==1?"(Combo)":"(Service)"}} - ${{$cs->cs_price}}</label><br>--}}
{{--                @endforeach--}}
{{--        </div>--}}
    </div>
    <hr>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Service Price</label>
        <div class="col-md-4"><input disabled type="number" class="form-control form-control-sm" id="service_price" name="service_price" value="{{old('service_price')}}"><input type="hidden" class="form-control form-control-sm" id="service_price_hidden" name="service_price_hidden" value="{{old('service_price_hidden')}}"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Discount($)</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="number" id="discount" name="discount" value="{{old('discount')}}"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Payment Amount($)</label>
        <div class="col-md-4">
            <input class="form-control form-control-sm" type="hidden" id="payment_amount" name="payment_amount" value="{{old('payment_amount')}}">
            <input class="form-control form-control-sm" type="number" disabled id="payment_amount_disable" name="payment_amount_disable" value="{{old('payment_amount')}}">
            <input class="form-control form-control-sm" type="hidden" id="payment_amount_hidden" name="payment_amount_hidden" value="{{old('payment_amount_hidden')}}">
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Credit Card Type</label>
        <div class="col-md-4"><select class="form-control form-control-sm" name="credit_card_type" id="credit_card_type">
            <option value="MasterCard">MasterCard</option>
            <option value="VISA">VISA</option>
            <option value="Discover">Discover</option>
            <option value="American Express">American Express</option>
            <option value="E-CHECK">E-CHECK</option>
        </select></div>
    </div>
    <div class="col-md-12 form-group row check" style="display: none">
        <label class="col-md-2 required">Routing Number</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="routing_number"  value="{{old('routing_number')}}"></div>
    </div>
    <div class="col-md-12 form-group row check" style="display: none">
        <label class="col-md-2 required">Account Number</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="account_number"  value="{{old('account_number')}}"></div>
    </div>
    <div class="col-md-12 form-group row check" style="display: none">
        <label class="col-md-2 required">Bank Name</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="bank_name"  value="{{old('bank_name')}}"></div>
    </div>
    <div class="col-md-12 form-group row credit">
        <label class="col-md-2 required">Credit Card Number</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="credit_card_number"  value="{{old('credit_card_number')}}"></div>
    </div>
    <div class="col-md-12 form-group row credit">
        <label class="col-md-2 required">Experation Date</label>
        <div class="col-md-2"><select class="form-control form-control-sm"  name="experation_month">
            @for($i=1;$i<13;$i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select></div>
        <div class="col-md-2"><select class="form-control form-control-sm" name="experation_year">
            @for($i=2019;$i<2220;$i++)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select></div>
    </div>
    <div class="col-md-12 form-group row credit">
        <label class="col-md-2 required">CVV Number</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text"  value="{{old('cvv_number')}}" name="cvv_number"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Name On Card</label>
        <div class="col-md-2"><input class="form-control form-control-sm" type="text" value="{{old('first_name')}}" name="first_name" placeholder="First Name"></div>
        <div class="col-md-2"><input class="form-control form-control-sm" type="text" value="{{old('last_name')}}" name="last_name" placeholder="Last Name"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Address</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" value="{{old('address')}}"  name="address"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">City</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" value="{{old('city')}}"  name="city"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">State</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" value="{{old('state')}}"  name="state"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Zip Code</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" value="{{old('zip_code')}}"  name="zip_code"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Country</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" value="{{old('country')}}"  name="country"></div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Note</label>
        <div class="col-md-4"><textarea class="form-control form-control-sm" name="note" value="{{old('note')}}"  rows="5"></textarea></div>
    </div>
    <div class="form-group col-md-12">
        <div class="col-md-6 float-right">
        <button type="submit" class="btn btn-primary">Submit</button>

        </div>
    </div>
    </form>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {

    var combo_sevice_arr = [];
    var total_price = 0;
    var max_discount = 0;
    var place_id_arr = [];

   $(".combo_service").click(function(){

    var cs_price = $(this).attr('cs_price');
    var discount = $("#discount").val();
    var cs_id = $(this).val();

    if(discount == "")
        discount = 0;

    if(combo_sevice_arr.includes(cs_id)){
        total_price -= parseFloat(cs_price);
        combo_sevice_arr.splice( $.inArray(cs_id, combo_sevice_arr), 1 );
    }else{
        combo_sevice_arr.push(cs_id);
        total_price += parseFloat(cs_price);
    }

    $("#payment_amount").val(total_price-parseFloat(discount));
    $("#payment_amount_disable").val(total_price-parseFloat(discount));
    $("#payment_amount_hidden").val(total_price-parseFloat(discount));
    $("#service_price").val(total_price);
    $("#service_price_hidden").val(total_price);
    max_discount= total_price*10/100;

   });
   $("#discount").keyup(function(){

    discount = $(this).val();
    if(discount == "")
        discount = 0;

    if(discount > max_discount){
        discount = max_discount;
        $("#discount").val(max_discount);
        toastr.error('Max discount is 10% Service Price');
    }
     $("#payment_amount_disable").val(total_price-parseInt(discount));
     $("#payment_amount").val(total_price-parseInt(discount));
     $("#payment_amount_hidden").val(total_price-parseFloat(discount));
   });

   $(".btn-search").click(function(){

    var customer_phone = $("#customer_phone").val();

    if(customer_phone != "")
    {
        $.ajax({
            url: '{{route('get-customer-infor')}}',
            type: 'GET',
            dataType: 'html',
            data: {customer_phone: customer_phone},
        })
        .done(function(data) {
            console.log(data);
            data = JSON.parse(data);
            if(data.status == 'error'){
                $("#customer_bussiness").val("");
                $("#customer_fullname").val("");
                $("#customer_id").val("");
                $("#salon_list").html("");
                toastr.error(data.message);
            }
            else{
                $("#customer_bussiness").val(data.customer_info.ct_salon_name);
                $("#customer_fullname").val(data.customer_info.ct_firstname+" "+data.customer_info.ct_lastname);
                $("#customer_id").val(data.customer_info.id);
                if(data.place_list != ""){

                    var salon_html ="";
                    $.each(data.place_list, function(index, val) {
                        salon_html += '<div class="col-md-3"><label class="ml-3 text-uppercase text-dark"><input style="width:20px;height: 20px" type="radio" class="place_id"  name="place_id" value="'+val.place_id+'"><b>'+val.place_name+'</b></label></div>';
                    });
                    $("#salon_list").html(salon_html);
                }
            }
        })
        .fail(function() {
            console.log("error");
        });
    }
   });
   $("#credit_card_type").change(function(event) {
       var credit_card_type = $('#credit_card_type :selected').val();
       if(credit_card_type == 'E-CHECK'){
            $(".check").css('display', '');
            $(".credit").css('display', 'none');
       }else{
            $(".check").css('display', 'none');
            $(".credit").css('display', '');
        }
   });
});
</script>
@endpush
