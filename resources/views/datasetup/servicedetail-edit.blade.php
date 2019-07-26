@extends('layouts.app')
@section('content-title')
    Edit Service Detail
@endsection
@section('content')
<form class="sb-form" action="servicedetails.php">
    <div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4 required">Name</label>
        <div class="col-sm-8">            
            <input type='text' value=""  class="form-control form-control-sm" required/> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
         <label class="col-sm-4">Price</label>
        <div class="col-sm-8 input-group">
            <div class="input-group-prepend">
             <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
            </div>
            <input name="cash" type="number"  class="form-control form-control-sm" value="">
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4">Slogan</label>
       <div class="col-sm-8">            
            <input type='text' value=""  class="form-control form-control-sm"/> 
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4">Description</label>
       <div class="col-sm-8">            
           <textarea type='text' value=""  class="form-control form-control-sm">
               
           </textarea>
        </div>
    </div>    
</div>
<div class="form-group">
    <div class="col-md-7 form-inline">
        <label class="col-sm-4">Status</label>
        <div class="col-sm-8">
           <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" class="custom-control-input" id="customRadio" name="example" value="customEx" checked="checked">
                        <label class="custom-control-label" for="customRadio">Active</label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customRadio2" name="example" value="customEx">
                        <label class="custom-control-label" for="customRadio2">Inactive</label>
                      </div> 
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