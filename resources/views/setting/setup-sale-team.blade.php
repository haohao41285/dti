@extends('layouts.app')
@section('content-title')
    SETUP SALE TEAM
@stop
@section('content')

{{-- MODAL FOR SETUP calendar --}}
<div class="modal fade" id="calendar-modal">
    <div class="modal-dialog modal-lg" style="max-width:90%">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Modal Heading</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
            <div class="calendar-box"></div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
<div class="col-12 row">
    <div class="col-md-8">
        <div class="card shadow mb-3 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary">SELLERS</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="thead-light">
                            <th class="text-center">ID</th>
                            <th>Fullname</th>
                            <th>Nickname</th>
                            <th>Target</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="card shadow mb-3 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary">Edit Seller</h6>
            </div>
            <div class="card-body">
                <form id="seller-form">
                    <div class="form-group">
                        <label for="">User</label>
                        <input type="text" class="form-control form-control-sm" disabled name="" id="fullname">
                    </div>
                    <div class="form-group">
                        <label for="">Target(per day)</label>
                        <input type="text" class="form-control form-control-sm" onkeypress="return isNumberKey(event)" name="user_target_sale" id="user_target_sale">
                    </div>
                    <div class="form-group">
                        <label for="">Phone</label>
                        <input type="text" class="form-control form-control-sm" name="user_phone_call" id="user_phone_call">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-danger float-right cancel-tt ml-2">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary float-right submit-tt" style="display:none">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@push('scripts')
<script type="text/javascript">
//DEFINE VAR
var id = 0;
$(document).ready(function($) {

    var old_team_type_name = "";
    var user_id = 0;

    dataTable = $("#dataTable").DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        buttons: [
            {
                text: '<i class="fas fa-calendar"></i> Calendar',
                className: "btn-calendar btn btn-sm btn-info",
            }
        ],
        ajax: { url: "{{route('setting.sale_team.datatable')}}" },
        columns: [
            { data: 'user_id', name: 'user_id', class: 'text-center' },
            { data: 'fullname', name: 'fullname' },
            { data: 'user_nickname', name: 'user_nickname' },
            { data: 'user_target_sale', name: 'user_target_sale', class: 'text-center' },
            { data: 'user_phone_call', name: 'user_phone_call', class: 'text-center' },
        ],
    })

    $('#dataTable tbody').on('click', 'tr', function() {

        $("#user_target_sale").val(dataTable.row(this).data()['user_target_sale']);
        $("#user_phone_call").val(dataTable.row(this).data()['user_phone_call']);
        $("#fullname").val(dataTable.row(this).data()['fullname']);
        $(".tt-tip").text("Edit Seller");
        user_id = dataTable.row(this).data()['user_id'];
        $('.submit-tt').css('display','');
    
    });
    $(document).on('click', '.submit-tt', function() {

        let formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');
        formData.append('user_id',user_id);
        $.ajax({
            url: '{{route('setting.sale_team.save')}}',
            type: 'POST',
            dataType: 'html',
            processData: false,
            contentType: false,
            data:formData,
        })
        .done(function(data) {
            data = JSON.parse(data);
            if (data.status === 'error')
                toastr.error(data.message);
            else {
                clearView();
                dataTable.ajax.reload( null, false )
                toastr.success(data.message);
            }
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            toastr.error('Error!');
        });
    });
    $(".cancel-tt").click(function() {
        clearView();
    })

    function clearView() {
        $("#seller-form")[0].reset();
        $(".submit-tt").css('display','none');
    }
    $(document).on('click','.btn-calendar',function(){
        $.ajax({
            type: 'GET' ,
            url: "{{route('setting.sale_team.calendar')}}",
            data: "data",
            dataType: "html",
            success: function (data) {
                console.log(data);
            },
            
        });
        $("#calendar-modal").modal('show');
    });
});

</script>
@endpush
