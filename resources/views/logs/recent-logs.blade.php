@extends('layouts.app')
@section('content-title')

@endsection
@section('content')
<div class="col-12 ">
    <div class="card shadow mb-4 ">
        <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-primary">Recent logs</h6>
        </div>
        <div class="card-body">
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
</div>
</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
            table = $('#log-datatable').DataTable({
             "order": [[ 5, "desc" ]],
             buttons: [
             
          ],  
             processing: true,
             serverSide: true,
             ajax:{ url:"{{ route('recentlogDatatable') }}",
             data: function (d) {

                  } 
              },
             columns: [

                       { data: 'id', name: 'id' },
                       { data: 'user_nickname', name: 'user_nickname' },
                       { data: 'type', name: 'type' },
                       { data: 'message', name: 'message' },
                       { data: 'ip_address', name: 'ip_address' },
                       { data: 'created_at', name: 'created_at' },
              ],       
           }); 
      
    });
</script>
@endpush