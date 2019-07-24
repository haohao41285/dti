@extends('layouts.app')
@section('content-title')
    Create new Customer
@endsection
@section('content')
<form class="sb-form" action="edit.php">
    <div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4 required">First Name</label>
        <div class="col-sm-8">            
             <input type='text' value=""  class="form-control form-control-sm" /> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
         <label class="col-sm-4 required">Last Name</label>
        <div class="col-sm-8">
            <input type='text' value=""  class="form-control form-control-sm" /> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4 required">Phone(Login)</label>
        <div class="col-sm-8">            
             <input class="form-control form-control-sm maskphone" placeholder="" value=""  type="text" data-inputmask="'mask' : '(999) 999-9999'" >                    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4">Phone Introduced</label>
        <div class="col-sm-8">            
             <input class="form-control form-control-sm maskphone" placeholder="" value="" type="text" data-inputmask="'mask' : '(999) 999-9999'" >                    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
         <label class="col-sm-4 required">Email</label>
        <div class="col-sm-8">
            <input type='text' name="email" value=""  class="form-control form-control-sm" /> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4">Password</label>
        <div class="col-md-8">
            <input type='password' name="password" value=""  class="form-control form-control-sm" />    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4">Password Confirm</label>
        <div class="col-md-8">
            <input type='password' name="password_confirm" value=""  class="form-control form-control-sm" />    
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-md-4">&nbsp;</label>
        <div class="col-md-8">
            <input class="btn btn-sm btn-primary" value="Save changes" type="submit">   
            <input class="btn btn-sm btn-secondary" value="Cancel" type="button">   
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
