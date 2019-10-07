@extends('layouts.app')
@section('content-title')
Recent Logs
@endsection
@section('content')
<div class="table-responsive">
    <table class="table table-bordered" id="log-datatable" width="100%" cellspacing="0">
        <thead>                
        <th>UID</th>
        <th>USERNAME</th>
        <th>EVENT TYPE</th>
        <th>LOG MESSAGES</th>
        <th>HOSTNAME</th>
        <th>TIMESTAMP</th>            
        </tr>
        </thead>
        {{-- <tbody>
            <tr role="row">
                <td class=" alignCenter" tabindex="0">2821</td>
                <td class=" alignCenter">linhhoang</td>
                <td class=" alignCenter">LOGIN</td>
                <td>linhhoang is logged in</td>
                <td class=" alignCenter">::1</td>
                <td class="alignCenter sorting_1">24/07/2019 10:13:38 AM</td>
            </tr>
        </tbody> --}}
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
            table = $('#log-datatable').DataTable({
             // dom: "lBfrtip",
             buttons: [
              {
                  text: '<i class="fas fa-trash"></i> Clear All Logs',
                  className: 'btn btn-sm btn-primary',
                  action: function ( e, dt, node, config ) {
                     // document.location.href = "";
                  }
              },
              { text : '<i class="fas fa-download"></i> Export',
                extend: 'csvHtml5', 
                className: 'btn btn-sm btn-primary' 
              }
          ],  
             processing: true,
             serverSide: true,
             ajax:{ url:"{{ route('recentlogDatatable') }}",
             data: function (d) {

                  } 
              },
             columns: [

                       { data: 'id', name: 'id' },
                       { data: 'user_id', name: 'user_id' },
                       { data: 'type', name: 'type' },
                       { data: 'message', name: 'message' },
                       { data: 'ip_address', name: 'ip_address' },
                       { data: 'created_at', name: 'created_at' },
              ],       
           }); 
      
    });
</script>
@endpush