@extends('layouts.app')
@section('content-title')
    Customers Management
@endsection
@section('content')
<div class="table-responsive">
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label for="">Created date</label>
            <input type="text" id="created_at" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="">City</label>
            <input type="text" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="">State</label>
            <select name="" id="" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($state as $element)                    
                    <option value="{{$element}}">{{$element}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="">Status</label>
            <select name="" id="" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($status as $element)                    
                    <option value="{{$element}}">{{$element}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-2 " style="position: relative;">
            <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="reset" value="Reset">
            </div>
        </div>      
    </div>
    <hr>
    <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead>                
                <th>ID</th>
                <th>Business</th>
                <th>Contact Name</th>
                <th>Business Phone</th>
                <th>Cell Phone</th>
                <th>Note</th>
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
             buttons: [
                 {   
                     extend: 'csv', 
                     text: '<i class="fas fa-download"></i> Import',
                     className: "btn-sm"
                 },
                 {   
                     extend: 'csv', 
                     text: '<i class="fas fa-upload"></i> Export',
                     className: "btn-sm"
                 }
             ],  
             processing: true,
             serverSide: true,
         ajax:{ url:"{{ route('customersDatatable') }}",
         data: function (d) {

              } 
          },
         columns: [

                  { data: 'id', name: 'id',class:'text-center' },
                  { data: 'ct_salon_name', name: 'ct_salon_name' },
                  { data: 'ct_contact_name', name: 'ct_contact_name'},
                  { data: 'ct_business_phone', name: 'ct_business_phone' ,class:'text-center'},
                  { data: 'ct_cell_phone', name: 'ct_cell_phone',class:'text-center' },
                  { data: 'ct_note', name: 'ct_note',class:'text-center' },
                  { data: 'ct_status', name: 'ct_status',class:'text-center' },
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
        data: {customer_id: customer_id},
      })
      .done(function(data) {

        if(data == 0){
          toastr.error('Get Detaill Customer Error!');
        }else{
          data = JSON.parse(data);
          if(data.ct_salon_name==null)data.ct_salon_name="";
          if(data.ct_contact_name==null)data.ct_contact_name="";
          if(data.ct_business_phone==null)data.ct_business_phone="";
          if(data.ct_cell_phone==null)data.ct_cell_phone="";
          if(data.ct_email==null)data.ct_email="";
          if(data.ct_address==null)data.ct_address="";
          if(data.ct_website==null)data.ct_website="";
          if(data.ct_note==null)data.ct_note="";
          if(data.ct_status==null)data.ct_status="";

          var button = ``;
          if(data.ct_status === 'Arrivals')
            button = `<button type="button" id=`+data.id+` class="btn btn-primary btn-sm get-customer">Assign</button>`;
          $(".modal-content").html(`
            <div class="modal-header">
              <h5 class="modal-title text-center" id="exampleModalLabel"><b>Customer Detail</b></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <div class="modal-body" id="content-customer-detail">
            <div class="row pr-5 pl-5" >
            <div class="col-md-6">
              <div class="row">
                <span class="col-md-4">Business:</span>
                <p class="col-md-8"><b>`+data.ct_salon_name+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Contact Name:</span>
                <p class="col-md-8"><b>`+data.ct_contact_name+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Business Phone:</span>
                <p class="col-md-8"><b>`+data.ct_business_phone+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Cell Phone:</span>
                <p class="col-md-8"><b>`+data.ct_cell_phone+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Email:</span>
                <p class="col-md-8"><b>`+data.ct_email+`</b></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <span class="col-md-4">Address:</span>
                <p class="col-md-8"><b>`+data.ct_address+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Website:</span>
                <p class="col-md-8"><b>`+data.ct_website+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Note:</span>
                <p class="col-md-8"><b>`+data.ct_note+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Created:</span>
                <p class="col-md-8"><b>`+data.created_at+` by `+data.user_nickname+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Status:</span>
                <p class="col-md-8"><b>`+data.ct_status+`</b></p>
              </div>
              <div class="row float-right">
                `+button+`
                <button type="button" class="btn btn-danger btn-sm ml-2 close-customer-detail">Close</button>
              </div>
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
      $(".modal-content").html(``);
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
    $(document).on('click','.edit-customer',function(){

      var customer_id = $(this).attr('customer_id');

      $.ajax({
        url: '{{route('editCustomer')}}',
        type: 'GET',
        dataType: 'html',
        data: {customer_id: customer_id},
      })
      .done(function(data) {
        if(data == 0){
          toastr.error('Getting Error! Check again!');
        }else{
          data = JSON.parse(data);

          $(".modal-content").html(`
             <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel"><b>Edit Customer</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"">
        <form id="edit-customer-form">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group row">
                <label class="col-md-4" for="ct_salon_name">Business Name<i class="text-danger">*</i></label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_salon_name" id="ct_salon_name" value="`+data.ct_salon_name+`" placeholder="">
                <input type="hidden" name="" id="customer_id" value="`+data.id+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="ct_contact_name">Contact Name<i class="text-danger">*</i></label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_contact_name" id="ct_contact_name" value="`+data.ct_contact_name+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="ct_business_phone">Business Phone<i class="text-danger">*</i></label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_business_phone" id="ct_business_phone" value="`+data.ct_business_phone+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="ct_cell_phone">Cell Phone<i class="text-danger">*</i></label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_cell_phone" id="ct_cell_phone" value="`+data.ct_cell_phone+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="ct_email">Email</label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_email" id="ct_email" value="`+data.ct_email+`" placeholder="">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group row">
                <label class="col-md-4" for="ct_address">Address</label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_address" id="ct_address" value="`+data.ct_address+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="ct_website">Website</label>
                <input type="text" class="col-md-8 form-control form-control-sm" name="ct_website" id="ct_website" value="`+data.ct_website+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="ct_note">Note</label>
                <textarea class="col-md-8 form-control form-control-sm" name="ct_note" rows="3" >`+data.ct_note+`</textarea>
              </div>
              <div class="form-group float-right">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm submit-edit" >Submit</button>
              </div>
            </div>
          </div>
        </form>
      </div>
            `);
          $("#viewModal").modal('show');
        }
      })
      .fail(function() {
        console.log("error");
      });
    });
    //SUBMIT EDIT CUSTOMER
    $(document).on("click",".submit-edit",function(){

      var ct_salon_name = $("#ct_salon_name").val();
      var ct_contact_name = $("#ct_contact_name").val();
      var ct_business_phone = $("#ct_business_phone").val();
      var ct_cell_phone = $("#ct_cell_phone").val();
      var ct_email = $("#ct_email").val();
      var ct_address = $("#ct_address").val();
      var ct_website = $("#ct_website").val();
      var ct_note = $("#ct_note").val();
      var customer_id = $("#customer_id").val();

      $.ajax({
        url: '{{route('save-customer')}}',
        type: 'GET',
        dataType: 'html',
        data: {
          ct_salon_name: ct_salon_name,
          ct_contact_name: ct_contact_name,
          ct_business_phone: ct_business_phone,
          ct_cell_phone: ct_cell_phone,
          ct_email: ct_email,
          ct_address: ct_address,
          ct_website: ct_website,
          ct_note: ct_note,
          customer_id: customer_id
        },
      })
      .done(function(data) {
        if(data == 0){
          toastr.error('Update Error! Check again!');
        }else{
          toastr.success('Update Success!');
          $("#viewModal").modal('hide');
          table.ajax.reload(null, false);
          $(".modal-content").html("");
        }
        console.log(data);
      })
      .fail(function() {
        console.log("error");
      });
      

    })
});
</script>
@endpush

