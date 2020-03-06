@extends('layouts.app')
@section('content-title')
    SETUP SALE TEAM
@stop
@section('content')
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
                <h6 class="m-0 font-weight-bold text-primary">Add Team Type</h6>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="">Target</label>
                        <input type="text" class="form-control form-control-sm" name="user_target_sale" id="user_target_sale">
                    </div>
                    <div class="form-group">
                        <label for="">Phone</label>
                        <input type="text" class="form-control form-control-sm" name="use_phone_call" id="use_phone_call">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-danger float-right cancel-tt ml-2">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary float-right submit-tt">Submit</button>
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
        buttons: [],
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
        $(".tt-tip").text("Edit Seller");
        id = dataTable.row(this).data()['id'];
        user_id = dataTable.row(this).data()['user_id'];

    });
    $(document).on('click', '.submit-tt', function() {

        let formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');
        $.ajax({
            url: '{{route('setting.sale_team.save')}}',
            type: 'GET',
            dataType: 'html',
            processing: false,
            cacheData: fale,
            data:formData,
        })
        .done(function(data) {
            data = JSON.parse(data);
            if (data.status === 'error') {
                if(typeof(data.message) === 'string' )
                    toastr.error(data.message);
                else{
                    $.each(data.message,function (ind,val) {
                        toastr.error(val);
                    });
                }
            } else {
                clearView();
                dataTable.draw();
            }
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            toastr.error('Error!');
            // console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        });
    });
    $(".cancel-tt").click(function() {
        clearView();
    })

    function clearView() {
        $(".tt-tip").text("Add Team Type");
        $("#team_type_description").val("");
        $("#team_type_name").val("");
        id = 0;
    }
    $(document).on("click", ".delete-tt", function() {
        
        if (confirm("Do you want to delete this team type?")) {

            var tt_id = $(this).attr('tt_id');

            $.ajax({
                    url: '{{route('delete-team-type')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: { 
                        tt_id: tt_id,
                        old_team_type_name: old_team_type_name
                    },
                })
                .done(function(data) {
                    data = JSON.parse(data);
                    if (data.status == 'error')
                        toastr.error(data.message);
                    else {
                        toastr.success(data.message);
                        dataTable.draw();
                        clearView();
                    }
                })
                .fail(function() {
                    console.log("error");
                });

        }
    })
});

</script>
@endpush
