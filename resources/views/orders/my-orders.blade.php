@extends('layouts.app')
@section('title')
    My Orders
@endsection
@section('content')
<h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info">MY ORDER</h4>
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
       {{--  <div class="col-md-2">
            <label for="">Status</label>
            <select id="status-customer" name="status_customer" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($status as $key =>  $element)
                    <option value="{{$key}}">{{$element}}</option>
                @endforeach
            </select>
        </div> --}}
        <div class="col-2 " style="position: relative;">
            <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="reset" value="Reset">
            </div>
        </div>
    </div>
    <hr>
    <table class="table table-bordered table-hover" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead class="text-center">
            <tr>
                <th>Id</th>
                <th class="order-date">Order Date</th>
                <th>Customer</th>
                <th>Services</th>
                <th>Subtotal($)</th>
                <th>Discount($)</th>
                <th>Total Charged($)</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Information Card</th>
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
            order: [[ 1, "desc" ]],
         // dom: "lBfrtip",
            buttons: [
            ],
            processing: true,
            serverSide: true,
        ajax:{ url:"{{ route('my-order-datatable') }}",

        data: function (d) {
            d.start_date = $("#start_date").val();
            d.end_date = $("#end_date").val();
            d.my_order = 1;
            }
        },
        columns: [

            { data: 'id', name: 'id',class:'text-center' },
            { data: 'order_date', name: 'order_date', class:'text-center' },
            { data: 'customer', name: 'customer'},
            { data: 'servivce', name: 'servivce' },
            { data: 'subtotal', name: 'subtotal',class:'text-right' },
            { data: 'discount', name: 'discount',class:'text-right' },
            { data: 'total_charge', name: 'total_charge',class:'text-right' },
            { data: 'status', name: 'status',class:"text-center" },
            { data: 'updated_at', name: 'updated_at',class:"text-center" },
            { data: 'information', name: 'information'},
        ],
    });
    $("#search-button").click(function(){
        table.draw();
    });
    $(document).on('click','.order_date',function(){

    });
});
</script>
@endpush
