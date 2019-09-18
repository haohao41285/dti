@extends('layouts.app')
@section('content-title')
    Create new Customer
@endsection
@section('content')
<form class="sb-form" action="{{route('save-my-customer')}}" method="post">
    @csrf()
    <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-sm-4 required">First Name</label>
            <div class="col-sm-8">            
                 <input type='text' value="{{old('ct_firstname')}}" name="ct_firstname"  class="form-control form-control-sm" /> 
            </div>
        </div>    
    </div>
<div class="form-group">
    <div class="col-md-7 form-inline">
         <label class="col-sm-4 required">Last Name</label>
        <div class="col-sm-8">
            <input type='text' value="{{old('ct_lastname')}}" name="ct_lastname"  class="form-control form-control-sm" /> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
         <label class="col-sm-4 required">Business</label>
        <div class="col-sm-8">
            <input type='text' value="{{old('ct_salon_name')}}" name="ct_salon_name" class="form-control form-control-sm" /> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4 required">Business Phone</label>
        <div class="col-sm-8">            
             <input class="form-control form-control-sm maskphone" placeholder="" value="{{old('ct_business_phone')}}" name="ct_business_phone"  type="text" data-inputmask="'mask' : '(999) 999-9999'" >                    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4">Cell Phone</label>
        <div class="col-sm-8">            
             <input class="form-control form-control-sm maskphone" placeholder="" value="{{old('ct_cell_phone')}}" name="ct_cell_phone"  type="text" data-inputmask="'mask' : '(999) 999-9999'" >                    
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
         <label class="col-sm-4 required">Email</label>
        <div class="col-sm-8">
            <input type='email' name="ct_email" value="{{old('ct_email')}}"  class="form-control form-control-sm" /> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4 required" >Address</label>
        <div class="col-md-8">
            <input type='text' name="ct_address" value="{{old('ct_address')}}"  class="form-control form-control-sm" />    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4">Website</label>
        <div class="col-md-8">
            <input type='text' name="ct_website" value="{{old('ct_website')}}"  class="form-control form-control-sm" />    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4">Note</label>
        <div class="col-md-8">
            <textarea name="ct_note" class="form-control form-control-sm" rows="3" value="{{old('ct_note')}}"></textarea>   
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4">&nbsp;</label>
        <div class="col-md-8">
            <input class="btn btn-sm btn-danger" value="Cancel" type="button">
            <input class="btn btn-sm btn-primary" value="Submit" type="submit">
        </div>
    </div>    
</div>
</form>    
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
   
});
</script>
@endpush
