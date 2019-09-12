@extends('layouts.app')
@section('content-title')
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
        <tbody>
            <tr>
                <td>1</td>
                <td>06-06-2019</td>
                <td class="text-center">abc customer</td>
                <td class="text-center">123 Services</td>
                <td>1</td>                           
                <td>1</td>                           
                <td>1</td>                           
                <td class="text-center">06-02-2018</td>
                <td class="text-center">06-02-2018</td>
                {{-- <td class="text-center nowrap">
                    <a class="btn btn-sm btn-secondary" href="{{ route("editCustomer") }}"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-link"></i></a>
                </td> --}}
            </tr>
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
 	$("#created_at").datepicker({});
    $('#dataTableAllCustomer').DataTable({      
//       "ajax": {
//            "url": "data.json",
//            "data": function ( d ) {
//                d.status = $('#filterStatus').val();
//            }
//        }
    }); 
});
</script>
@endpush