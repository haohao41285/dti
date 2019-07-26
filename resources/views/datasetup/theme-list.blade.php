@extends('layouts.app')
@section('content-title')
    Theme Management
@endsection
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>           
                <th>Theme Name</th>
                <th>Theme Code</th>
                <th>Price($)</th>
                <th>Image</th> 
                <th>Status</th>
                <th>Last Update</th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Theme 10</td>
                <td>demo10</td>
                <td class="text-right">$100</td>               
                <td class="text-center"><img src="{{asset("images/no-image.png")}}" width="100px" height="100px"/></td>                                
                <td class="text-center"><input type="checkbox" class="js-switch" checked="checked" /></td> 
                <td>20/11/2019 10:11 AM by admin</td>
                <td class="text-center nowrap">
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-link"></i> DEMO</a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-edit"></i></a>
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
                  text: '<i class="fas fa-plus"></i> Add Theme',
                  className: 'btn btn-sm btn-primary',
                  action: function ( e, dt, node, config ) {
                     document.location.href = "{{ route('addTheme') }}";
                  }
              },
              { text : '<i class="fas fa-download"></i> Export',
                extend: 'csvHtml5', 
                className: 'btn btn-sm btn-primary' 
              }
          ]
    });
});
</script>
@endpush