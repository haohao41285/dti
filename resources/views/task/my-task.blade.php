@extends('layouts.app')
@section('title')
    My Orders
@endsection
@section('content')
{{-- MODAL INPUT FORM --}}
<div class="modal fade" id="modal-input-form" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><b>Input Order Form</b></h6>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary">Submit</button>
        </div>
      </div>
      
    </div>
  </div>
{{-- END MODAL --}}
<div class="table-responsive">
	<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">MY TASK</h4>
    <table class="table mt-4 table-hover table-bordered" id="task-datatable" widtd="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr>
                <th>Task#</th>
                <th>Subject</th>
                <th class="text-center">Priority</th>
                <th class="text-center">Status</th>
                <th class="text-center">Date Start</th>
                <th class="text-center">Date end</th>
                <th class="text-center">%Complete</th>
                <th class="text-center">Category</th>
                <th class="text-center">Order#</th>
                <th class="text-center">Last Updated</th>
            </tr>
        </thead>
    </table>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#task-datatable').DataTable({
           // dom: "lBfrtip",
           order:[[0,'desc']],
           info: false,
           buttons: [
           ],  
           processing: true,
           serverSide: true,
           ajax:{ url:"{{route('my-task-datatable')}}",
           data: function (d) {
                } 
            },
           columns: [
                    { data: 'task', name: 'task',class:'text-center' },
                    { data: 'subject', name: 'subject',class:'text-center' },
                    { data: 'priority', name: 'priority',class:'text-center' },
                    { data: 'status', name: 'status',class:'text-center' },
                    { data: 'date_start', name: 'date_start',class:'text-center' },
                    { data: 'date_end', name: 'date_end',class:'text-center' },
                    { data: 'complete_percent', name: 'complete_percent',class: 'text-center' },
                    { data: 'category', name: 'category',class: 'text-center' },
                    { data: 'order_id', name: 'order_id',class: 'text-center' },
                    { data: 'updated_at', name: 'updated_at',class: 'text-center'},
            ],
        });
    });
</script>
@endpush