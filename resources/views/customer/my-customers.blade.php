@extends('layouts.app')
@section('content-title')
    Customers Management
@endsection
@section('content')
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
        <div class="col-md-2">
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
    <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead>                
                <th>ID</th>
                <th>Nail Shop</th>
                <th>Contact Name</th>
                <th>Business Phone</th>
                <th>Cell Phone</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
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
           { 
               text: '<i class="fas fa-download"></i> Import',
               className: "btn-sm import-show"
           },
           { 
               text: '<i class="fas fa-upload"></i> Export',
               className: "btn-sm export",
               action: function ( e, dt, node, config ) {
                  document.location.href = "{{route('export-my-customer')}}";
              }
           }
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
                { data: 'updated_at', name: 'updated_at' ,class:'text-center'},                
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
        // console.log(data);
        // return;

        if(data == 0){
          toastr.error('Get Detaill Customer Error!');
        }else{
          data = JSON.parse(data);
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
                </tr>;`
            });
            place_service = `<table class="table table-hovered table-bordered" style="width: 100%">
              <thead>
                <tr>
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
          if(data.customer_list.ct_status === 'Arrivals')
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

      
        console.log(data);
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
});
</script>
@endpush

