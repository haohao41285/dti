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
{{--MODAL DETAIL REVIEW--}}
<div class="modal fade" id="detail_review">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Detail Review</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <form id="review-form">
            <div class="modal-body">

                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" disabled value="" id="review_name" class="form-control form-control-sm">
                        <input type="hidden" name="review_id" id="review_id" value="">
                    </div>
                    <div class="form-group">
                        <label for="">Note</label>
                        <textarea name="note" id="note_review" class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                    <div class="form-group status-form">

                    </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger btn-cancel-review" >Cancel</button>
                <button type="button" class="btn btn-sm btn-primary btn-submit-review" >Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
{{--END MODAL DETAIL REVIEW--}}
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
                <th>{{getCategory()[$task_info->category]}}</th>
                <th class="text-info">{{getPriorityTask()[$task_info->priority]}}</th>
                <th class="text-info status-task">{{getStatusTask()[$task_info->status]}}</th>
                <th>{{format_datetime($task_info->created_at)}}</th>
                <th>{{$task_info->getCreatedBy->user_nickname}}<span class="text-capitalize">({{$task_info->getCreatedBy->user_firstname}} {{$task_info->getCreatedBy->user_lastname}})</span></th>
                <th colspan="2">
                    @foreach($assign_to as $assign)
                        {{$assign->user_nickname}}
                        <span class="text-capitalize">({{$assign->user_firstname}} {{$assign->user_lastname}})</span><br>
                    @endforeach
                </th>
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
                <th class="percent_complete">{{$task_info->complete_percent}}%</th>
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
                        @if($task_info->getOrder->csb_status == 0)
                            NOTPAYMENT
                        @else
                            PAID
                        @endif
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
                                 $allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
                                $contentType = mime_content_type($file->name);
                                if(! in_array($contentType, $allowedMimeTypes) ){
                                      $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';

                                }else{
                                      $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';

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

{{--    <div class="row">--}}
        @if((isset($content_arr['order_review']) && $content_arr['order_review'] > 0)
            || (isset($content_arr['number']) && $content_arr['number'] > 0)
            )
            <form>
                {{--@for ($i = 1; $i <= $content_arr['order_review']; $i++)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for=""><b>Review {{$i}}</b></label>
                            <label for="fail_{{$i}}" class="float-right text-danger"><input type="checkbox" id="fail_{{$i}}"> Fail</label>
                            <label for="done_{{$i}}" class="float-right text-primary"><input type="checkbox" id="done_{{$i}}"> Done</label>

                        </div>
                    </div>
                @endfor--}}
                <h4 class="text-info">Review  List</h4>
                <table class="table table-hover table-striped" id="table_review">
                    <thead>
                        <tr>
                            <th style="width: 20%">Name Review</th>
                            <th>Note</th>
                            <th style="width: 20%">Status</th>
                            <th style="width: 10%" class="text-center"></th>
                        </tr>
                    </thead>
                </table>
            </form>
    @endif
{{--    </div>--}}
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
    <input type="hidden" name="receiver_id" value="{{$task_info->assign_to==\Illuminate\Support\Facades\Auth::user()->user_id?$task_info->created_by:$task_info->assign_to}}">
    <textarea  id="summernote2" class="form-control form-control-sm"  name="note" placeholder="Text Content..."></textarea>
    <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile2()" placeholder=""><br>
    <span id="file_names"></span>
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
    @if($task_info->status == 3)
        <div class="form-group change-status-form">
            <label for="status" class="required">Change Status</label>
            <select name="status" id="status" class="form-control form-control-sm">
                @foreach(getStatusTask() as $key => $status)
                    @if($key != 1)
                        <option value="{{$key}}">{{$status}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    @endif
    <button type="button" class="btn btn-sm btn-primary submit-comment">Submit Comment</button>
</form>
@endsection
@push('scripts')
<script>
	function getFile2(){
        $("#file_image_list_2").click();
    }
	$(document).ready(function() {
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
        $(document).on('click', '.submit-comment', function(){
            var formData = new FormData($(this).parents('form')[0]);
            formData.append('order_id',{{$task_info->order_id}});
            formData.append('task_id',{{$id}});
            formData.append('_token','{{csrf_token()}}');

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
                    // console.log(data);
                    // return;
                    // data = JSON.parse(data);
                    if(data.status == 'error'){
                        if(typeof(data.message) == "string")
                            toastr.error(data.message);
                        else{
                            $.each(data.message,function(ind,val){
                                toastr.error(val);
                            });
                        }
                    }else{
                        toastr.success(data.message);
                        table.draw();
                        clearView();
                        if($("#status").val() ===  2)
                            $(".change-status-form").remove();
                            $(".status-task").text("PROCESSING");
                        if($("#status").val() === 3)
                            $(".status-task").text("DONE");
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
            // $('#summernote2').summernote('reset');
            $('#summernote2').val("");
            $("#file_names").text("");
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
        });
        //  GET NAME FILES
        $(document).on('change','#file_image_list_2',function(e){
            file_image_list = Array.from(e.target.files);
            console.log(file_image_list);

            var names = [];
            var name_html = "";

            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
                name_html += "<span>"+$(this).get(0).files[i].name+ "</span><br>";

            }
            $("#file_names").html(name_html);
        });
       /* $(document).on("click",".remove-file",function(){
            var index = $(this).attr('index');
            file_image_list.splice(index,1);
            console.log(file_image_list);
            $(this).closest('span').remove();
        })*/
       function getStatusTaskOrder(){
           $.ajax({
               url: '{{route('get_status_task_order')}}',
               type: 'GET',
               dataType: 'html',
               data: {
                   order_id: '{{$task_info->order_id}}',
                   task_id: '{{$id}}'
               },
           })
               .done(function(data) {
                   console.log(data);
                   return;
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
       }
       @if( (isset($content_arr['number']) && $content_arr['number'] > 0)
        || (isset($content_arr['order_review']) && $content_arr['order_review'] > 0)
       )
        var table_review = $('#table_review').DataTable({
            // dom: "lBfrtip",
            // paging: false,
            // info:false,
            // searching: false,
            responsive: false,
            // order:[[0,'desc']],
            buttons: [
            ],
            // processing: true,
            serverSide: true,
            ajax:{ url:"{{ route('get_review') }}",
                data: function (d) {
                    d.task_id = '{{$id}}',
                    @if( isset($content_arr['order_review']) )
                        d.order_review = '{{$content_arr['order_review']}}'
                    @elseif(isset($content_arr['number']))
                        d.order_review = '{{$content_arr['number']}}'
                    @endif
                }
            },
            columns: [
                { data: 'name', name: 'name',class:'text-center' },
                { data: 'note', name: 'note', },
                { data: 'status', name: 'status',class:'text-center' },
                {data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
            ],
        });
        /*$('#table_review tbody').on( 'click', 'tr td:nth-of-type(2)', function () {
            var note = table.cell( this ).data();
            var note_html = `<textarea name="" id="" class="form-control form-control-sm" rows="3">`+note+`</textarea>`;
            $(this).html(note_html);
        }).blur(function(){
            alert('ok');
        })*/
        $(document).on('click','.edit-review',function(){

            var note = $(this).attr('note');
            var review_id = $(this).attr('review_id');
            var status = $(this).attr('status');

            var status_html = "";
            var check_1 = "";
            var check_2 = "";

            if(status == ""){
                status_html = `
                 <div class="custom-control custom-radio">
                    <input type="radio" class="custom-control-input" checked id="successfully" name="status" value="1">
                    <label class="custom-control-label text-primary" for="successfully">SUCCESSFULLY</label>
                </div>
                `;
            }else{
                if(status == 1){
                    check_1 = "checked";
                    check_2 = "";
                }else{
                    check_1 ="";
                    check_2 = "checked";
                }
                status_html = `
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" `+check_1+` id="successfully" name="status" value="1">
                        <label class="custom-control-label text-primary" for="successfully">SUCCESSFULLY</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" `+check_2+` id="failed" name="status" value="0">
                        <label class="custom-control-label text-danger" for="failed">FAILED</label>
                    </div>
                `;
            }
            $("#review_id").val(review_id);
            $("#review_name").val("Review "+review_id);
            $("#note_review").val(note);
            $(".status-form").html(status_html);
            $("#detail_review").modal('show');
        });
        $(".btn-cancel-review").click(function(){
            clearModalReview();
        });
        $(".btn-submit-review").click(function(){
            var formData = new FormData($(this).parents('form')[0]);
            formData.append('task_id',{{$id}});
            formData.append('_token','{{csrf_token()}}');
            formData.append('user_id','{{$task_info->assign_to}}');
            formData.append('place_id','{{$task_info->place_id}}');
            @if(isset($content_arr['order_review']) )
            formData.append('total_order','{{$content_arr['order_review']}}');
            @elseif(isset($content_arr['number']) )
            formData.append('total_order','{{$content_arr['number']}}');
            @endif
            $.ajax({
                url: '{{route('save_review')}}',
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
            })
                .done(function(data) {

                    if(data.status == 'error'){
                        toastr.error(data.message);
                    }else{
                        toastr.success(data.message);
                        table_review.ajax.reload(null, false);
                        clearModalReview();
                        if(data.status == 2)
                            var status = "PROCESSING";
                        else
                            var status = 'DONE';

                        $(".status-task").text(status);
                        $(".percent_complete").text(data.percent_complete+"%");
                    }
                })
                .fail(function() {
                    console.log("error");
                })
        });
        function clearModalReview(){
            $("#review-form")[0].reset();
            $("#detail_review").modal('hide');
        }
        @endif
	});
</script>
@endpush
