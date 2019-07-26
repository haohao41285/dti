@extends('layouts.app')
@section('content-title')
    Combo/Package Management
@stop
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>                
                <th>ID</th>
                <th>Name</th>
                <th>Price($)</th>
                <th width="80">Type</th>
                <th width="80">Status</th>                
                <th>Last Update</th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a class="details-control" href="#" title="click here to view detail">123</a></td>
                <td>Basic web</td>
                <td class="text-right">69</td>
                <td class="text-center">SMS</td>    
                <td class="text-center"><input type="checkbox" class="js-switch" checked="checked" /></td>
                <td>20/11/2019 10:11 AM by admin</td>
                <td class="text-center nowrap">
                    <a class="btn btn-sm btn-secondary" href="{{ route('editCombo',['id' =>1]) }}"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <tr>
                <td><a class="details-control" href="#" title="click here to view detail">123</a></td>
                <td>Basic packet</td>
                <td class="text-right">69</td>
                <td class="text-center">Web</td>    
                <td class="text-center"><input type="checkbox" class="js-switch" checked="checked" /></td>
                <td>20/11/2019 10:11 AM by admin</td>
                <td class="text-center nowrap">
                    <a class="btn btn-sm btn-secondary" href="{{ route('editCombo',['id' =>1]) }}"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
function format ( d ) {
    // `d` is the original data object for the row    
    return '<table class="table table-borderless bg-white">'+
        '<tr>'+
            '<th scope="col">Name</th>'+
            '<th scope="col">Price</th>'+
            '<th scope="col">Description</th>'+
        '</tr>'+
        '<tr>'+
            '<td>Full System POS</td>'+
            '<td>$12</td>'+
            '<td>test</td>'+
        '</tr>'+
       '<tr>'+
            '<td>Full System POS</td>'+
            '<td>$12</td>'+
            '<td>test</td>'+
        '</tr>'+
    '</table>';
}    
 $(document).ready(function() {     
    var table = $('#dataTable').DataTable({
         buttons: [
              {
                  text: '<i class="fas fa-plus"></i> Add Combo',
                  className: 'btn btn-sm btn-primary',
                  action: function ( e, dt, node, config ) {
                     document.location.href = "{{ route('addCombo') }}";
                  }
              },
              { text : '<i class="fas fa-download"></i> Export',
                extend: 'csvHtml5', 
                className: 'btn btn-sm btn-primary' 
              }
          ]  
    });
   $('#dataTable tbody').on('click', '.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
});
</script>
@endpush
