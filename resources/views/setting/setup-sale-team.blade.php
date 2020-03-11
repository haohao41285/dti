@extends('layouts.app')
@section('content-title')
    SETUP SALE TEAM
@stop
@push('styles')
<style>
    .form-group>label{
        font-weight: 700;
    }
    td.day{
      position:relative;  
    }
    td.day.disabled{
      text-decoration: line-through;
    }

    td.day.disabled:hover:before {
        content: 'This time is closed';
        border: 1px red solid;
        border-radius: 11px;
        color: red;
        background-color: white;
        top: -22px;
        position: absolute;
        width: 136px;
        left: -34px;
        z-index: 1000;
        text-align: center;
        padding: 2px;
    }
</style>
@endpush
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
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active sellers" data-toggle="tab" href="#home">SELLERS</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link teams" data-toggle="tab" href="#menu1">TEAMS</a>
                </li>
            </ul>
            
            <div class="tab-content">
                <div id="home" class=" tab-pane active"><br>
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
                <div id="menu1" class="tab-pane fade"><br>
                    <table class="table table-sm table-bordered table-hover" id="dataTable_teams" width="100%" cellspacing="0">
                        <thead>
                            <tr class="thead-light">
                                <th class="text-center">ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Day of week</th>
                                <th>Other Days</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="card shadow mb-3 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary">Edit</h6>
            </div>
            <div class="card-body" id="edit-seller-content">
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
                        <button type="button" class="btn btn-sm btn-primary float-right submit-tt">Submit</button>
                    </div>
                </form>
            </div>
            <div class="card-body" id="edit-team-content" style="display:none">
                <form id="team-form">
                    <div class="form-group">
                        <label for="">Team</label>
                        <input type="text" class="form-control form-control-sm" disabled name="" id="team_name">
                    </div>
                    <div class="form-group">
                        <label for="">Sale Date</label>
                        <div class="row">
                            @foreach( dayOfWeek() as $day )
                                <div class="custom-control custom-checkbox mx-2">
                                    <input type="checkbox" class="custom-control-input" id="day_{{$day}}" value="{{$day}}" name="sale_date[]">
                                    <label class="custom-control-label" for="day_{{$day}}">{{$day}}</label>
                                </div>
                            @endforeach
                        </div>
                        
                    </div>
                    <label for=""><b>Other Date</b></label>
                    <div class="input-group mb-3 list-other-date">
                        <input type="text" class="form-control form-control-sm" id="other_date" name="other_date[]">
                        <div class="input-group-append">
                          <button class="btn btn-info btn-sm btn-add-other-date" type="button"><i class="fas fa-plus"></i></button>  
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-danger float-right cancel-tt ml-2">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary float-right submit-team" style="display:none">Submit</button>
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
var stt = 0;
var team_id = 0;
$(document).ready(function($) {

    $("#other_date").datepicker({
        todayHighlight: true,
        setDate: new Date(),
        startDate: new Date()
    });

    var old_team_type_name = "";
    var user_id = 0;

    dataTable = $("#dataTable").DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        buttons: [
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
    dataTable_teams = $("#dataTable_teams").DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        buttons: [
        ],
        ajax: { url: "{{route('setting.sale_team.datatable_teams')}}" },
        columns: [
            { data: 'id', name: 'id', class: 'text-center' },
            { data: 'team_name', name: 'team_name' },
            { data: 'team_type', name: 'team_type' },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'other_date', name: 'other_date', class: 'text-center' },
        ],
    })

    $('#dataTable tbody').on('click', 'tr', function() {
        clearView();

        $("#user_target_sale").val(dataTable.row(this).data()['user_target_sale']);
        $("#user_phone_call").val(dataTable.row(this).data()['user_phone_call']);
        $("#fullname").val(dataTable.row(this).data()['fullname']);
        user_id = dataTable.row(this).data()['user_id'];
    
    });
    $('#dataTable_teams tbody').on('click', 'tr', function() {
        clearView();

        $("#team_name").val(dataTable_teams.row(this).data()['team_name']);
        $('.submit-team').css('display','');
        var sale_date = dataTable_teams.row(this).data()['sale_date'];
        var other_date = dataTable_teams.row(this).data()['other_date'];

        if(sale_date != null){
            let sale_date_arr = sale_date.split(';');
            $(sale_date_arr).each(function (index, element) {
                $("#day_"+element).prop('checked',true);
            });
        }
        if(other_date != null){
            let other_date_arr = other_date.split(';');
            $(other_date_arr).each(function (index, element) {

                $('.btn-add-other-date').parent().parent().after(`
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm" value="`+element+`" id="other_date_`+stt+`" name="other_date[]">
                        <div class="input-group-append">
                        <button class="btn btn-info btn-sm btn-delete-other-date" type="button"><i class="fas fa-trash text-danger"></i></button>  
                    </div>
                </div>
                `);$("#other_date_"+stt).datepicker({
                    todayHighlight: true,
                    setDate: new Date(),
                    startDate: new Date()
                });
                stt++;
            });
        }

        team_id = dataTable_teams.row(this).data()['id'];

    });
    $(document).on('click', '.submit-tt', function() {

        let formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');
        formData.append('user_id',user_id);
        
        if(user_id > 0){
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
        }
    });
    $(".cancel-tt").click(function() {
        clearView();
    })

    function clearView() {
        $("#seller-form")[0].reset();
        $("#team-form")[0].reset();
        $('input[type=checkbox]').prop('checked',false);
        $(".btn-delete-other-date").parent().parent().remove();
        id = 0;
        team_id = 0;
        stt = 0;
    }
    $(".btn-add-other-date").click(function(){
        $(this).parent().parent().after(`
        
        <div class="input-group mb-3">
            <input type="text" class="form-control form-control-sm" id="other_date_`+stt+`" name="other_date[]">
                <div class="input-group-append">
                <button class="btn btn-info btn-sm btn-delete-other-date" type="button"><i class="fas fa-trash text-danger"></i></button>  
            </div>
        </div>
        `);
        $("#other_date_"+stt).datepicker({
            todayHighlight: true,
            setDate: new Date(),
            startDate: new Date()
        });
        stt++;
    });
    $(document).on('click',".btn-delete-other-date",function(){
        $(this).parent().parent().remove();
    })
    function ableBox(id_box){
        $("#"+id_box).css('display','');
    }
    function disableBox(id_box){
        $("#"+id_box).css('display','none');
    }
    $(".sellers").click(function(){
        ableBox('edit-seller-content');
        disableBox('edit-team-content');
    });
    $(".teams").click(function(){
        ableBox('edit-team-content');
        disableBox('edit-seller-content');
    });
    $(".submit-team").click(function(){
        let formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');
        formData.append('team_id',team_id);
        if(team_id != 0){
            $.ajax({
                type: "POST",
                url: "{{ route('setting.sale_team.save_team') }}",
                data: formData,
                dataType: "html",
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    data = JSON.parse(data);
                    if(data.status == 'error')
                        toastr.error(data.message);
                    else{
                        toastr.success(data.message);
                        dataTable_teams.ajax.reload(null,false);
                        clearView();
                    }
                },
                error: function(){
                    toastr.error('Failed!');
                }
            });

        }
       
    })
    
});

</script>
@endpush
