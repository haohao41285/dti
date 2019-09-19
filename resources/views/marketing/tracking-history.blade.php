@extends('layouts.app')
@section('title','Sms History')
@push('styles')
@endpush
@section('content')
{{-- MODAL SMS DETAIL --}}
<div class="modal fade" id="list-sms-detail" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header text-center ">
          <h5>Receive List</h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-2">Total: <span style="color: red" id="total"></span></div>
            <div class="col-md-2">Send: <span style="color: red" id="success"></span> </div>
            <div class="col-md-2">Fail: <span style="color: red" id="fail"></span></div>
          </div>
          <table id="datatable_receive" width="100%" class="table table-bordered table-hover">
              <thead>
                  <tr>
                      <th>Phone</th>
                      <th>Date & Time</th>
                      <th>Content</th>
                  </tr>
              </thead>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm close-table-detail" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
{{-- END MODAL --}}
<div class="row">
   <div class="card shadow mb-4 col-lg-12">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Id</th>
              <th>Template Title</th>
              <th>SMS Content Template</th>
              <th>Created at</th>
              <th style="width: 80px">Action</th>
            </tr>
          </thead>  
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    //DEFINE
    var event_id = 0;

    table = $("#dataTable").DataTable({
      processing:true,
      serverSide:true,
      buttons: [
      ],
      ajax:{
        url:" {{ route('tracking-history-datatable') }}",
      },
      columns:[
        {data:'id',name: 'id', class: 'text-center'},
        {data:'sms_send_event_title',name:'sms_send_event_title'},
        {data:'sms_content_template',name:'sms_content_template'},
        {data:'created_by',name:'created_by',class:'text-center'},
        {data:'action',name:'action',orderable: false, searcheble: false, class: 'text-center'},        
      ]
    });
    $(document).on('click','.view-sms',function(){

      event_id = $(this).attr('event_id');

      $.ajax({
        url: '{{route('calculate-sms')}}',
        type: 'get',
        dataType: 'html',
        data: {event_id: event_id},
      })
      .done(function(data) {
        data = JSON.parse(data);
        if(data.status == 'error')
          toastr.error(data.message);
        else{
          $("#total").text(data.calculate.total);
          $("#success").text(data.calculate.success);
          $("#fail").text(data.calculate.fail);
          $('#list-sms-detail').modal('show');
          receiveTable.draw();
        }
      })
      .fail(function() {
        toastr.error('Error!');
      });
    });

    //LIST RECEIVER
     receiveTable = $('#datatable_receive').DataTable({
         dom: "ftip",
         processing: true,
         serverSide: true,
         ajax:{ url:"{{ route('event-detail')}}",
              data:function(d){
                d.event_id = event_id;
              }
            },
         columns: [
                  { data: 'phone', name: 'phone',class:'text-center' },
                  { data: 'date_time', name: 'date_time', class:'text-left' },
                  { data: 'content', name: 'content',class: 'text-left' }
               ]    
    });
  });
</script>
@endpush
