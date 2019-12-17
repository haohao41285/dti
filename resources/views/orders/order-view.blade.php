@extends('layouts.app')
@section('title')
    My Orders
@endsection
@push('styles')
<style type="text/css" media="screen">
   .file-comment{
        /*max-width: 100px;*/
        max-height: 80px;
    }
   .note-popover.popover {
        display: none;
   }

</style>
@endpush
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
        <form>
        <div class="modal-body" id="content-form">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary submit-input-form">Submit</button>
        </div>
        </form>
      </div>

    </div>
  </div>
{{-- END MODAL --}}
{{-- MODAL FOR COMMENT --}}
{{--<div class="modal fade" id="add-comment-modal" role="dialog">--}}
{{--    <div class="modal-dialog modal-lg">--}}
{{--      <!-- Modal content-->--}}
{{--      <div class="modal-content">--}}
{{--        <div class="modal-header">--}}
{{--          <h6 class="modal-title"><b>Add Comment</b></h6>--}}
{{--          <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
{{--        </div>--}}
{{--        <div class="modal-body">--}}
{{--            <form  enctype="multipart/form-data" accept-charset="utf-8">--}}
{{--                @csrf()--}}
{{--                <textarea hidden id="summernote" class="form-control form-control-sm" name="note"></textarea>--}}
{{--                <input type="hidden" name="receiver_id" id="receiver_id">--}}
{{--                <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile()" placeholder="">--}}
{{--                <input type="file" hidden id="file_image_list" multiple name="file_image_list[]">--}}
{{--                <p>(The maximum upload file size: 100M)</p>--}}
{{--                <div style="height: 10px" class="bg-info">--}}
{{--                </div>--}}
{{--                <hr style="border-top: 1px dotted grey;">--}}
{{--                <p class="text-primary">An email notification will send to web@dataaeglobal.com</p>--}}
{{--                 <div class="input-group mb-2 mr-sm-2">--}}
{{--                    <div class="input-group-prepend">--}}
{{--                      <div class="input-group-text">Add CC:</div>--}}
{{--                    </div>--}}
{{--                    <input type="text" class="form-control" name="email_list" id="email_list" placeholder="">--}}
{{--                  </div>--}}
{{--                <p>CC Multiple Email for example:<i> email_1@gmail.com;email_2@gmail.com</i></p>--}}
{{--                <button type="botton" class="btn btn-sm btn-primary submit-comment">Submit Comment</button>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--      </div>--}}

{{--    </div>--}}
{{--  </div>--}}
{{-- END MODAL COMMENT --}}
<div class="table-responsive">
	<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">ORDER INFORMATON #{{$id}}</h4>
    <table class="table table-striped mt-4 table-bordered" id="dataTableAllCustomer" widtd="100%" cellspacing="0">
        <tbody>
            <tr>
                <td>ORDER</td>
                <td>ORDER STATUS</td>
                <td>ORDER DATE</td>
                <td>ORDER SELLER</td>
            </tr>
            <tr>
                <th>#{{$id}}</th>
                <th class="status">
                    {{($order_info->csb_status==0?"NOTPAYMENT":"PAID")}}
                    @if($order_info->csb_status == 0)
                    <a href="javascript:void(0)" id="change-status"> <i class="fas fa-edit"></i><span>Change Status</span></a>
                    @endif
                </th>
                <th>{{format_datetime($order_info->created_at)}}</th>
                <th>{{$order_info->user_nickname}} ({{$order_info->user_email}})</th>
            </tr>
            <tr>
                <td>SUB TOTAL</td>
                <td>DISCOUNT</td>
                <td>PAYMENT AMOUNT</td>
                <td>PAYMENT INFO</td>
            </tr>
            <tr>
                <th>{{$order_info->csb_amount}}</th>
                <th>{{$order_info->csb_amount_deal}}</th>
                <th>{{$order_info->csb_charge}}</th>
                <th>
                    <span>{{$order_info->csb_card_type}}</span><br>
                    @if($order_info->csb_amount != "")<span>{{$order_info->csb_amount}}</span><br>@endif
                    @if($order_info->csb_card_number != "")<span>{{$order_info->csb_card_number}}</span><br>@endif
                    @if($order_info->routing_number != "")<span>{{$order_info->routing_number}}</span><br>@endif
                    @if($order_info->account_number != "")<span>{{$order_info->account_number}}</span><br>@endif
                    @if($order_info->bank_name != "")<span>{{$order_info->bank_name}}</span><br>@endif
                </th>
            </tr>
            <tr>
                <td>CUSTOMER(<a href="{{route('customer-detail',$customer_id)}}">View Detail</a>)</td>
                <td>CELL PHONE</td>
                <td>BUSINESS NAME</td>
                <td>BUSINESS PHONE</td>
            </tr>
            <tr>
                <th>{{$order_info->customer_firstname}} {{$order_info->customer_lastname}}</th>
                <th></th>
                <th></th>
                <th>{{$order_info->customer_phone}}</th>
            </tr>
            @if(\Gate::allows('permission','order-invoice'))
            <tr>
                <td colspan="2">ORDER NOTES: {{$order_info->csb_note}}</td>
                <td>
                    <a href="{{route('dowload-invoice',$id)}}"><button class="btn btn-sm btn-info"><i class="fas fa-file-pdf text-danger"></i> PRINT INVOICE</button></a>
                    <button class="btn btn-sm btn-info resend-invoice"><i class="fas fa-envelope text-danger"></i> RESEND INVOICE</button>
                </td>
                <td></td>
                {{-- <td class="align-left"><i class="text-primary">Last sent invoice:</i></td> --}}
            </tr>
            @endif
        </tbody>
    </table>
    {{--<table class="table mt-4 table table-hover" id="service-datatable" widtd="100%" cellspacing="0">
        <thead class="thead-light">
            <tr>
                <th style="width: 10%">SERVICE NAME</th>
                <th class="text-center" style="width: 10%">PRICE($)</th>
                <th class="text-center" style="width: 10%">ACTION</th>
                <th style="width: 70%">SERVICE ORDER FORM</th>
            </tr>
        </thead>

    </table>--}}
    <table class="table mt-4 table-sm table-hover table-bordered" id="" widtd="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr class="text-center">
                <th>TASK#</th>
                <th>SERVICE</th>
                <th>ACTION</th>
                <th>PRIORITY</th>
                <th>STATUS</th>
                <th>DATE START</th>
                <th>DATE END</th>
                <th>%COMPLETE</th>
                <th>ASSIGNE</th>
                <th>LAST UPDATE</th>
{{--                <th class="text-center">ACTION</th>--}}
            </tr>
        </thead>
        <tbody>
            @foreach($task_list as $task)
            <tr class="text-center">
                <td><a href="{{route('task-detail',$task->id)}}" title="">#{{$task->id}}</a></td>
                <td>{{$task->subject}}</td>
                <td>
                    <button type="button" form_type_id="{{$task->getService->cs_form_type}}" task_id="{{$task->id}}" class="btn btn-sm btn-secondary input-form">INPUT FORM</button>
                </td>
                <td>{{\App\Helpers\GeneralHelper::getPriorityTask()[$task->priority]}}</td>
                <td>{{\App\Helpers\GeneralHelper::getStatusTask()[$task->status]}}</td>
                <td>{{$task->date_start!=""?format_date($task->date_start):""}}</td>
                <td>{{$task->date_end!=""?format_date($task->date_end):""}}</td>
                <td>{{$task->complete_percent}}</td>
                <td>{{$task->getUser->user_nickname}}</td>
                <td class="text-left">{{format_datetime($task->updated_at)}} by {{$task->user_nickname}}</td>
{{--                <td class="text-primary add-comment" order_id="{{$id}}" created_by="{{$task->created_by}}" task_id="{{$task->id}}" assign_to="{{$task->assign_to}}" ><a href="javascript:void(0)" title="">Add comment</a></td>--}}
            </tr>
            @endforeach
        </tbody>
    </table>
{{--    <table class="table mt-4 table-bordered table-hover" id="tracking_history" width="100%" cellspacing="0">--}}
{{--        <thead  class="thead-light">--}}
{{--            <tr>--}}
{{--                <th hidden></th>--}}
{{--                <th style="width:20%">TRACKING HISTORY</th>--}}
{{--                <th style="width:10%"></th>--}}
{{--                <th style="width:70%"></th>--}}
{{--            </tr>--}}
{{--        </thead>--}}
{{--    </table>--}}
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    function uploadFile(){
        $("#upload_file").click();
    }
    function getFile(){
        $("#file_image_list").click();
    }
    function getFile2(){
        $("#file_image_list_2").click();
    }
    $(document).ready(function() {
        var task_id = 0;
        var order_id = {{$id}};

        $('#summernote').summernote({
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

        {{--var table = $('#tracking_history').DataTable({--}}
        {{--    // dom: "lBfrtip",--}}
        {{--    order:[[0,'desc']],--}}
        {{--    info: false,--}}
        {{--    buttons: [--}}
        {{--    ],--}}
        {{--    // processing: true,--}}
        {{--    serverSide: true,--}}
        {{--    ajax:{ url:"{{ route('order-tracking') }}",--}}
        {{--    data: function (d) {--}}
        {{--        d.order_id = '{{$id}}'--}}
        {{--    }--}}
        {{--},--}}
        {{--   columns: [--}}
        {{--            { data: 'created_at', name: 'created_at',class:'d-none' },--}}
        {{--            { data: 'user_info', name: 'user_info' },--}}
        {{--            { data: 'task', name: 'task',class: 'text-center' },--}}
        {{--            { data: 'content', name: 'content'},--}}
        {{--        ],--}}
        {{--});--}}
        /*var service_table = $('#service-datatable').DataTable({
            // dom: "lBfrtip",
            // order:[[0,'desc']],
            responsive: false,
            searching: false,
            paging: false,
            info: false,

            buttons: [
            ],
            processing: true,
            serverSide: true,
            ajax:{ url:"{{ route('order-service') }}",
            data: function (d) {
                d.order_id = '{{$id}}'
            }
        },
           columns: [
                    { data: 'cs_name', name: 'cs_name'},
                    { data: 'cs_price', name: 'cs_price', class:'text-right' },
                    { data: 'action', name: 'action',class: 'text-center' },
                    { data: 'infor', name: 'infor'},
                ],
        });
*/
        $('body').on('click', '.submit-comment', function(e){
            e.preventDefault();
            var formData = new FormData($(this).parents('form')[0]);
            formData.append('order_id',order_id);
            formData.append('task_id',task_id);

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
                        clearView();
                        // table.draw();
                    }
                },
                fail: function() {
                    console.log("error");
                }
            });
            return false;
        });

        function clearView(){
            task_id = 0;
            $("#email_list").val("");
            $("#email_list_2").val("");
            $(".note-editable p").html("");
            $("#summernote2").summernote('reset');
            $("#add-comment-modal").modal('hide');
        }

        $(document).on("click",".input-form",function(){

            var input_form_type = $(this).attr('form_type_id');
            task_id = $(this).attr('task_id');

            $.ajax({
                url: '{{route('input_form.task')}}',
                type: 'get',
                dataType: 'html',
                data: {
                    task_id: task_id
                },
            })
                .done(function(data) {
                    // console.log(data);
                    // return;
                    data = JSON.parse(data);
                    if(data.status == 'error')
                        toatr.error(data.mesage);
                    else{
                        var content = data.content;
                        var content_html = getHtmlForm(input_form_type,content);
                        $("#content-form").html(content_html);
                        $("#datepicker_form").datepicker({
                            todayHighlight: true,
                            setDate: new Date(),
                        });
                        $("#modal-input-form").modal('show');
                        // if(data.content == null)
                        //     console.log('Ok');
                    }
                })
                .fail(function() {
                    console.log("error");
                });
            return;

        });
        function getHtmlForm(input_form_type,content){
            content = JSON.parse(content);
            console.log(content);

             var content_html = "";

            if(input_form_type == 1){

                var google_link_html = "";
                var worker_name_html = "";
                var star_html = "";
                var current_review_html ="";
                var order_review_html = "";
                var complete_date_html = "";
                var description_html = "";

                 if( content !== null && typeof(content['google_link']) != "undefined" && content['google_link'] !== null)
                     google_link_html = '<b>'+content['google_link']+'</b>';
                 else
                     google_link_html = `<input type="text" class="form-control form-control-sm" id="google-link" name="google_link" value="">`;

                if(content !== null && typeof(content['worker_name']) != "undefined" && content['worker_name'] !== null)
                    worker_name_html = '<b>'+content['worker_name']+'</b>';
                else
                    worker_name_html = '<input type="text" class="form-control form-control-sm" id="worker-name" name="worker_name" value="">';

                if(content !== null && typeof(content['star']) != "undefined" && content['star'] !== null)
                    star_html = "<b>"+content['star']+"</b>";
                else
                    star_html = '<input type="number" class="form-control form-control-sm col-md-6" id="number_of_stars" name="star" value="">';

                if(content !== null && typeof(content['current_review']) != "undefined" && content['current_review'] !== null)
                    current_review_html = "<b>"+content['current_review']+"</b>";
                else
                    current_review_html = '<input type="number" class="form-control form-control-sm col-md-6"  id="number_of_reviews" name="current_review" value="">';

                if(content !== null && typeof(content['order_review']) != "undefined" && content['order_review'] !== null)
                    order_review_html = "<b>"+content['order_review']+"</b>";
                else
                    order_review_html = '<input type="number" class="form-control form-control-sm col-md-6"  id="number_of_reviews" name="order_review" value="">';

                if(content !== null && typeof(content['complete_date']) != "undefined" && content['complete_date'] !== null)
                    complete_date_html = "<b>"+content['complete_date']+"</b>";
                else
                    complete_date_html = '<input type="text" class="form-control form-control-sm col-md-6" id="datepicker_form" name="complete_date" >';

                if(content !== null && typeof(content['desription']) != "undefined" && content['desription'] !== null)
                    description_html = "<span class='text-danger'>"+content['desription']+"</span>";
                else
                    description_html = '<textarea class="form-control form-control-sm desription" name="desription" rows="3"></textarea>'

                content_html = `
                <div class="form-group">
                    <label for="google-link">Google Link: </label>
                    `+google_link_html+`
                </div>
                <div class="form-group">
                    <label for="worker-name">Tên Thợ Nails: </label>
                    `+worker_name_html+`
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="number_of_stars" class="col-md-6">Number of Stars: </label>
                            `+star_html+`
                        </div>
                        <div class="form-group row">
                            <label for="number_of_reviews" class="col-md-6">Số Reviews hiện tại: </label>
                            `+current_review_html+`
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6" for="offer_of_reviews">Số reviews yêu cầu: </label>
                            `+order_review_html+`
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6" for="offer_of_reviews">Complete Date: </label>
                            `+complete_date_html+`
                        </div>
                    </div>
                    <!--<div class="col-md-1" style="border-right: .5px dashed grey">

                    </div>-->
                    <!--<div class="col-md-5">
                        <input type="file" hidden id="file" name="" value="">
                        <input type="button" class="btn btn-sm btn-secondary" onclick="uploadFile()" value="Upload attachment files" name="">
                        <input type="file" id="upload_file" hidden class="" value="" name="list_file[]" multiple><br>
                        <span id="file_names"></span>
                    </div>-->

                </div>
                <div class="form-group">
                    <label for="desription">Description: </label>
                    `+description_html+`
                </div>
                `;
            }
            if(input_form_type == 2){
                let product_name_html = "";
                let style_customer_html = "";
                let link_html = "";
                let website_html = "";
                let main_color_html ="";

                if(content !== null && typeof(content['product_name']) != "undefined" && content['product_name'] !== null)
                    product_name_html = '<b>'+content['product_name']+'</b>';
                else
                    product_name_html = `<input type="text" class="form-control form-control-sm" id="product_name" name="product_name" value="" placeholder="">`;

                if(content !== null && typeof(content['style_customer']) != "undefined" && content['style_customer'] !== null)
                    style_customer_html = '<b>'+content['style_customer']+'</b>';
                else
                    style_customer_html = `<input type="text" class="form-control form-control-sm" id="kind_of" name="style_customer" value="" placeholder="">`;

                if(content !== null && typeof(content['link']) != "undefined" && content['link'] !== null)
                    link_html = '<b>'+content['link']+'</b>';
                else
                    link_html = `<input type="text" class="form-control form-control-sm" id="facebook_link" name="link" value="" placeholder="">`;

                if(content !== null && typeof(content['website']) != "undefined" && content['website'] !== null)
                    website_html = '<b>'+content['website']+'</b>';
                else
                    website_html = `<input type="text" class="form-control form-control-sm" id="website" name="website" value="" placeholder="">`;

                if(content !== null && typeof(content['main_color']) != "undefined" && content['main_color'] !== null)
                    main_color_html = '<b>'+content['main_color']+'</b>';
                else
                    main_color_html = `<input type="text" class="form-control form-control-sm" id="main_color" name="main_color" value="" placeholder="">`;


                content_html = `
                <label for="product_name">Tên sản phẩm: </label>
                `+product_name_html+`<br>
                <label for="main_color">Màu chủ đạo</label>
                `+main_color_html+`<br>
                <label for="kind_of">Thể loại hoặc phong cách khách hàng hướng đến</label>
                `+style_customer_html+`<br>
                <label for="facebook_link">Facebook Link</label>
                `+link_html+`<br>
                <label for="website">Website</label>
                 `+website_html+`<br>
                <!--<div class="border border-secondary p-2 rounded">
                    <p>Upload Logo images or file</p>
                    <input type="button" class="btn btn-sm btn-secondary" onclick="uploadFile()" value="Upload attachment files" name="">
                    <input type="file" id="upload_file" hidden class="" value="" name="list_file[]" multiple><br>
                        <span id="file_names"></span>
                </div>-->
                <div class="form-group">
                    <label for="desription">Description</label>
                    <textarea class="form-control form-control-sm desription" name="desription" rows="3"></textarea>
                </div>
                `;
            }
            if(input_form_type == 3){

                let link_html = "";
                let promotion_html = "";
                let number_html ="";
                let admin_html ="";
                let user_html ="";
                let password_html = "";
                let image_html ="";

                if(content !== null && typeof(content['link']) != "undefined" && content['link'] !== null)
                    link_html = '<b>'+content['link']+'</b>';
                else
                    link_html = `<input type="text" class="form-control form-control-sm"  id="facebook-link" name="link" value="">`;

                if(content !== null && typeof(content['promotion']) != "undefined" && content['promotion'] !== null)
                    promotion_html = '<b>'+content['promotion']+'</b>';
                else
                    promotion_html = `<input type="text" class="form-control form-control-sm"  id="promotion" name="promotion" value="">`;

                if(content !== null && typeof(content['number']) != "undefined" && content['number'] !== null)
                    number_html = '<b>'+content['number']+'</b>';
                else
                    number_html = `<input type="text" class="form-control form-control-sm col-md-6"  id="number_of_stars" name="number" value="">`;

                if(content !== null && typeof(content['admin']) != "undefined" && content['admin'] !== null)
                    admin_html = '<b>Yes</b>';
                else
                    admin_html = `<input type="checkbox" class="col-md-6"  id="add_admin" name="admin" value="1">`;

                if(content !== null && typeof(content['user']) != "undefined" && content['user'] !== null)
                    user_html = '<b>'+content['user']+'</b>';
                else
                    user_html = `<input type="text" class="form-control form-control-sm col-md-6"  id="facebook_username" name="user" value="">`;

                if(content !== null && typeof(content['password']) != "undefined" && content['password'] !== null)
                    password_html = '<b>'+content['password']+'</b>';
                else
                    password_html = `<input type="text" class="form-control form-control-sm col-md-6" id="facebook_password" name="password" value="">`;

                if(content !== null && typeof(content['image']) != "undefined" && content['image_html'] !== null && content['image'] == 1)
                    image_html = '<b>Yes</b>';
                else
                    image_html = `<input type="checkbox" class="col-md-6"  id="image" name="image" value="1">`;

                content_html = `
                <div class="form-group">
                <label for="facebook-link">Facebook Link</label>
                `+link_html+`
                </div>
                <div class="form-group">
                    <label for="worker-name">Promotion</label>
                    `+promotion_html+`
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="number_of_stars" class="col-md-6">Số lượng bài viết</label>
                            `+number_html+`
                        </div>
                        <div class="form-group row">
                            <label for="add_admin" class="col-md-6">Đã add admin chưa?</label>
                            `+admin_html+`
                        </div>
                        <div class="form-group row">
                            <label for="facebook_username" class="col-md-6">Facebook Username</label>
                            `+user_html+`
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6" for="facebook_password">Facebook Password</label>
                            `+password_html+`
                        </div>
                       <div class="form-group row">
                            <label for="image" class="col-md-6">Có lấy được hình ảnh?</label>
                            `+image_html+`
                        </div>
                    </div>
                    <!--<div class="col-md-1" style="border-right: .5px dashed grey">

                    </div>-->
                   <!-- <div class="col-md-5">
                        <input type="button" class="btn btn-sm btn-secondary" onclick="uploadFile()" value="Upload attachment files" name="">
                        <input type="file" id="upload_file" hidden class="" value="" name="list_file[]" multiple><br>
                        <span id="file_names"></span>
                    </div>-->
                </div>
                <div class="form-group">
                    <label for="desription">Description</label>
                    <textarea class="form-control form-control-sm desription" name="desription" rows="3"></textarea>
                </div>
                `;
            }
            if(input_form_type == 4){

                let domain = "";
                let theme = "";
                let show_price = "";
                let business_name = "";
                let business_phone = "";
                let email = "";
                let address = "";

                if(content !== null && typeof(content['domain']) != "undefined" && content['domain'] !== null)
                    domain = '<b>'+content['domain']+'</b>';
                else
                    domain = `<input type="text" id="domain" class="form-control form-control-sm" name="domain">`;

                if(content !== null && typeof(content['theme']) != "undefined" && content['theme'] !== null)
                    theme = '<b>'+content['theme']+'</b>';
                else
                    theme = `<input type="text" id="theme" class="form-control form-control-sm col-md-10" name="theme">`;

                if(content !== null && typeof(content['show_price']) != "undefined" && content['show_price'] !== null && content['show_price'] == 1)
                    show_price = '<b>Yes</b>';
                else
                    show_price = `<input type="checkbox" class="col-md-2 mt-1" id="show_price" name="show_price" value="1">`;

                if(content !== null && typeof(content['business_name']) != "undefined" && content['business_name'] !== null)
                    business_name = '<b>'+content['business_name']+'</b>';
                else
                    business_name = `<input type="text" class="col-md-3 form-control form-control-sm" id="business_name" name="business_name">`;

                if(content !== null && typeof(content['business_phone']) != "undefined" && content['business_phone'] !== null)
                    business_phone = '<b>'+content['business_phone']+'</b>';
                else
                    business_phone = `<input type="number" class="col-md-3 form-control form-control-sm" id="business_phone" name="business_phone">`;

                if(content !== null && typeof(content['email']) != "undefined" && content['email'] !== null)
                    email = '<b>'+content['email']+'</b>';
                else
                    email = `<input type="email" id="email" class="col-md-3 form-control form-control-sm" name="email">`;

                if(content !== null && typeof(content['address']) != "undefined" && content['address'] !== null)
                    address = '<b>'+content['address']+'</b>';
                else
                    address = `<input type="text" id="address" class="col-md-3 form-control form-control-sm" name="address">`;

                content_html = `
                <label for="domain">Domain</label>
                `+domain+`
                <div class="col-md-12 row mt-2">
                    <div class="col-md-6 row pr-3" style="border-right: .5px dashed grey">
                        <label class="col-md-2" for="theme">Theme</label>
                        `+theme+`<br>
                        `+show_price+`
                        <label for="show_price" class="col-md-10 mt-1">Is show Service Price?</label>
                    </div>
                    <!--<div class="col-md-6 pl-3">
                        <input type="button" class="btn btn-sm btn-secondary" onclick="uploadFile()" value="Upload attachment files" name="">
                        <input type="file" id="upload_file" hidden class="" value="" name="list_file[]" multiple><br>
                        <span id="file_names"></span>
                    </div>-->
                </div>
                <h5><b>BUSINESS INFO</b></h5>
                <div class="col-md-12 row">
                    <label class="col-md-3" for="business_name">Business Name</label>
                    `+business_name+`<br>
                    <label class="col-md-3" for="business_phone">Business Phone</label>
                    `+business_phone+`<br>
                    <label for="email" class="col-md-3">Email</label>
                     `+email+`
                    <label for="address" class="col-md-3">Address</label>
                    `+address+`
                </div>
                <div class="form-group">
                    <label for="note">Description</label>
                    <textarea class="form-control form-control-sm desription" name="desription" rows="3"></textarea>
                </div>
                `;
            }
            if(input_form_type == 5){
                content_html = `
                    <div class="form-group">
                        <label for="desription">Discription</label>
                        <textarea class="form-control form-control-sm desription" name="desription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="button" class="btn btn-sm btn-secondary" onclick="uploadFile()" value="Upload attachment files">
                        <input type="file" id="upload_file" hidden class="" value="" name="list_file[]" multiple><br>
                        <span id="file_names"></span>
                    </div>
                `;
            }

            return content_html;
        }
        $(".add-comment").click(function(){

            task_id = $(this).attr('task_id');
            var assign_to = $(this).attr('assign_to');
            var created_by = $(this).attr('created_by');

            if( assign_to == {{\Illuminate\Support\Facades\Auth::user()->user_id}}){
                receiver_id = created_by;
            }else{
                receiver_id = assign_to;
            }
            $("#receiver_id").val(assign_to);

            $("#add-comment-modal").modal('show');
        });
        $(document).on("click",".file-comment",function(){
            $(this).parent('form').submit();
        });
        $(".submit-input-form").click(function(){

            var formData = new FormData($(this).parents('form')[0]);
            formData.append('_token','{{csrf_token()}}');
            formData.append('task_id',task_id);
            formData.append('order_id',{{$id}});

            $.ajax({
                url: '{{route('submit-info-task')}}',
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
                data:formData,
            })
            .done(function(data) {
                // console.log(data);
                // return;

                data = JSON.parse(data);
                if(data.status == 'error'){
                    toasrt.error(data.message);
                }else{
                    toastr.success(data.message);
                    $("#content-form").html("");
                    $("#modal-input-form").modal('hide');
                    // service_table.draw();
                    // table.draw();
                }
            })
            .fail(function() {
                console.log("error");
            });
        });
        $("#change-status").click(function(){
            $.ajax({
                url: '{{route('change-status-order')}}',
                type: 'POST',
                dataType: 'html',
                data: {
                    order_id: '{{$id}}',
                    _token: '{{csrf_token()}}'
                },
            })
            .done(function(data) {
                // console.log(data);
                // return;
                data = JSON.parse(data);
                if(data.status == 'error')
                    toatr.error(data.mesage);
                else{
                    toastr.success(data.message);
                    $(".status").text("PAID");
                }
            })
            .fail(function() {
                console.log("error");
            });
        });
        $(".resend-invoice").click(function(){
            var order_id = '{{$id}}';
            $.ajax({
                url: '{{route('resend-invoice')}}',
                type: 'POST',
                dataType: 'html',
                data: {
                    order_id:order_id,
                    _token: '{{csrf_token()}}'
                },
            })
                .done(function(data) {
                    data = JSON.parse(data);
                    if(data.status == 'error'){
                        toastr.error(data.message);
                    }else{
                        toastr.success(data.message);
                    }
                    console.log(data);
                })
                .fail(function() {
                    console.log("error");
                });
        });

        //  GET NAME FILES
        $(document).on('change','#upload_file',function(e){

            var names = [];
            var name_html = "";

            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push($(this).get(0).files[i].name);
                name_html += $(this).get(0).files[i].name + "<br>";

            }
            $("#file_names").html(name_html);
        })
    });
</script>
@endpush
