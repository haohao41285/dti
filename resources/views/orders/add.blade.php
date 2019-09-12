@extends('layouts.app')
@section('content-title')
    New Order
@endsection
@push('styles')
<style>
    .form-group {
    margin-bottom: .5rem;
}
</style>
@endpush
@section('content')
<div class="">
    <form action="{{route('authorize')}}" method="get">
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label class="required">Customer phone:</label>
        </div>
        <div class="col-md-4" >
            <input type="text" class="input-sm form-control form-control-sm"  name="customer_phone" />
        </div>
    </div>
	<div class="col-12 row">
        <div class="col-md-2">
            <label class="required">Services</label>
        </div>
             <div class="col-md-5">
                @foreach($combo_service_list as $key => $cs)
                @if($key == $count)
                </div>
                <div class="col-md-5">
                @endif
                <label><input style="width:20px;height: 20px" type="checkbox" name="cs_id" value=""> {{$cs->cs_name}} - ${{$cs->cs_price}}</label><br>
                @endforeach
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Service Price</label>
        <div class="col-md-4"><input disabled type="text" class="form-control form-control-sm" name="service_price" value="{{old('service_price')}}"></div>   
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Discount($)</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="discount" value="{{old('discount')}}"></div>   
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Payment Amount($)</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="payment_amount" value="{{old('payment_amount')}}"></div>   
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Credit Card Type</label>
        <div class="col-md-4"><select class="form-control form-control-sm" name="credit_card_type">
            <option value="1">MasterCard</option>
            <option value="2">VISA</option>
            <option value="3">Discover</option>
            <option value="4">American Express</option>
            <option value="5">E-CHECK</option>
        </select></div>   
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">Credit Card Number</label>
        <div class="col-md-4"><input class="form-control form-control-sm" type="text" name="credit_card_number"  value="{{old('credit_card_number')}}"></div>   
    </div>
    <div class="col-md-12 form-group row">
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
    <div class="col-md-12 form-group row">
        <label class="col-md-2 required">CVV Number</label>
        <div class="col-md-3"><input class="form-control form-control-sm" type="text"  value="{{old('cvv_number')}}" name="cvv_number"></div>   
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
        <label class="col-md-2">Zip Code</label>
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
   
});
</script>
@endpush