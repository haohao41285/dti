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
         ajax:{ url:"{{ route('get-my-customer') }}",
         data: function (d) {

              } 
          },
         columns: [

                  { data: 'id', name: 'id',class:'text-center' },
                  { data: 'ct_salon_name', name: 'ct_salon_name' },
                  { data: 'ct_contact_name', name: 'ct_contact_name'},
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
          if(data.ct_status === 'Assigned')
            button = `<button type="button" id=`+data.id+` class="btn btn-primary btn-sm get-customer">Get</button>`;
          $("#content-customer-detail").html(`
            <div class="row pr-5 pl-5" >
          <div class="col-md-6">
            <div class="row">
              <span class="col-md-4">Salon Name:</span>
              <h5 class="col-md-8">`+data.ct_salon_name+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Contact Name:</span>
              <h5 class="col-md-8">`+data.ct_contact_name+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Business Phone:</span>
              <h5 class="col-md-8">`+data.ct_business_phone+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Cell Phone:</span>
              <h5 class="col-md-8">`+data.ct_cell_phone+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Email:</span>
              <h5 class="col-md-8">`+data.ct_email+`</h5>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <span class="col-md-4">Address:</span>
              <h5 class="col-md-8">`+data.ct_address+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Website:</span>
              <h5 class="col-md-8">`+data.ct_website+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Note:</span>
              <h5 class="col-md-8">`+data.ct_note+`</h5>
            </div>
            <div class="row">
              <span class="col-md-4">Status:</span>
              <h5 class="col-md-8">`+data.ct_status+`</h5>
            </div>
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
});
</script>
@endpush

