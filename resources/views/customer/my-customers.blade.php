@extends('layouts.app')
@section('content')
    <h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info">MY CUSTOMER</h4>
    <div class="modal fade" id="move-customers-modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><b>MOVE CUSTOMERS</b></h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="get" accept-charset="utf-8">
                    <div class="modal-body">
                        <table class="table table-sm table-hover table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th>Customer</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($customer_list as $customer)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" value="{{$customer->ct_salon_name}}" name="customer_name" disabled>
                                        <input type="hidden" value="{{$customer->id}}" name="customer_id[]">
                                    </td>
                                    <td>
                                        <select name="user_id[]" id="user_id" class="form-control form-control-sm text-capitalize">
                                            <option value="0"></option>
                                            @foreach($user_list as $user)
                                                <option value="{{$user->user_id}}">{{$user->user_nickname}} ( {{$user->getFullname()}} )</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm cancel" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary move-customers-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div class="modal fade" id="move-modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-info"><b>MOVE CUSTOMER:</b></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="get" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="input-group mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Move Customer:</div>
                        </div>
                        <input type="text" class="form-control text-info"  id="contact_name" disabled>
                    </div>
                    <div class="input-group mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">To User:</div>
                        </div>
                        <select name="user_id" id="user_id" class="form-control  text-capitalize">
                            @foreach($user_list as $user)
                                <option value="{{$user->user_id}}">{{$user->user_nickname}} ( {{$user->getFullname()}} )</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="customer_id" id="customer_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm cancel" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary move-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="move-place-modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-info"><b>MOVE PLACE:</b></h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="form-place" action="" method="get" accept-charset="utf-8">
                <div class="modal-body">
                    <div class="input-group mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Move Place:</div>
                        </div>
                        <input type="text" class="form-control text-info"  id="place_name" disabled>
                        <input type="hidden" name="place_id" id="place_id_hidden">
                        <input type="hidden" name="customer_id" id="customer_id_hidden">
                    </div>
                    <div class="input-group mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">To User:</div>
                        </div>
                        <select name="user_id" id="user_id" class="form-control  text-capitalize">
                            @foreach($user_list as $user)
                                <option value="{{$user->user_id}}">{{$user->user_nickname}} ( {{$user->getFullname()}} )</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm cancel-move" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary move-place-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="add-note-modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-info"><b>ADD CUSTOMER NOTE:</b></h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" method="post" accept-charset="utf-8">
                    <div class="modal-body">
                        <div class="input-group mb-2 mr-sm-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">For Customer:</div>
                            </div>
                            <input type="text" class="form-control text-info"  id="customer_name_note" disabled>
                        </div>
                        <label for="customer_note">Note:</label>
                        <textarea name="customer_note" id="customer_note" class="form-control form-control-sm" rows="3"></textarea>
                        <input type="hidden" name="customer_id_note" id="customer_id_note" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm cancel" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary add-note-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="table-responsive">
    <div class="form-group col-md-12 row">
        <div class="col-md-4">
            <label for="">Created date</label>
            <div class="input-daterange input-group" id="created_at">
              <input type="text" class="input-sm form-control form-control-sm" id="start_date" name="start" />
              <span class="input-group-addon">to</span>
              <input type="text" class="input-sm form-control form-control-sm" id="end_date" name="end" />
            </div>
        </div>
        <div class="col-md-3">
            <label for="">Address</label>
            <input type="text" id="address" name="address" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="">Status</label>
            <select id="status-customer" name="status_customer" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($status as $key =>  $element)
                    <option value="{{$key}}">{{$element}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-2 " style="position: relative;">
            <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="reset" value="Reset">
            </div>
        </div>
    </div>
    <hr>

  <div style="height:700px" style="overflow:auto">
    <table class="table table-sm table-hover" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead>
          <tr class="sticky-top bg-primary text-white"  style="z-index: 9">
            <th>ID</th>
            <th>Business</th>
            <th>Contact Name</th>
            <th>Business Phone</th>
            <th>Cell Phone</th>
            <th>Note</th>
            <th>Status</th>
            <th>Created Date</th>
            <th style="width: 15%">Action</th>
          </tr>
        </thead>
    </table>
  </div>
</div>

<!-- Modal view-->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div style="max-width: 70%" class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel"><b>Customer Detail</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="content-customer-detail">

      </div>
    </div>
  </div>
</div>
{{-- MODAL IMPORT --}}
<div class="modal fade" id="import-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
          <form  method="post" id="customer-import-form" enctype="multipart/form-data" name="customer-import-form">
            <div class="col-md-12">
                <div class="row col-md-12">
                  <a href="" class="blue">Download an import template spreadsheet</a>
                </div>
                <div class="row col-md-12">
                  <input type="file" class="btn btn-sm" id="file" name="file">
                </div>
                <div class="row col-md-12">
                  <label class="col-md-6">Begin Row Index</label>
                  <input type='number' name="begin_row" id="begin_row" class="form-control form-control-sm col-md-6" value="0"/>
                </div>
                <div class="row col-md-12">
                  <label class="col-md-6">End Row Index</label>
                  <input type='number' name="end_row" id="end_row" class="form-control form-control-sm col-md-6" value="1000"/>
                </div>
                <div class="row col-md-12 ">
                     <button type="button" class="btn btn-danger btn-sm float-right cancle-import" >Cancle</button>
                     <button type="button" class="btn btn-primary btn-sm ml-2 float-right submit-form" >Submit</button>
                </div>
            </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
    $("#created_at").datepicker({});
    var table = $('#dataTableAllCustomer').DataTable({
       // dom: "lBfrtip",
       order:[[6,"desc"]],
       processing: true,
       serverSide: true,
       buttons: [
           // {
           //     text: '<i class="fas fa-exchange-alt"></i> Move Customers',
           //     className: "btn-sm move-customers"
           // },
           {{--{--}}
           {{--    text: '<i class="fas fa-download"></i> Import',--}}
           {{--    className: "btn-sm import-show"--}}
           {{--},--}}
           {{--{--}}
           {{--    text: '<i class="fas fa-upload"></i> Export',--}}
           {{--    className: "btn-sm export",--}}
           {{--    action: function ( e, dt, node, config ) {--}}
           {{--       document.location.href = "{{route('export-my-customer')}}";--}}
           {{--   }--}}
           {{--}--}}
       ],
       ajax:{ url:"{{ route('get-my-customer') }}",
       data: function (d) {
          d.start_date = $("#start_date").val();
          d.end_date = $("#end_date").val();
          d.address = $("#address").val();
          d.status_customer = $("#status-customer :selected").val();
            }
        },
       columns: [

                { data: 'id', name: 'id',class:'text-center' },
                { data: 'ct_salon_name', name: 'ct_salon_name' },
                { data: 'ct_fullname', name: 'ct_fullname'},
                { data: 'ct_business_phone', name: 'ct_business_phone' ,class:'text-center'},
                { data: 'ct_cell_phone', name: 'ct_cell_phone',class:'text-center' },
                { data: 'ct_status', name: 'ct_status',class:'text-center' },
                { data: 'ct_note', name: 'ct_note' },
                { data: 'created_at', name: 'created_at' ,class:'text-center'},
                { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
        ],
    });

    $("#reset").on('click',function(e){
        e.preventDefault();
        table.ajax.reload(null, false);
    });


    $(document).on("click",".view",function(){

      var customer_id = $(this).attr('customer_id');

      $.ajax({
        url: '{{route('get-customer-detail')}}',
        type: 'GET',
        dataType: 'html',
        data: {
          customer_id: customer_id,
          my_customer: 1
        },
      })
      .done(function(data) {
        if(data == 0){
          toastr.error('Get Detaill Customer Error!');
        }else{
          data = JSON.parse(data);
          console.log(data);
          if(data.customer_list.ct_salon_name==null)data.customer_list.ct_salon_name="";
          if(data.customer_list.ct_fullname==null)data.customer_list.ct_fullname="";
          if(data.customer_list.ct_business_phone==null)data.customer_list.ct_business_phone="";
          if(data.customer_list.ct_cell_phone==null)data.customer_list.ct_cell_phone="";
          if(data.customer_list.ct_email==null)data.customer_list.ct_email="";
          if(data.customer_list.ct_address==null)data.customer_list.ct_address="";
          if(data.customer_list.ct_website==null)data.customer_list.ct_website="";
          if(data.customer_list.ct_note==null)data.customer_list.ct_note="";
          if(data.customer_list.ct_status==null)data.customer_list.ct_status="";

          var place_service = "";
          var content_table = "";
          if(data.place_arr.length != 0){
            $.each(data.place_arr, function(index, val) {

            content_table +=  `<tr>
                  <td>`+index+`</td>
                  <td>`+val+`</td>
                </tr>`
            });
            place_service = `<table class="table-sm table table-hover table-bordered" style="width: 100%">
              <thead>
                <tr class="thead-light">
                  <th>Places</th>
                  <th>Service</th>
                </tr>
              </thead>
              <tbody>
                `+content_table+`
              </tbody>
            </table>`;
          }
          var button = ``;
          if(data.customer_list.ct_status === 'New Arrivals')
            button = `<button type="button" id=`+data.customer_list.id+` class="btn btn-primary btn-sm get-customer">Get</button>`;
          $("#content-customer-detail").html(`
            <div class="row pr-5 pl-5" >
          <div class="col-md-6">
              <div class="row">
                <span class="col-md-4">Business:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_salon_name+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Contact Name:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_fullname+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Business Phone:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_business_phone+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Cell Phone:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_cell_phone+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Email:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_email+`</b></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <span class="col-md-4">Address:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_address+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Website:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_website+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Note:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_note+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Created:</span>
                <p class="col-md-8"><b>`+data.customer_list.created_at+` by `+data.customer_list.user_nickname+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Status:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_status+`</b></p>
              </div>

            </div>
            `+place_service+`
            <div class="col-md-12">
              <div class="row float-right">
                `+button+`
                <button type="button" class="btn btn-danger btn-sm ml-2 close-customer-detail">Close</button>
              </div>
            </div>
        </div>
            `);
          $("#viewModal").modal('show');
        }
      })
      .fail(function() {
        console.log("error");
      });
    });
    //CLOSE MODAL DETAI CUSTOMER
    $(document).on('click','.close-customer-detail',function(){
      $("#viewModal").modal('hide');
      $("#content-customer-detail").html(``);
    });
    //GET CUSTOMER TO MY CUSTOMER
    $(document).on('click','.get-customer',function(){

      var customer_id = $(this).attr('id');

      $.ajax({
        url: '{{route('add-customer-to-my')}}',
        type: 'GET',
        dataType: 'html',
        data: {customer_id: customer_id},
      })
      .done(function(data) {
        if(data == 1){
          $("#viewModal").modal('hide');
          table.ajax.reload(null, false);
        }else{
          toastr.error('Getting Error! Check again!');
        }
      })
      .fail(function() {
        console.log("error");
      });
    });
    $(document).on('click','.import-show',function(){
      $("#import-modal").modal("show");
    });
     $(".submit-form").click(function(){

      var begin_row = $("#begin_row").val();
      var end_row = $("#end_row").val();

      var formData = new FormData();
      formData.append('begin_row', begin_row);
      formData.append('end_row', end_row);
      formData.append('_token','{{csrf_token()}}')
      formData.append('check_my_customer',1)
      // Attach file
      formData.append('file', $('#file')[0].files[0]);

      $.ajax({
        url: '{{route('import-customer')}}',
        type: 'POST',
        dataType: 'html',
        data: formData,
        contentType: false,
        processData: false
      })
      .done(function(data) {
        data = JSON.parse(data);
        if(data.status == 'success'){
          $("#import-modal").modal('hide');
          table.draw();
          toastr.success(data.message);
        }
        else
          toastr.error(data.message);
        console.log(data);
      })
      .fail(function() {
        console.log("error");
      });
    });
     $("#search-button").click(function(){
      table.draw();
    });
    $("#reset").on('click',function(e){
      $("#start_date").val("");
      $("#end_date").val("");
      $("#address").val("");
      e.preventDefault();
      table.ajax.reload(null, false);
    });
    $(document).on('click','.move-customer',function () {

        var customer_id = $(this).attr('customer_id');
        var contact_name = $(this).attr('contact_name');
        $("#customer_id").val(customer_id);
        $("#contact_name").val(contact_name);

        $("#move-modal").modal("show");
    });
    $(".move-submit").click(function(){

        var formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');

        $.ajax({
            url: '{{route('move-customer')}}',
            type: 'POST',
            dataType: 'html',
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            data: formData,
        })
        .done(function(data) {
            // console.log(data);
            // return;
            data = JSON.parse(data);
            if(data.status == 'error'){
                toastr.error(data.message);
            }else{
                toastr.success(data.message);
                table.ajax.reload(null, false);
                $("#move-modal").modal("hide");
            }
        })
        .fail(function() {
            console.log("error");
        });
    });
     $(document).on('click','.add-note',function () {
         var customer_id = $(this).attr('customer_id');
         var contact_name = $(this).attr('contact_name');

         $("#customer_name_note").val(contact_name);
         $("#customer_id_note").val(customer_id);
         $("#add-note-modal").modal('show');
     });
    $(document).on('click','.add-note-submit',function () {

        var formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');

        $.ajax({
            url: '{{route('add-customer-note')}}',
            type: 'POST',
            dataType: 'html',
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            data: formData,
        })
            .done(function(data) {
                // console.log(data);
                // return;
                data = JSON.parse(data);
                let message = '';
                if(data.status == 'error'){
                    if( typeof(data.message) == 'string')
                        toastr.error(data.message);
                    else{
                        $.each(data.message,function (index,val) {
                            message += val+'\n';
                        });
                        toastr.error(message);
                    }
                }else{
                    toastr.success(data.message);
                    table.ajax.reload(null, false);
                    $("#add-note-modal").modal("hide");
                }
            })
            .fail(function() {
                console.log("error");
            });
    });
    $(document).on('click','.move-customers',function(){
        $("#move-customers-modal").modal('show');
    });
    $(".move-customers-submit").click(function () {

        var formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');
        $.ajax({
            url: '{{route('move-customers')}}',
            type: 'POST',
            dataType: 'html',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
        })
            .done(function(data) {
                // console.log(data);
                // return;
                data = JSON.parse(data);
                let message = '';
                if(data.status == 'error'){
                    if( typeof(data.message) == 'string')
                        toastr.error(data.message);
                    else{
                        $.each(data.message,function (index,val) {
                            message += val+'\n';
                        });
                        toastr.error(message);
                    }
                }else{
                    toastr.success(data.message);
                    table.ajax.reload(null, false);
                    $("#move-customers-modal").modal("hide");
                }
            })
            .fail(function() {
                console.log("error");
            });
    });
     $('#dataTableAllCustomer tbody').on('click', '.details-control', function () {

         var customer_template_id = $(this).attr('id');
         $(this).toggleClass('fa-plus-circle fa-minus-circle');
         var tr = $(this).closest('tr');
         var row = table.row( tr );
         // var team_id = $("#team_id :selected").val();

         if ( row.child.isShown() ) {
             // This row is already open - close it
             row.child.hide();
             tr.removeClass('shown');
         }else{
             $.ajax({
                 url: '{{route('get_place_my_customer')}}',
                 type: 'GET',
                 dataType: 'html',
                 data: {
                     customer_template_id: customer_template_id,
                     team_id: '{{\Auth::user()->user_team}}'
                 },
             })
                 .done(function(data) {
                     data = JSON.parse(data);
                     console.log(data);
                     var subtask_html = "";
                     $.each(data, function(index,val){

                         if(val.get_user.length  != 0) var user_manage = val.get_user.user_nickname;
                         else var user_manage = "";

                         subtask_html += `
                                <tr>
                                    <td>`+val.get_place.place_name+`</td>
                                    <td>`+val.get_place.place_phone+`</td>
                                    <td>`+val.get_place.place_ip_license+`</td>
                                    <td>`+user_manage+`</td>
                                    <td class="text-center">
                                         <a class="btn btn-sm btn-secondary move-place"
                                            place_name="`+val.get_place.place_name+`"
                                            place_id="`+val.get_place.place_id+`"
                                            customer_id="`+val.customer_id+`" href="javascript:void(0)" title="Move Place To User">
                                            <i class="fas fa-exchange-alt"></i>
                                         </a>
                                    </td>
                                </tr> `;
                     });
                     row.child(format(row.data()) +subtask_html+"</table>" ).show();
                     tr.addClass('shown');
                 })
                 .fail(function() {
                     toastr.error('Get SubTask Failed!');
                 });
         }
     } );

     function format ( d ) {
         // `d` is the original data object for the row
         return `<table class="border border-info table-striped table table-border bg-white">
            <tr class="bg-info text-white">
                <th scope="col">Name</th>
                <th scope="col">Phone</th>
                <th>Liences</th>
                <th>User Manager</th>
                <th class="text-center">Action</th>
            </tr>`;
     }
     $(document).on('click',".move-place",function(){
         var place_name = $(this).attr('place_name');
         var place_id = $(this).attr('place_id');
         var customer_id = $(this).attr('customer_id');
         $("#place_id_hidden").val(place_id);
         $("#customer_id_hidden").val(customer_id);
         $("#place_name").val(place_name);
         $("#move-place-modal").modal('show');
     });
     $(".move-place-submit").click(function(){
         var formData = new FormData($(this).parents('form')[0]);
         formData.append('_token','{{csrf_token()}}');
         formData.append('current_user','{{\Auth::user()->user_id}}');
         formData.append('team_id','{{\Auth::user()->user_team}}');

         $.ajax({
             url: '{{route('move_place')}}',
             type: 'POST',
             dataType: 'html',
             processData: false,
             contentType: false,
             data: formData,
         })
             .done(function(data) {
                 // console.log(data);
                 // return;
                 data = JSON.parse(data);
                 if(data.status == 'error')
                     toastr.error(data.message);
                 else{
                     toastr.success(data.message);
                     cleanModalPlace();
                 }
             })
             .fail(function() {
                 toastr.error('Get List User Failed!');
             });
     });
     function cleanModalPlace(){
         $("#form-place")[0].reset();
         $("#move-place-modal").modal('hide');
         table.ajax.reload(null, false);
     }
     $(".cancel-move").click(function () {
         cleanModalPlace();
     });
});
</script>
@endpush

