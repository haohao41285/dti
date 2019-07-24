@extends('layouts.app')
@section('content-title')
    Merchants Management
@endsection
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead>                
                <th>Shop Name</th>
                <th>Contact Name</th>
                <th>Phone Login</th>
                <th>License</th>
                <th>Domain</th>
                <th>Expired Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Laguna Spa & Nails Salon</td>
                <td>Jendy</td>
                <td class="text-center">19494582430</td>
                <td class="text-center">837ec5754f503cfaaee0929fd48974e7</td>
                <td>solarnailspagainesville.com</td>                           
                <td class="text-center">06-02-2018</td>
                <td class="text-center nowrap">
                    <a class="btn btn-sm btn-secondary" href="{{ route("editCustomer") }}"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-link"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
    $('#dataTableAllCustomer').DataTable({      
//       "ajax": {
//            "url": "data.json",
//            "data": function ( d ) {
//                d.status = $('#filterStatus').val();
//            }
//        }
    });
     var arrStatus = [
        {val : "", text: '-- Status -- '}, 
        {val : 1, text: 'Trial'},
        {val : 2, text: 'Purchasing'},
        {val : 3, text: 'Expired'}
      ];
    var statusFilter = $("<select id='filterStatus' class='custom-select custom-select-sm form-control form-control-sm'/>");
    $.each(arrStatus, function(i, item) {
       statusFilter.append($("<option>").attr('value',item.val).text(item.text));
    });    
    $('<label />').append(statusFilter).appendTo($("#dataTableAllCustomer_filter"));   
});
</script>
@endpush