@extends('layouts.app')
@section('title')
    Detail Task
@endsection
@push('styles')
<style type="text/css" media="screen">
	.file-comment{
    max-width: 100px;
    max-height: 100px;
   }
   .note-popover.popover {
    display: none;
   }
</style>
@endpush
@section('content')
{{-- MODAL SEND MAIL --}}
<div class="modal fade" id="form-notification" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><b>Send Email & Notification to CSR</b></h6>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="" method="get" accept-charset="utf-8">
            <div class="modal-body">
                    <label for="subject" class="required">Subject</label>
                    <input type="text" name="subject" id="subject" required class="form-control form-control-sm">
                    <label for="message" class="required">Message</label>
                    <textarea name="message" id="message" rows="3" class="form-control form-control-sm"></textarea>
                    <label for="team">CSR TECHNICAL TEAM</label>
                    <select name="team" id="team" class="form-control form-control-sm">
                        @foreach($team as $t)
                        <option value="{{$t->id}}">{{$t->team_name}} - {{$t->team_email}}</option>
                        @endforeach
                    </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger btn-sm cancel" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-sm btn-primary submit">Submit</button>
            </div>
        </form>
      </div>
    </div>
</div>
{{-- END MODAL --}}
<div class="table-responsive">
	<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info text-uppercase">TASK #{{$id}} - {{$task_info->subject}}</h4>
    <table class="table table-striped mt-4 table-bordered" id="dataTableAllCustomer" widtd="100%" cellspacing="0">
        <tbody>
            <tr>
                <td>CATEGORY</td>
                <td>PRIORITY</td>
                <td>TASK STATUS</td>
                <td>CREATED DATE</td>
                <td>CREATED BY</td>
                <td>ASSIGN TO</td>
                <td class="float-right">
                    @if($task_info->task_parent_id == "")
                    <a href="{{route('task-add',$id)}}" title=""><button class="btn btn-sm btn-info"><i class="fas fa-plus">ADD SUBTASK</i></button></a>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{$task_info->category}}</th>
                <th class="text-info">{{\App\Helpers\GeneralHelper::getPriorityTask()[$task_info->priority]}}</th>
                <th class="text-info">{{\App\Helpers\GeneralHelper::getStatusTask()[$task_info->status]}}</th>
                <th>{{format_datetime($task_info->created_at)}}</th>
                <th>{{$task_info->getCreatedBy->user_nickname}}<span class="text-capitalize">({{$task_info->getCreatedBy->user_firstname}} {{$task_info->getCreatedBy->user_lastname}})</span></th>
                <th colspan="2">{{$task_info->getUser->user_nickname}}<span class="text-capitalize">({{$task_info->getUser->user_firstname}} {{$task_info->getUser->user_lastname}})</span></th>
            </tr>
            <tr>
                <td>DATE START</td>
                <td>DATE END</td>
                <td>%COMPLETE</td>
                <td>LAST UPDATED</td>
                <td>UPDATED BY</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <th>{{$task_info->date_start!=""?format_date($task_info->date_start):""}}</th>
                <th>{{$task_info->date_start!=""?format_date($task_info->date_end):""}}</th>
                <th>{{$task_info->complete_percent}}</th>
                <th>{{format_datetime($task_info->updated_at)}}</th>
                <th class="text-capitalize">{{$task_info->getUpdatedBy->user_firstname}} {{$task_info->getUpdatedBy->user_lastname}}</th>
                <th colspan="2"><a href="{{route('edit-task',$id)}}"><i class=" fas fa-edit"></i><span class="text-info">Edit Task</span></a> <a href="javascript:void(0)" id="send-notification"><i class="fas fa-bell"></i><span class="text-info ">Send Email & Notification to CSR</span></a></th>
            </tr>
            @if($task_info->order_id != "")
                <tr>
                    <td colspan="2">ORDER#</td>
                    <td>ORDER STATUS</td>
                    <td>ORDER DATE</td>
                    <td>SELLER</td>
                    <td colspan="2">CUSTOMER BUSINESS NAME</td>
                </tr>
                <tr>
                    <th class="text-info" colspan="2"><a href="{{route('order-view',$task_info->order_id)}}" title="Go To Order">#{{$task_info->order_id}}</a></th>
                    <th class="text-info">
                        @php
                            $count = 0;
                            $task_status = 0;

                            $order_info = \App\Models\MainComboServiceBought::find($task_info->order_id);
                            foreach($order_info->getTasks as $task){
                                $count++;
                                $task_status = intval($task->status);
                            }
                            $order_status = $task_status/$count;
                        if($order_status == 0)
                            $status = 'NEW';
                        elseif($order_status > 0 && $order_status < 1)
                            $status = 'PROCESSING';
                        else
                            $status = 'DONE';
                        @endphp
                        {{$status}}
                    </th>
                    <th>{{format_datetime($task_info->created_at)}}</th>
                    <th class="text-capitalize">{{$task_info->getCreatedBy->user_firstname}} {{$task_info->getCreatedBy->user_lastname}}</th>
                    <th colspan="2" class="text-capitalize">{{$task_info->getPlace->place_name}}</th>
                </tr>
                <tr>
                    <td colspan="7">ORDER FORM DATA</td>
                </tr>
                <tr>
                    <td colspan="7">
                        @if($task_info != "")
                            @php
                                $content_arr = json_decode($task_info->content,TRUE);
                            @endphp
                            {{-- Google --}}
                            @if($task_info->getService->cs_form_type == 1)
                                <span>Google Link: <b>{{$content_arr['google_link']}}</b></span><br>
                                <span>Tên thợ nails: {{$content_arr['worker_name']}}</span><br>
                                <div class="row">
                                    <span class="col-md-6">Number of starts: <b>{{$content_arr['star']}}</b></span>
                                    <span class="col-md-6">Số review hiện tại: <b>{{$content_arr['current_review']}}</b></span>
                                    <span class="col-md-6">Conplete date: <b>{{$content_arr['complete_date']}}</b></span>
                                    <span class="col-md-6">Số review yêu cầu: <b>{{$content_arr['order_review']}}</b></span>
                                </div>
                            @endif
                            {{-- Website --}}
                            @if($task_info->getService->cs_form_type == 2)
                                <span>Tên sản phẩm: <b>{{$content_arr['product_name']}}</b></span><br>
                                <span>Màu chủ đạo: <b>{{$content_arr['main_color']}}</b></span><br>
                                <span>Thể loại hoặc phong cách khách hàng: <b>{{$content_arr['style_customer']}}</b></span><br>
                                <span>Facebook Link: <b>{{$content_arr['link']}}</b></span><br>
                                <span>Website: <b>{{$content_arr['website']}}</b></span><br>
                            @endif
                            {{-- Facebook --}}
                            @if($task_info->getService->cs_form_type == 3)
                                <span>Facebook Link: <b>{{$content_arr['link']}}</b></span><br>
                                <span>Promotion: <b>{{$content_arr['promotion']}}</b></span><br>
                                <span>Số lượng bài viết: <b>{{$content_arr['number']}}</b></span><br>
                                <div class="row">
                                    <span class="col-md-6">Đã có admin chưa: <b>{{isset($admin)?"YES":"NO"}}</b></span>
                                    <span class="col-md-6">Username: <b>{{$content_arr['user']}}</b></span>
                                    <span class="col-md-6">Có lấy được hình ảnh: <b>{{isset($image)?"YES":"NO"}}</b></span>
                                    <span class="col-md-6">Password: <b>{{$content_arr['password']}}</b></span>
                                </div>
                            @endif
                            {{-- Domain --}}
                            @if($task_info->getService->cs_form_type == 4)
                                <span>Domain:<b>{{$content_arr['domain']}}</b></span>
                                <div class="row">
                                    <span class="col-md-6">Is Show Service Price: <b>Yes</b></span>
                                    <span class="col-md-6">Themes: <b>{{$content_arr['theme']}}</b></span>
                                    <span class="col-md-6">Business Name: <b>{{$content_arr['business_name']}}</b></span>
                                    <span class="col-md-6">Business Phone: <b>{{$content_arr['business_phone']}}</b></span>
                                    <span class="col-md-6">Business Email: <b>{{$content_arr['email']}}</b></span>
                                    <span class="col-md-6">Business Phone: <b>{{$content_arr['address']}}</b></span>
                                </div>
                            @endif
                        @endif
                        <p><b>Notes:</b></p>
                        <div class="ml-5">
                            {!!$task_info->note!!}
                        </div>
                        {{-- get file--}}
                        @php
                            $file_list = $task_info->getFiles;
                            $file_name = "<div class='row '>";

                            foreach ($file_list as $key => $file) {
                                $zip = new ZipArchive();
                                if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
                                    $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';
                                 }else{
                                   $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';
                                }
                            }
                                $file_name .= "</div>";
                        @endphp
                        {!! $file_name !!}
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="7">TASK DESCRIPTION</td>
            </tr>
            <tr>
            	<td colspan="7">
            		<div class="ml-5">
        		    	{!!$task_info->desription!!}
        		    </div>
            	</td>
            </tr>

        </tbody>
    </table>
    @if(count($task_info->getSubTask))
    <div class="border border-info mb-4">
    	<table class="table table-bordered table-hover mb-0" id="subtask-datatable" width="100%" cellspacing="0">
	        <thead  class="thead-light">
	            <tr>
	                <th class="text-info">SUB TASK#</th>
	                <th class="text-info">SUBJECT</th>
	                <th class="text-info">PRIORITY</th>
	                <th class="text-info">STATUS</th>
	                <th class="text-info">DATE START</th>
	                <th class="text-info">DATE END</th>
	                <th class="text-info">%COMPLETE</th>
	                <th class="text-info">ASSIGN TO</th>
	                <th class="text-info">LAST UPDATE</th>
	            </tr>
	        </thead>
	    </table>
    </div>
    @endif
    <table class="table table-bordered table-hover" id="tracking_history" width="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr>
                <th hidden></th>
                <th style="width:20%">TRACKING HISTORY</th>
                <th style="width:80%"></th>
            </tr>
        </thead>
    </table>
</div>
<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info mt-5">ADD NEW COMMENT</h4>
<form  enctype="multipart/form-data" accept-charset="utf-8">
    @csrf()
    <textarea  id="summernote2" class="form-control form-control-sm"  name="note"></textarea>
    <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile2()" placeholder="">
    <input type="file" hidden id="file_image_list_2" multiple name="file_image_list[]">
    <p>(The maximum upload file size: 100M)</p>
    <div style="height: 10px" class="bg-info">
    </div>
    <hr style="border-top: 1px dotted grey;">
    <p class="text-primary">An email notification will send to {{\Auth::user()->user_email}}</p>
     <div class="input-group mb-2 mr-sm-2">
        <div class="input-group-prepend">
          <div class="input-group-text">Add CC:</div>
        </div>
        <input type="text" class="form-control" name="email_list" id="email_list_2" placeholder="">
      </div>
    <p>CC Multiple Email for example:<i> email_1@gmail.com;email_2@gmail.com</i></p>
    <button type="botton" class="btn btn-sm btn-primary submit-comment">Submit Comment</button>
</form>
@endsection
@push('scripts')
<script>
	function getFile2(){
        $("#file_image_list_2").click();
    }
	$(document).ready(function() {

        $('#summernote2').summernote({
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

        var table = $('#subtask-datatable').DataTable({
            // dom: "lBfrtip",
            paging: false,
            info:false,
            searching: false,
            order:[[0,'desc']],
            buttons: [
            ],
            // processing: true,
            serverSide: true,
            ajax:{ url:"{{ route('get-subtask') }}",
            data: function (d) {
                d.task_id = '{{$id}}'
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
                    { data: 'assign_to', name: 'assign_to',class: 'text-center' },
                    { data: 'updated_at', name: 'updated_at',class: 'text-center'},
                ],
        });

		var table = $('#tracking_history').DataTable({
            // dom: "lBfrtip",
            order:[[0,'desc']],
            buttons: [
            ],
            // processing: true,
            serverSide: true,
            ajax:{ url:"{{ route('task-tracking') }}",
            data: function (d) {
            	d.task_id = '{{$id}}'
            }
        },
           columns: [

                    { data: 'created_at', name: 'created_at',class:'d-none' },
                    { data: 'user_info', name: 'user_info' },
                    { data: 'content', name: 'content'},
                ],
        });

        $(document).on("click",".file-comment",function(){
            $(this).parent('form').submit();
        });
        $('body').on('click', '.submit-comment', function(e){
            e.preventDefault();
            var formData = new FormData($(this).parents('form')[0]);
            formData.append('order_id',{{$task_info->order_id}});
            formData.append('task_id',{{$id}});

            $.ajax({
                url: '{{route('post-comment')}}',
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
                    console.log(data);
                    // return;
                    // data = JSON.parse(data);
                    if(data.status == 'error'){
                        toastr.error(data.message);
                    }else{
                        toastr.success(data.message);
                        table.draw();
                        clearView();
                    }
                },
                fail: function() {
                    console.log("error");
                }
            });
            return false;
        });
        function clearView(){
            $("#email_list_2").val("");
            $("#summernote2").val("");
        }
        $("#send-notification").click(function(){
            $("#form-notification").modal('show');
        });
        function clearViewNotification(){
            $("#subject").val("");
            $("#message").val("");
            $("#form-notification").modal('hide');
        }
        $(".cancel").click(function(){
            clearViewNotification();
        });
        $(".close").click(function(){
            clearViewNotification();
        });
        $(".submit").click(function(e){

            var subject = $("#subject").val();
            var message = $("message").val();
            if(subject == ""){
                toastr.error('Type Subject');
                return;
                e.preventDefault();
            }
            if(message == ""){
                toastr.error('Type Message');
                return;
                e.preventDefault();
            }

            var formData = new FormData($(this).parents('form')[0]);
            formData.append('_token','{{csrf_token()}}');
            $.ajax({
                url: '{{route('send-mail-notification')}}',
                type: 'POST',
                dataType: 'html',
                cache: false,
                contentType: false,
                processData: false,
                async: true,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    return myXhr;
                },
                data: formData,
            })
            .done(function(data) {
                // console.log(data);
                // return;
                data = JSON.parse(data);
                if(data.status == 'error'){
                    toastr.error(data.message);
                }else{
                    toastr.success(data.message);
                    clearViewNotification();
                }
            })
            .fail(function() {
                console.log("error");
            });
        })
	});
</script>
@endpush
