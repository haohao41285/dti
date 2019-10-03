@extends('layouts.app')
@section('content-title')
Places
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
                     <th>Email</th>
                     <th>Phone</th>
                     <th>License</th>
                     <th>Created Date</th>
                     <th>Action</th>
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
                     <h6 class="m-0 font-weight-bold text-primary">Change password</h6>
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
                              <div class="previewImage">
                                 <img id="previewImageAppbanner" src="http://localhost:8000/images/no-image.png">
                              </div>
                           </div>
                           <div class="row col-12 row-detail" >
                              <label class="col-sm-4">Business name</label>
                              <label class="col-sm-8">Business name</label>
                           </div>
                           <div class="row col-12 row-detail">
                              <label class="col-sm-4">Tax code</label>
                           </div>
                           <div class="row col-12 row-detail">
                              <label class="col-sm-4">Price floor</label>
                           </div>
                        </div>
                        <div class="col-6">
                           <div class="row col-12 row-detail">
                              <label class="col-sm-4">Favicon</label>
                              <div class="previewImage">
                                 <img id="previewImageAppbanner" src="http://localhost:8000/images/no-image.png">
                              </div>
                           </div>
                           <div class="row col-12 row-detail">
                              <label class="col-sm-4">Address</label>
                           </div>
                           <div class="row col-12 row-detail">
                              <label class="col-sm-4">Email</label>
                           </div>
                           <div class="row col-12 row-detail">
                              <label class="col-sm-4">Interest($)</label>
                           </div>
                        </div>
                     </div>
                     <div class="col-12">
                        <div>
                           <label class="col-sm-2">Hide service price</label>                              
                        </div>
                        <div class=" form-group">
                           <div class="col-md-12 row row-detail">
                              <label class="col-sm-2" ">Working Day</label>
                              <div class="col-sm-10 workingtime">
                                 <div class="col-day">
                                    <label>Monday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day  active">
                                       <input disabled checked="" value="1" type="radio" rel="monday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day ">
                                       <input disabled  value="0" type="radio" rel="monday"> Close
                                       </label>
                                    </div>
                                 </div>
                                 <div class="col-day">
                                    <label>Tuesday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day  active">
                                       <input disabled name="work_tue" checked="" value="1" type="radio" rel="tuesday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day ">
                                       <input disabled name="work_tue" value="0" type="radio" rel="tuesday"> Close
                                       </label>
                                    </div>
                                 </div>
                                 <div class="col-day">
                                    <label>Wednesday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day  active">
                                       <input disabled name="work_wed" checked="" value="1" type="radio" rel="wednesday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day ">
                                       <input disabled name="work_wed" value="0" type="radio" rel="wednesday"> Close
                                       </label>
                                    </div>
                                 </div>
                                 <div class="col-day">
                                    <label>Thursday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day  active">
                                       <input disabled name="work_thur" checked="" value="1" type="radio" rel="thursday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day ">
                                       <input disabled name="work_thur" value="0" type="radio" rel="thursday"> Close
                                       </label>
                                    </div>
                                 </div>
                                 <div class="col-day">
                                    <label>Friday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day active">
                                       <input disabled name="work_fri" checked="" value="1" type="radio" rel="friday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day">
                                       <input disabled name="work_fri" value="0" type="radio" rel="friday"> Close
                                       </label>
                                    </div>
                                 </div>
                                 <div class="col-day">
                                    <label>Saturday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day  active">
                                       <input disabled name="work_sat" checked="" value="1" type="radio" rel="saturday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day ">
                                       <input disabled name="work_sat" value="0" type="radio" rel="saturday"> Close
                                       </label>
                                    </div>
                                 </div>
                                 <div class="col-day">
                                    <label>Sunday</label>
                                    <div class="btn-group btn-group-toggle working-day" >
                                       <label class="btn btn-sm btn-day  active">
                                       <input disabled name="work_sun" checked="" value="1" type="radio" rel="sunday"> Open
                                       </label>
                                       <label class="btn btn-sm btn-day ">
                                       <input disabled name="work_sun" value="0" type="radio" rel="sunday"> Close
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div>
                           <div class=" form-group">
                              <div class="col-md-12 row">
                                 <label class="col-sm-2">Time End</label>
                                 <div class="col-sm-10 workingtime">
                                    <div class="col-day input-group-spaddon day_monday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_mon" value="19:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_tuesday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_tue" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_wednesday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_wed" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_thursday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_thur" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_friday" style="visibility: visible;">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_fri" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_saturday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_sat" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_sunday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_sun" value="23:00" class="form-control form-control-sm timepicker">
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
                                 <div class="col-sm-10 workingtime">
                                    <div class="col-day input-group-spaddon day_monday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_mon" value="19:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_tuesday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_tue" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_wednesday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_wed" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_thursday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_thur" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_friday" style="visibility: visible;">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_fri" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_saturday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_sat" value="21:00" class="form-control form-control-sm timepicker">
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                          </span>                            
                                       </div>
                                    </div>
                                    <div class="col-day input-group-spaddon day_sunday" style="visibility:">
                                       <div class="input-group date">
                                          <input disabled type="text" name="time_end_sun" value="23:00" class="form-control form-control-sm timepicker">
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
@endsection
@push('scripts')
<script type="text/javascript">
   function clear(){
     $("#change-password-form")[0].reset();
   }
   
   $(document).ready(function() {
     var placeId = null;
     var userId = null;
   
     var placeTable = $('#places-datatable').DataTable({
          // dom: "lfrtip",    
          processing: true,
          serverSide: true,
          ajax:{ url:"{{ route('getPlacesDatatable') }}" },
          columns: [
   
                   { data: 'place_id', name: 'place_id',class:'text-center' },
                   { data: 'place_name', name: 'place_name' },
                   { data: 'place_address', name: 'place_address'},
                   { data: 'place_email', name: 'place_email' },
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
                   { data: 'user_nickname', name: 'user_nickname' },
                   { data: 'user_phone', name: 'user_phone',class:'text-center' },
                   { data: 'user_email', name: 'user_email' },
                   { data: 'created_at', name: 'created_at' ,class:'text-center'},                
                   // { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
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
             $("#detail").modal("show");
           }
         },
         error:function(){
           toastr.error("Failed to get data");
         }
       });
     });

     
   
   
   });
   
   
</script>
@endpush