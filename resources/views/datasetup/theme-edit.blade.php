@extends('layouts.app')
@section('content-title')
    Edit Theme
@endsection
@section('content')
<form class="sb-form" action="edit.php">
    <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-sm-4 required">Theme Name</label>
            <div class="col-sm-8">            
                 <input type='text' value=""  class="form-control form-control-sm" /> 
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
             <label class="col-sm-4 required">Theme Code</label>
            <div class="col-sm-8">
                <input type='text' value=""  class="form-control form-control-sm" /> 
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-sm-4 required">Price</label>
            <div class="col-sm-8 input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                 </div>
                <input type="text" class="form-control">
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
             <label class="col-sm-4 required">URL</label>
            <div class="col-sm-8">
                <input type='text' value=""  class="form-control form-control-sm" /> 
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
             <label class="col-sm-4">License</label>
            <div class="col-sm-8">
                <input type='text' value=""  class="form-control form-control-sm" /> 
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
             <label class="col-sm-4 required">Status</label>
            <div class="col-sm-8">
                <input type="checkbox" class="js-switch" checked="checked" />
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
             <label class="col-sm-4">Description</label>
            <div class="col-sm-8">
                <textarea  class="form-control form-control-sm"></textarea> 
            </div>
        </div>    
    </div>
        <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-sm-4 align-self-baseline">Image</label>
             <div class="col-sm-8">                
                 <div class="custom-file">
                    <input type="file" class="custom-file-input" id="customFile">
                    <label class="custom-file-label" for="customFile">Choose image file</label>
                </div>    
                 <div class="previewImage">
                     <img id="previewImage" src="{{ asset("images/no-image.png")}}" width="100%"/>
                 </div>
            </div>
        </div>    
            
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-md-4">&nbsp;</label>
            <div class="col-md-8">
                <input class="btn btn-sm btn-primary" value="Submit" type="submit">   
                <input class="btn btn-sm btn-secondary" value="Cancel" type="button">   
            </div>
        </div>    
    </div>
</form>    
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
   $("input#customFile").on("change", function(event){
       console.log(event.target);
        $("#previewImage").attr("src",URL.createObjectURL(event.target.files[0]));
   });
});
</script>
@endpush
