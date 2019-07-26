@extends('layouts.app')
@section('content-title')
    License Management
@endsection
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>           
                <th>License Code</th>
                <th>User Sale</th>
                <th>Place Store</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>5a0d3a90f102d1.23209932</td>
                <td>(nick.name)Full Name</td>
                <td><a href="#">Place Store 1</a></td>
                <td class="text-center"><input type="checkbox" class="js-switch" checked="checked" /></td>     
                <td class="text-center">11/11/2019 11:20 AM</td>
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
                  text: '<i class="fas fa-plus"></i> Generate Licenses',
                  className: 'btn btn-sm btn-primary',
                  action: function ( e, dt, node, config ) {
                     document.location.href = "{{ route('generateLicenses') }}";
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