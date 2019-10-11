@extends('layouts.app')
@section('content-title')
{{-- Places --}}
@endsection
@push('styles')
<style>
    .row-detail{
    margin-top: 12px;
    }
</style>
@endpush
@section('content')
<div class="col-12 ">
    <div class="card shadow mb-4 ">
        <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-primary">Places List </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="places-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            {{-- 
                            <th>Email</th>
                            --}}
                            <th>Phone</th>
                            <th>License</th>
                            <th>Created Date</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view" tabindex="-1" role="dialog">
    <div style="max-width: 90%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change a user password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-8 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Users List </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="user-datatable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Full name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " id="user_nickname">Change password </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="change-password-form">
                                @csrf
                                <div class="form-group row col-12">
                                    <label class="col-5">New Password</label>
                                    <input class="col-7 form-control-sm form-control" type="password" name="newPassword">
                                </div>
                                <div class="form-group row col-12">
                                    <label class="col-5">Confirm Password</label>
                                    <input class="col-7 form-control-sm form-control" type="password" name="confirmPassword">
                                </div>
                                <div class="form-group col-12 row">
                                    <label class="col-5"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Save">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- 
            <div class="modal-footer">
                <button type="button" class="btn-sm btn btn-primary">Save changes</button>
                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            --}}
        </div>
    </div>
</div>
{{-- detail place --}}
<div class="modal fade" id="detail" tabindex="-1" role="dialog">
    <div style="max-width: 90%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- 
                <h5 class="modal-title">Detail place</h5>
                --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-12 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Detail place</h6>
                        </div>
                        <div class="card-body">
                            <div class="col-12 row">
                                <div class="col-6">
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Logo</label>
                                        <div class="previewImage" id="logo">
                                            <img id="previewImageAppbanner" src="http://localhost:8000/images/no-image.png">
                                        </div>
                                    </div>
                                    <div class="row col-12 row-detail" >
                                        <label class="col-sm-4">Business name</label>
                                        <label class="col-sm-8" id="bussiness-name">Business name</label>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Tax code</label>
                                        <label class="col-sm-8" id="tax-code">Tax code</label>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Business phone</label>
                                        <label class="col-sm-8" id="business-phone">Business phone</label>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Price floor</label>
                                        <label class="col-sm-8" id="price-floor">Price floor</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Favicon</label>
                                        <div class="previewImage" id="favicon">
                                            <img id="previewImageAppbanner" src="http://localhost:8000/images/no-image.png">
                                        </div>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Address</label>
                                        <label class="col-sm-8" id="address">Address</label>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Email</label>
                                        <label class="col-sm-8" id="email">Email</label>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Website</label>
                                        <label class="col-sm-8" id="website">Website</label>
                                    </div>
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-4">Interest($)</label>
                                        <label class="col-sm-8" id="interest">Interest($)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row col-12 row-detail">
                                    <label class="col-sm-2">Hide service price</label>                              
                                    <label class="col-sm-8 row" id="hide-service-price">Hide service price</label>                              
                                </div>
                                <div class=" form-group">
                                    <div class="col-md-12 row row-detail">
                                        <label class="col-sm-2" ">Working Day</label>
                                        <div class="col-sm-10 workingtime">
                                            <div class="col-day">
                                                <label>Monday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="monday">
                                                    <label class="btn btn-sm btn-day " rel="open">
                                                    <input disabled value="1" type="radio" > Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day " rel="close">
                                                    <input disabled  value="0" type="radio" > Close
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-day">
                                                <label>Tuesday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="tuesday">
                                                    <label class="btn btn-sm btn-day " rel="open">
                                                    <input disabled name="work_tue"  value="1" type="radio" > Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day " rel="close">
                                                    <input disabled name="work_tue" value="0" type="radio"> Close
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-day">
                                                <label>Wednesday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="wednesday">
                                                    <label class="btn btn-sm btn-day  " rel="open">
                                                    <input disabled name="work_wed"  value="1" type="radio" > Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day " rel="close">
                                                    <input disabled name="work_wed" value="0" type="radio" > Close
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-day">
                                                <label>Thursday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="thursday">
                                                    <label class="btn btn-sm btn-day " rel="open">
                                                    <input disabled name="work_thur"  value="1" type="radio"> Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day " rel="close">
                                                    <input disabled name="work_thur" value="0" type="radio" > Close
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-day">
                                                <label>Friday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="friday">
                                                    <label class="btn btn-sm btn-day " rel="open">
                                                    <input disabled name="work_fri"  value="1" type="radio" > Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day" rel="close">
                                                    <input disabled name="work_fri" value="0" type="radio" > Close
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-day">
                                                <label>Saturday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="saturday">
                                                    <label class="btn btn-sm btn-day " rel="open">
                                                    <input disabled name="work_sat"  value="1" type="radio" > Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day " rel="close">
                                                    <input disabled name="work_sat" value="0" type="radio" > Close
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-day">
                                                <label>Sunday</label>
                                                <div class="btn-group btn-group-toggle working-day" rel="sunday">
                                                    <label class="btn btn-sm btn-day" rel="open">
                                                    <input disabled name="work_sun"  value="1" type="radio" > Open
                                                    </label>
                                                    <label class="btn btn-sm btn-day " rel="close">
                                                    <input disabled name="work_sun" value="0" type="radio" > Close
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class=" form-group">
                                        <div class="col-md-12 row">
                                            <label class="col-sm-2">Time Start</label>
                                            <div class="col-sm-10 time-start workingtime">
                                                <div class="col-day input-group-spaddon day_monday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="mon-start" value="19:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_tuesday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="tue-start" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_wednesday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="wed-start" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_thursday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="thustart" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_friday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="fri-start" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_saturday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="sat-start" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_sunday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="sun-start" value="23:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" form-group">
                                        <div class="col-md-12 row">
                                            <label class="col-sm-2">Time End</label>
                                            <div class="col-sm-10 time-end workingtime">
                                                <div class="col-day input-group-spaddon day_monday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="mon-end" value="19:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_tuesday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="tue-end" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_wednesday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="wed-end" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_thursday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="thu-end" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_friday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="fri-end" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_saturday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="sat-end" value="21:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                                <div class="col-day input-group-spaddon day_sunday" >
                                                    <div class="input-group date">
                                                        <input disabled type="text" id="sun-end" value="23:00" class="form-control form-control-sm timepicker">
                                                        <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                        </span>                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="col-sm-2">Description</label>
                                        <label class="col-sm-8" id="description">Description</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- 
            <div class="modal-footer">
                <button type="button" class="btn-sm btn btn-primary">Save changes</button>
                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            --}}
        </div>
    </div>
</div>
{{-- setting modal --}}
<div class="modal fade" id="setting" tabindex="-1" role="dialog">
    <div style="max-width: 80%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setting place theme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " >Website themes </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="themes-dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>ID</th>
                                        <th>Theme Code</th>
                                        <th>Image</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <button class="btn-sm btn btn-success btn-copy-theme">Copy theme</button>
                        <button class="btn-sm btn btn-warning btn-copy-properties">Copy properties</button>
                    </div>
                    <div class="card shadow mb-4 copy-theme">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " >Setting </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="setting-form">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-2">License</label>
                                    <label id="get-license"><b>837ec5754f503cfaaee0929fd48974e7</b></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Website</label>
                                    <input class="col-10 form-control-sm form-control" type="text" name="website">
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Branch</label>
                                    <input class="col-10 form-control-sm form-control" type="text" name="branch">
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Theme</label>
                                    <label id="get-code"><b>demo 10</b></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Update">
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- properties -->
                    <div class="copy-properties" style="display: none">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " >Setting </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="copy-properties-form">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-2">License</label>
                                    <label id="get-license"><b></b></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Website</label>
                                    <input class="col-10 form-control-sm form-control" type="text" name="website">
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Branch</label>
                                    <input class="col-10 form-control-sm form-control" type="text" name="branch">
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Theme</label>
                                    <label id="get-code"><b></b></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Update">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " >Website properties </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="position: relative; overflow: auto; max-height: 70vh; width: 100%;">
                                <table  class="dataTable  table table-bordered table-hover dataTables_scrollBody dataTables_scroll" id="themeProperties" width="100%" cellspacing="0">
                                    <thead>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn-sm btn btn-primary">Update</button> --}}
                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    function clear(){
      $("#change-password-form")[0].reset();
      $("#setting-form")[0].reset();
      $("#user_nickname").html("Change password");
      $("label#get-code b").text("");
      $('#themes-datatable tbody tr.selected').removeClass('selected');
    }

    function listThemePropertiesByThemeId(theme_id){
        $.ajax({
            url:"{{ route('listThemePropertiesByThemeId') }}",
            method:"get",
            dataType:"json",
            data:{theme_id},
            success:function(data){
                if(data.status == 1){
                    var html = '';
                    for(var i = 0; i < data.data.length; i++){
                            html    +='<tr properties-id='+data.data[i].theme_properties_id+'>'
                                    +'<td>'+data.data[i].theme_properties_id+'</td>'
                                    +'<td>'+data.data[i].theme_properties_name +'</td>'
                                    +'<td><img style="height: 5rem;" src="'+"{{env('URL_FILE_VIEW')}}" + data.data[i].theme_properties_image+'" /></td>'
                                    +'</tr>'
                    }

                    $("#themeProperties tbody").html(html);
                }
            }, 
            error:function(){
                toastr.error("Failed to load Properties!");
            }
        });
    }
    
    $(document).ready(function() {
      var placeId = null;
      var userId = null;
      var license = null;
      var themeId = null;
    
      var placeTable = $('#places-datatable').DataTable({
           // dom: "lfrtip",    
           processing: true,
           serverSide: true,
           ajax:{ url:"{{ route('getPlacesDatatable') }}" },
           columns: [
    
                    { data: 'place_id', name: 'place_id',class:'text-center' },
                    { data: 'place_name', name: 'place_name' },
                    { data: 'place_address', name: 'place_address'},
                    // { data: 'place_email', name: 'place_email' },
                    { data: 'place_phone', name: 'place_phone',class:'text-center' },
                    { data: 'place_ip_license', name: 'place_ip_license' },
                    { data: 'created_at', name: 'created_at' ,class:'text-center'},                
                    { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
            ], 
            buttons: [
    
                ],      
      });
    
      var customerTable = $('#user-datatable').DataTable({
           // dom: "lfrtip",    
           processing: true,
           serverSide: true,
           ajax:{
              url:"{{ route('getUsersDatatable') }}",
              data:function(data){
                data.placeId = placeId;
              },
            },
           columns: [
    
                    { data: 'user_id', name: 'user_id',class:'text-center' },
                    { data: 'user_nickname', name: 'user_nickname', class:'user_nickname' },
                    { data: 'user_phone', name: 'user_phone',class:'text-center' },
                    { data: 'user_email', name: 'user_email' },
                    { data: 'created_at', name: 'created_at' ,class:'text-center'},                
                    // { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
            ], 
            buttons: [
    
                ],      
      });
       
    
       var themesTable = $('#themes-dataTable').DataTable({
                 processing: true,
                 serverSide: true,
                 ajax:{ url:"{{ route('getDatatableWebsiteThemes') }}",},
                 columns: [
                      { data: 'theme_id', name: 'theme_id' ,class:"id"},
                      { data: 'theme_name_temp', name: 'theme_name_temp' ,class:"code"},
                      { data: 'theme_image', name: 'theme_image' },
     
                ],       
                 buttons: [
    
                   ],
          });
    
    
      $(document).on('click','.view',function(e){
        e.preventDefault();
        placeId = $(this).attr('data-id');
        customerTable.draw();
        $("#view").modal("show");
        clear();
      });
    
      $("#user-datatable tbody").on('click',"tr",function(){
          $('#user-datatable tbody tr.selected').removeClass('selected');
          $(this).addClass('selected');
          userId = $(this).find("td.sorting_1").text();
          var user_nickname = $(this).find("td.user_nickname").text();
    
          $("#user_nickname").html("Change password of <b>"+user_nickname+"</b>");
      });
    
      $("#change-password-form").on('submit',function(e){
        e.preventDefault();
    
        var checkSelected = $('#user-datatable tbody tr.selected');
        if(checkSelected.length == 0){
          toastr.error("Please select the user!");
          return false;
        }
    
        var form = $(this).serialize();
        form += "&placeId="+placeId;
        form += "&userId="+userId;
    
        $.ajax({
          url:"{{ route('changeNewPassword') }}",
          method:"post",
          data:form,
          dataType:"json",
          success:function(data){
            if(data.status == 1){
              toastr.success("Changed successfully!");
              clear()
            } else {
              toastr.error(data.msg);
            }
          },
          error:function(){
            toastr.error("Failed to change!");
          }
        });
      });
    
      $(document).on('click',".detail",function(e){
        e.preventDefault();
        var placeId = $(this).attr('data-id');
        $.ajax({
          url:"{{ route('getDetailPlace') }}",
          method:"get",
          dataType:"json",
          data:{placeId},
          success:function(data){
            if(data.status == 1){
              var hide_service_price = data.data.place.hide_service_price == 1 ? "On" : "Off" ;
              $("#detail").modal("show");
    
              $("#logo img").attr('src',"{{env('URL_FILE_VIEW')}}"+data.data.place.place_logo);
              $("#Favicon img").attr('src',"{{env('URL_FILE_VIEW')}}"+data.data.place.place_favicon);
              $("#business-name").text(data.data.place.place_name);
              $("#tax-code").text(data.data.place.place_taxcode);
              $("#price-floor").text(data.data.place.place_worker_mark_bonus);
              $("#hide-service-price").text(hide_service_price);
              $("#address").text(data.data.place.place_address);
              $("#email").text(data.data.place.place_email);
              $("#interest").text(data.data.place.place_interest);
              $("#description").text(data.data.place.place_description);
              $("#business-phone").text(data.data.place.place_phone);
              $("#website").text(data.data.place.place_website);
    
              var monClosed = data.data.place_actiondate.mon.closed;
              var tueClosed = data.data.place_actiondate.tue.closed;
              var wedClosed = data.data.place_actiondate.wed.closed;
              var thuClosed = data.data.place_actiondate.thur.closed;
              var friClosed = data.data.place_actiondate.fri.closed;
              var satClosed = data.data.place_actiondate.sat.closed;
              var sunClosed = data.data.place_actiondate.sun.closed;
    
              var monStart = data.data.place_actiondate.mon.start;
              var tueStart = data.data.place_actiondate.tue.start;
              var wedStart = data.data.place_actiondate.wed.start;
              var thuStart = data.data.place_actiondate.thur.start;
              var friStart = data.data.place_actiondate.fri.start;
              var satStart = data.data.place_actiondate.sat.start;
              var sunStart = data.data.place_actiondate.sun.start;
    
              var monEnd = data.data.place_actiondate.mon.end;
              var tueEnd = data.data.place_actiondate.tue.end;
              var wedEnd = data.data.place_actiondate.wed.end;
              var thuEnd = data.data.place_actiondate.thur.end;
              var friEnd = data.data.place_actiondate.fri.end;
              var satEnd = data.data.place_actiondate.sat.end;
              var sunEnd = data.data.place_actiondate.sun.end;
    
              $("label").removeClass("active")
              $(".time-start input").css("visibility","");
              $(".time-end input").css("visibility","");
    
              if(monClosed == true){
                 $("div[rel='monday']").find("label[rel='close']").addClass("active");
                 $("#mon-start").css("visibility","hidden");
                 $("#mon-end").css("visibility","hidden");
              } else {
                 $("div[rel='monday']").find("label[rel='open']").addClass("active");
                 $("#mon-start").val(monStart);
                 $("#mon-end").val(monEnd);
              }
    
              if(tueClosed == true){
                 $("div[rel='tuesday']").find("label[rel='close']").addClass("active");
                 $("#tue-start").css("visibility","hidden");
                 $("#tue-end").css("visibility","hidden");
              } else {
                 $("div[rel='tuesday']").find("label[rel='open']").addClass("active");
                 $("#tue-start").val(tueStart);
                 $("#tue-end").val(tueEnd);
              }
    
              if(wedClosed == true){
                 $("div[rel='wednesday']").find("label[rel='close']").addClass("active");
                 $("#wed-start").css("visibility","hidden");
                 $("#wed-end").css("visibility","hidden");
              } else {
                 $("div[rel='wednesday']").find("label[rel='open']").addClass("active");
                 $("#wed-start").val(wedStart);
                 $("#wed-end").val(wedEnd);
              }
    
              if(thuClosed == true){
                 $("div[rel='thursday']").find("label[rel='close']").addClass("active");
                 $("#thu-start").css("visibility","hidden");
                 $("#thu-end").css("visibility","hidden");
              } else {
                 $("div[rel='thursday']").find("label[rel='open']").addClass("active");
                 $("#thu-start").val(thuStart);
                 $("#thu-end").val(thuEnd);
              }
    
              if(friClosed == true){
                 $("div[rel='friday']").find("label[rel='close']").addClass("active");
                 $("#fri-start").css("visibility","hidden");
                 $("#fri-end").css("visibility","hidden");
              } else {
                 $("div[rel='friday']").find("label[rel='open']").addClass("active");
                 $("#fri-start").val(friStart);
                 $("#fri-end").val(friEnd);
              }
    
              if(satClosed == true){
                 $("div[rel='saturday']").find("label[rel='close']").addClass("active");
                 $("#sat-start").css("visibility","hidden");
                 $("#sat-end").css("visibility","hidden");
              } else {
                 $("div[rel='saturday']").find("label[rel='open']").addClass("active");
                 $("#sat-start").val(satStart);
                 $("#sat-end").val(satEnd);
              }
    
              if(sunClosed == true){
                 $("div[rel='sunday']").find("label[rel='close']").addClass("active");
                 $("#sun-start").css("visibility","hidden");
                 $("#sun-end").css("visibility","hidden");
              } else {
                 $("div[rel='sunday']").find("label[rel='open']").addClass("active");
                 $("#sun-start").val(sunStart);
                 $("#sun-end").val(sunEnd);
              }
    
    
            }
          },
          error:function(){
            toastr.error("Failed to get data");
          }
        });
      });
    
      
      $(document).on('click','.setting',function(e){
          e.preventDefault();
          $("#setting").modal("show");
          license = $(this).attr("data-license");
          $("label#get-license b").text(license);
          clear();
      });
    
      $("#themes-datatable tbody").on('click',"tr",function(){
             $('#themes-datatable tbody tr.selected').removeClass('selected');
             $(this).addClass('selected');
             themeId = $(this).find("td.id").text();
             var code = $(this).find("td.code").text();
             $("label#get-code b").text(code);
             listThemePropertiesByThemeId(themeId);
       });

      $(".btn-copy-theme").on('click',function(){
            $(".copy-theme").fadeIn(300);
            $(".copy-properties").hide(300);
      });
      $(".btn-copy-properties").on('click',function(){
            $(".copy-theme").hide(300);
            $(".copy-properties").fadeIn(300);
      });

      $("#themeProperties tbody").on('click',"tr",function(){

            var checkSelected = $(this).hasClass("selected");
            if(checkSelected){
                $(this).removeClass('selected');
            } else{
                $(this).addClass('selected');
            }
       });
    
    
    });
    
    
</script>
@endpush