@extends('layouts.app')
@section('content-title')
    Seller's Orders
@endsection
@section('content')
<div class="table-responsive">
    <form action="" id="search-form" accept-charset="utf-8">
	<div class="form-group col-md-12 row">
		<div class="col-md-2">
			<label for="">Seller</label>
            <select id="user_id" name="status_customer" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($user_teams as $key =>  $user)
                    <option value="{{$user->user_id}}">{{$user->user_nickname}}</option>
                @endforeach
            </select>
		</div>
        <div class="col-md-4">
            <label for="">Created date</label>
            <div class="input-daterange input-group" id="created_at">
              <input type="text" class="input-sm form-control form-control-sm" id="start_date" name="start" />
              <span class="input-group-addon">to</span>
              <input type="text" class="input-sm form-control form-control-sm" id="end_date" name="end" />
            </div>
        </div>
        {{-- <div class="col-md-2">
            <label for="">Status</label>
            <select id="status-customer" name="status_customer" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($status as $key =>  $element)
                    <option value="{{$key}}">{{$element}}</option>
                @endforeach
            </select>
        </div> --}}
        <div class="col-md-2">
			<label for="">Service</label>
            <select id="service_id" name="status_customer" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($services as $key =>  $service)
                    <option value="{{$service->id}}">{{$service->cs_name}}</option>
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
    </form>
    <hr>
    <table class="table table-sm table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
        <thead>
                <th>Id</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Services</th>
                <th>Subtotal($)</th>
                <th>Discount($)</th>
                <th>Total Charged($)</th>
                <th>Serller</th>
                <th style="width: 160px">Info</th>
            </tr>
        </thead>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
 	$("#created_at").datepicker({});
    var table = $('#dataTable').DataTable({
         dom: "lBfrtip",
         order: [[1,'desc']],
            buttons: [
            ],
            processing: true,
            serverSide: true,
        ajax:{ url:"{{ route('seller-order-datatable') }}",

        data: function (d) {
            d.start_date = $("#start_date").val();
            d.end_date = $("#end_date").val();
            d.service_id = $("#service_id :selected").val();
            d.seller_id = $("#user_id :selected").val();
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
            { data: 'seller', name: 'seller' },
            { data: 'information', name: 'information'},
                  // { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
        ],
    });
    $("#search-button").click(function(){
        table.draw();
    });
    $("#reset").click(function(){
        $("#search-form")[0].reset();
        table.draw();
    });
});
</script>
@endpush
