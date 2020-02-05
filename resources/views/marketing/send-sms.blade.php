@extends('layouts.app')
@section('title','Send SMS')
@push('styles')
{{--
<link href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" type="text/css" /> --}}
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-4 ">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Send SMS</h6>
            </div>
            <div class="card-body">
                <form action="{{route('post-send-sms')}}" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label form-control-label">Title name</label>
                                <div class="col-lg-10">
                                    <input class="late form-control" required name="sms_send_event_title" type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label form-control-label">SMS Template</label>
                                <div class="col-lg-10">
                                    <select required="" class="selectpicker form-control form-control-sm" id="sms_send_event_template_id" name="sms_send_event_template_id" data-show-subtext="true" data-live-search="true">
                                        <option value="">--Select SMS Template--</option>
                                        @foreach($sms_content_template_list as $sms_content)
                                        <option value="{{$sms_content->id}}">{{$sms_content->template_title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label form-control-label"></label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" readonly="readonly" id="sms_message" rows="4" cols="50"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label form-control-label">Start date</label>
                                <div class="col-lg-4">
                                    <input required="" id="date" style="border: 1px solid #d1d3e2;" class="late form-control pl-2" value="{{\Carbon\Carbon::now()->format('m/d/Y')}}" type="text" name="sms_send_event_start_day" placeholder="To" />
                                </div>
                                <label class="col-lg-2 col-form-label form-control-label">Time send</label>
                                <div class="col-lg-4">
                                    <input id="timepicker" required name="sms_send_event_start_time" value="" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label form-control-label">List Phone</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" readonly="readonly" id="list_phone" name="list_phone" rows="4" cols="50"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Phone</th>
                                        <th>Name</th>
                                        {{-- <th>Birthday</th> --}}
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    {{-- <div class="form-group row">
                        <label class="col-lg-2 col-form-label form-control-label">Receiver list</label>
                        <div class="col-lg-6">
                            <div class="custom-file">
                                <input type="file" name="upload_list_receiver" style="border: 1px solid #d1d3e2;" required class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="form-group row">
                        <label class="col-lg-2 col-form-label form-control-label"></label>
                        <div class="note "><a href="{{route('download-template-file')}}">Download template file</a></div>
                    </div> --}}
                    <div class="form-group row">
                        <label class="col-lg-1 col-form-label form-control-label"></label>
                        <div class="col-lg-9">
                            <a href="" class="btn btn-danger">Cancel</a>
                            <input type="submit" class="btn btn-primary" value="Send" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        // dom: "lBfrtip",
        processing: true,
        // serverSide: true,
        buttons: [

        ],
        ajax: {
            url: "{{ route('marketing.customer.datatable') }}",
            data: function(d) {}
        },
        'columnDefs': [{
            'targets': 0,
            'checkboxes': {
                'selectRow': true
            }
        }],
        'select': {
            'style': 'multi'
        },
        columns: [

            { data: 'ct_cell_phone', name: 'ct_cell_phone', class: 'text-center phone' },
            { data: 'id', name: 'id', class: 'text-center' },
            { data: 'ct_cell_phone', name: 'ct_cell_phone', class: 'text-center phone' },
            { data: 'ct_fullname', name: 'ct_fullname' },
            // { data: 'updated_at', name: 'updated_at', class: 'text-center' },
            // { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
        ],
    });

    $(document).on('click', '#dataTable tbody tr', function(e) {
        var check = table.column(0).checkboxes.selected();
        var phone = '';
        $.each(check, function(index, value) {
            phone += value + ',';
        });
        $("#list_phone").val(phone);
    });

    $(document).change('input[type="checkbox"]', function(e) {
        var check = table.column(0).checkboxes.selected();
        var phone = '';
        $.each(check, function(index, value) {
            phone += value + ',';
        });
        $("#list_phone").val(phone);
    });

});
</script>
{{-- <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script> --}}
{{-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script> --}}
<script>
$("#date").datepicker({
    todayHighlight: true,
    setDate: new Date(),
});
$('#timepicker').timepicker({
    uiLibrary: 'bootstrap4'
});


// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
$("#sms_send_event_template_id").change(function() {
    var id = $("#sms_send_event_template_id option:selected").val();
    $.ajax({
            url: '{{route("get-content-template")}}',
            type: 'GET',
            dataType: 'html',
            data: { id: id },
        })
        .done(function(data) {
            $("#sms_message").text(data);
        })
        .fail(function() {
            console.log("error");
        });

});
</script>
@endpush