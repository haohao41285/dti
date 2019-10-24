@extends('layouts.app')
@section('content-title')
    Notifications List
@endsection
@push('styles')
    <style>
        .note-popover.popover {
            display: none;
        }
    </style>
@endpush
@section('content')
    <div class="">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#home">Receive</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu1" id="sent-list">Sent</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu2">Create</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div id="home" class=" tab-pane active"><br>
                <table class="table table-hover" id="notification-receive" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th></th>
                            <th style="width:60%">CONTENT</th>
                            <th>CREATED AT</th>
                            <th>STATUS</th>
                            <th hidden></th>
                            <th hidden></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div id="menu1" class="tab-pane fade"><br>
                <table class="table table-hover" id="notification-sent" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>ID</th>
                            <th style="width:60%">CONTENT</th>
                            <th>CREATED AT</th>
                            <th>SEND TO</th>
                            <th hidden></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div id="menu2" class="tab-pane fade"><br>
                <form id="form-send-notification">
                    <label for=""><b>Content:</label></b><br>
                    <div class="ml-5">
                        <textarea name="content" id="summernote" class="form-control form-control-sm" rows="20" ></textarea>
                    </div>

                    <label for=""><b>Send To:</b></label><br>
                        <div class="custom-control custom-checkbox mr-sm-2 ml-5">
                            <input type="checkbox" name="receiver_id[]" class="custom-control-input" value="all" id="all-staff">
                            <label class="custom-control-label" for="all-staff">All Staff</label>
                        </div>
                        @foreach($teams as $team)
                        <div class="custom-control custom-checkbox mr-sm-2 ml-5">
                            <input type="checkbox" name="receiver_id[]" class="custom-control-input" value="{{$team->id}}" id="{{$team->id}}">
                            <label class="custom-control-label" for="{{$team->id}}">{{$team->team_name}}</label>
                        </div>
                        @endforeach

                    <button class="btn btn-sm btn-primary" type="button" id="send-notification">Send</button>
                    <button class="btn btn-sm btn-danger" type="button" id="resetForm">Cancel</button>
                </form>
            </div>
        </div>

    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function (){
            $("#summernote").summernote({
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ]
            });

            var notification_arr = [];

            var table_receive = $('#notification-receive').DataTable({
                // dom: "lBfrtip",
                // paging: false,
                // info:false,
                // searching: false,
                order:[[5,'desc']],
                select: {
                    style:    '',
                    selector: 'td:first-child'
                },
                buttons: [
                    {
                        text: '<i class="fas fa-envelope-open"></i> Mark for read',
                        className: 'btn btn-sm btn-primary mark-read',
                    },
                ],
                // processing: true,
                serverSide: true,
                ajax:{ url:"{{ route('notification-receive-datatable') }}",
                    data: function (d) {
                    }
                },
                columns: [
                    { data: 'check', name: 'check',class:'text-center',class:'select-checkbox',orderable:false,targets: 0},
                    { data: 'content', name: 'content', },
                    { data: 'created_at', name: 'created_at',class:'text-center' },
                    { data: 'status', name: 'status',class:'text-center' },
                    { data: 'href_to', name: 'href_to',class:'d-none' },
                    { data: 'id', name: 'id',class:'d-none' },
                ],
            });
            var table_sent = $('#notification-sent').DataTable({
                // dom: "lBfrtip",
                // paging: false,
                // info:false,
                // searching: false,
                order:[[0,'desc']],
                buttons: [],

                processing: true,
                serverSide: true,
                ajax:{ url:"{{ route('notification-sent-datatable') }}",
                    data: function (d) {
                    }
                },
                columns: [
                    { data: 'id', name: 'id',class:'text-center',},
                    { data: 'content', name: 'content', },
                    { data: 'created_at', name: 'created_at',class:'text-center' },
                    { data: 'sent_to', name: 'sent_to',class:'text-center' },
                    { data: 'href_to', name: 'href_to',class:'d-none' },
                ],
            });
            $("#notification-receive tbody").on('click','tr td:nth-of-type(2),tr td:nth-of-type(3)',function(){
                var href = table_receive.rows(this).data()[0]['href_to'];
                var notification_id = table_receive.rows(this).data()[0]['id'];
                notification_arr.push(notification_id);
                markRead();
                window.location.href = href;
            });
            $("#notification-sent tbody").on('click','tr',function(){
                var href = table_sent.rows(this).data()[0]['href_to'];
                window.location.href = href;
            });
            $("#notification-receive tbody").on('click','tr td:nth-of-type(1)',function(){

                var notification_id = table_receive.rows(this).data()[0]['id'];

                if($.inArray(notification_id,notification_arr) !== -1){
                    notification_arr = $.grep(notification_arr, function(value) {
                        return value != notification_id;
                    });
                }else{
                    notification_arr.push(notification_id);
                    }
            });
            $(document).on('click','.mark-read',function () {
                if(notification_arr.length == 0){
                    toastr.error('Select Notification');
                    return;
                }
                markRead();
            });
            function markRead(){
                $.ajax({
                    url: '{{route('notification-mark-read')}}',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        notification_arr: notification_arr,
                        _token:'{{csrf_token()}}'
                    },
                })
                    .done(function(data) {
                        console.log(data);
                        data = JSON.parse(data);
                        if(data.status == 'success'){
                            toastr.success(data.message);
                            $("#number").text(data.notification_count);
                        }
                        else
                            toastr.error(data.message);

                        notification_arr = [];
                        table_receive.draw();
                    })
                    .fail(function() {
                        console.log("error");
                    });
            }
            $("#send-notification").click(function () {

                var formData = new FormData($(this).parents('form')[0]);
                formData.append('_token','{{csrf_token()}}');

                $.ajax({
                    url: '<?php echo e(route('send-notification')); ?>',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    async: true,
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();
                        return myXhr;
                    },
                    success: function (data) {
                        // console.log(data);
                        // return;
                        let message = "";
                        if(data.status == 'success'){
                            toastr.success(data.message);
                            table_sent.draw();
                            clearView();
                            $("#sent-list").click();
                        }else{
                            if($.type(data.message) == 'string'){
                                toastr.error(data.message);
                            }else{
                                $.each(data.message, function(index, val) {
                                    toastr.error(val);
                                });
                            }
                        }
                    },
                    fail: function() {
                        console.log("error");
                    }
                });
            });
            function clearView() {
                $("#form-send-notification")[0].reset();
            }
        });
    </script>

@endpush

