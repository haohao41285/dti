@extends('layouts.app')
@section('content-title')
    WEB SERVICES
@endsection
@push('scripts')
@endpush
@section('content')
<div class="modal fade" id="add-order-modal">
    <div class="modal-dialog"  style="max-width:50%">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title" id="service-id"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form id="form-modal">
        <!-- Modal body -->
        <div class="modal-body">
            <div class="col-md-12 row">
                <div class="col-md-6">
                    <label for="customer_name">Customer Name</label>
                    <input type="text" required class="form-control form-control-sm" name="customer_name">
                </div>
                <div class="col-md-6">
                    <label for="cell_phone">Cell Phone</label>
                    <input type="text" required class="form-control form-control-sm" name="cell_phone">
                </div>
                <div class="col-md-6">
                    <label for="business_name">Business Name</label>
                    <input type="text" required class="form-control form-control-sm" name="business_name">
                </div>
                <div class="col-md-6">
                    <label for="business_phone">Business Phone</label>
                    <input type="text" required class="form-control form-control-sm" name="business_phone">
                </div>
                <div class="col-md-12">
                    <label for="note">Note</label>
                    <textarea name="note" id="" rows="5" class="form-control form-control-sm"></textarea>
                </div>
            </div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-sm btn-primary btn-submit" >Submit</button>
        </div>
        </form>
        
      </div>
    </div>
  </div>
<div class="card shadow mb-4 ">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: "{{ route('web_service.datatable') }}", },
        columns: [
            { data: 'web_service_type_id', name: 'web_service_type_id',class:'text-center' },
            { data: 'web_service_type_name', name: 'web_service_type_name' },
            { data: 'web_service_type_status', name: 'web_service_type_status',class:'text-center' },
            { data: 'action', name: 'action', orderable: false, searcheble: false, class: "text-center" }

        ],
        buttons: [{
                text: '<i class="fas fa-plus"></i> Add Service Type',
                className: 'btn btn-sm btn-primary add-service-type',
                action: function(e, dt, node, config) {
                    // document.location.href = "{{ route('addTheme') }}";
                }
            },
        ],
    });

    $(document).on('click', '.changeStatus', function(e) {
        e.preventDefault();
        let service_type = $(this).attr('service-type');
        let service_status = $(this).val();

        $.ajax({
            url: "{{route('web_service.change_status')}}",
            method: "get",
            dataType:'html',
            data: {
                service_type,service_status
            },
            success: function(data) {
                console.log(data);
                data = JSON.parse(data);
                if(data.status === 'error')
                    toastr.error(data.message);
                else
                    toastr.success(data.message);

                table.ajax.reload(null, false);
            },
            error: function() {
                toastr.error("Failed to change!");
            }
        })
    });
    $(document).on('click','.delete-service-type',function(){
        if(confirm('Do you want to delete this service type?')){
            let service_type = $(this).attr('web-service');
            $.ajax({
                type: "POST",
                dataType: "html",
                url: "{{route('web_service.delete')}}",
                data: {
                    _token: '{{csrf_token()}}',
                    service_type: service_type
                },
                success: function (response) {
                    response = JSON.parse(response);
                    if(response.status == 'error')
                        toastr.error(response.message);
                    else{
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    }
                }
            });
        }else
            return;
    });
    
});
</script>
@endpush