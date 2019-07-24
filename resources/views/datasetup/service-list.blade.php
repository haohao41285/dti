@extends('layouts.app')
@section('content-title')
    Services Management
@endsection
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>           
                <th>Name</th>
                <th>Parent Service</th>
                <th>Price($)</th>
                <th>Description</th>
                <th width="80">Status</th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Bacsic Packet</td>
                <td></td>
                <td class="text-right">69</td>
                <td>Payroll</td>              
                <td class="text-center"><input type="checkbox" class="js-switch" checked="checked" /></td>
                <td class="text-center nowrap">
                    <a class="btn btn-sm btn-secondary" href="datasetup/service-edit.php"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
    var table = $('#dataTable').DataTable({
         buttons: [
              {
                  text: 'Add Service',
                  className: 'btn btn-sm btn-primary',
                  action: function ( e, dt, node, config ) {
                     document.location.href = "service-add.php";
                  }
              },
              { extend: 'csvHtml5', className: 'btn btn-sm btn-primary' },
              { extend: 'pdfHtml5', className: 'btn btn-sm btn-primary'}
          ]
    });
   
});
</script>
@endpush