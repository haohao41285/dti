@extends('layouts.app')
@section('title')
    My Tasks
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
    @include('task.search_task')
    <table class="table mt-4 table-hover table-sm" id="task-datatable" width="100%" cellspacing="0">
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
    function format ( d ) {
        // `d` is the original data object for the row
        return `<table class="border border-info table-striped table table-border bg-white">
            <tr class="bg-info text-white">
                <th scope="col">SubTask</th>
                <th scope="col">Subject</th>
                <th class="text-center">Priority</th>
                <th class="text-center">Status</th>
                <th class="text-center">Date Start</th>
                <th class="text-center">Date end</th>
                <th class="text-center">%Complete</th>
                <th class="text-center">Category</th>
                <th class="text-center">Assign To</th>
                <th class="text-center">Last Updated</th>
            </tr>`;
    }
    $(document).ready(function() {

        var table = $('#task-datatable').DataTable({
           // dom: "lBfrtip",
            responsive: false,
            order:[[9,'desc']],
           info: false,
           buttons: [
           ],
           processing: true,
           serverSide: true,
           ajax:{ url:"{{route('my-task-datatable')}}",
           data: function (d) {
               d.category = $("#category :selected").val();
               d.service_id = $("#service_id :selected").val();
               d.assign_to = $("#assign_to :selected").val();
               d.priority = $("#priority :selected").val();
               d.status = $("#status :selected").val();
                }
            },
           columns: [
                    { data: 'task', name: 'task',class:'text-center' },
                    { data: 'subject', name: 'subject',class:'text-center' },
                    { data: 'priority', name: 'priority',class:'text-center' },
                    { data: 'status', name: 'status',class:'text-center' },
                    { data: 'date_start', name: 'date_start',class:'text-center' },
                    { data: 'date_end', name: 'date_end',class:'text-center' },
                    { data: 'complete_percent', name: 'complete_percent',class: 'text-right' },
                    { data: 'category', name: 'category',class: 'text-center' },
                    { data: 'order_id', name: 'order_id',class: 'text-center' },
                    { data: 'updated_at', name: 'updated_at',class: 'text-center'},
            ],
        });
        $('#task-datatable tbody').on('click', '.details-control', function () {

            var task_id = $(this).attr('id');
            $(this).toggleClass('fa-plus-circle fa-minus-circle');
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }else{
                $.ajax({
                    url: '{{route('get-subtask')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        task_id: task_id,
                    },
                })
                    .done(function(data) {
                        data = JSON.parse(data);
                        var subtask_html = "";
                        $.each(data.data, function(index,val){

                            var complete_percent = "";
                            if(val.complete_percent == null)  complete_percent = "";
                            else complete_percent = val.complete_percent;

                            subtask_html += `
                                <tr>
                                    <td>`+val.task+`</td>
                                    <td>`+val.subject+`</td>
                                    <td>`+val.priority+`</td>
                                    <td>`+val.status+`</td>
                                    <td>`+val.date_start+`</td>
                                    <td>`+val.date_end+`</td>
                                    <td class="text-right">`+complete_percent+`</td>
                                    <td>`+val.category+`</td>
                                    <td>`+val.assign_to+`</td>
                                    <td>`+val.updated_at+`</td>
                                </tr> `;
                                });
                        row.child(format(row.data()) +subtask_html+"</table>" ).show();
                        tr.addClass('shown');
                    })
                    .fail(function() {
                        toastr.error('Get SubTask Failed!');
                    });
            }
        } );
        $("#search-button").click(function(){
            table.draw();
        });
        $("#formReset").click(function () {
            $(this).parents('form')[0].reset();
            table.draw();
        })
    });
</script>
@endpush
