@extends('layouts.app')
@section('content-title')
    My Customers
@endsection
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead>                
                <th>Full Name</th>
                <th>Nail Shop</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
                <th>Date Expired</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>                
                <td>Jendy</td>
                <td>Laguna Spa & Nails Salon</td>
                <td class="text-center">19494582430</td>
                <td>dsds@gmail.com</td>
                <td class="text-center"><span class="text-gray-500">Expired</span></td>                
                <td class="text-center"><span class="text-gray-500">06-02-2019</span></td>
                <td class="text-center">06-02-2018</td>
                <td class="text-center">
                    <a class="btn" href="#"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
        </tbody>
           <tr>                
                <td>Jendy</td>
                <td>Laguna Spa & Nails Salon</td>
                <td class="text-center">19494582430</td>
                <td>dsds@gmail.com</td>
                <td class="text-center">Trial</td>                
                <td class="text-center">06-12-2019</td>
                <td class="text-center">06-02-2018</td>
                <td class="text-center">
                    <a class="btn" href="#"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
         <tr>                
                <td>Jendy</td>
                <td>Laguna Spa & Nails Salon</td>
                <td class="text-center">19494582430</td>
                <td>dsds@gmail.com</td>
                <td class="text-center">Purchasing</td>                
                <td class="text-center">06-12-2019</td>
                <td class="text-center">06-02-2018</td>
                <td class="text-center">
                    <a class="btn" href="#"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
    $('#dataTableAllCustomer').DataTable({
        "language": {
            "search" : "",
            "searchPlaceholder": "Search",                
        }
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