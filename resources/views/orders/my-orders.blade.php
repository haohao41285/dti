@extends('layouts.app')
@section('title')
    My Orders
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
                <th>Id</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Services</th>
                <th>Subtotal($)</th>
                <th>Discount($)</th>
                <th>Total Charged($)</th>
                <th>Order Status</th>
                <th>Task#</th>
            </tr>
        </thead>
    </table>
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
                     text: '<i class="fas fa-download"></i> Import',
                     className: "btn-sm import-show",
                 },
                 {   
                     text: '<i class="fas fa-upload"></i> Export',
                     className: "btn-sm",
                     action: function ( e, dt, node, config ) {
                        document.location.href = "{{route('export-customer')}}";
                    }
                 }
             ],  
             processing: true,
             serverSide: true,
         ajax:{ url:"{{ route('customersDatatable') }}",
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
                  { data: 'ct_note', name: 'ct_note',class:'text-center' },
                  { data: 'ct_status', name: 'ct_status',class:'text-center' },
                  { data: 'created_at', name: 'created_at' ,class:'text-center'},                
                  { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
          ],       
    });
});
</script>
@endpush