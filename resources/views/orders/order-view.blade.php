@extends('layouts.app')
@section('title')
    My Orders
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
<div class="modal fade" id="add-comment-modal" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><b>Add Comment</b></h6>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <form  enctype="multipart/form-data" accept-charset="utf-8">
                @csrf()
                <textarea hidden id="summernote" class="form-control form-control-sm" name="note"></textarea>
                <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile()" placeholder="">
                <input type="file" hidden id="file_image_list" multiple name="file_image_list[]">
                <p>(The maximum upload file size: 100M)</p>
                <div style="height: 10px" class="bg-info">
                </div>
                <hr style="border-top: 1px dotted grey;">
                <p class="text-primary">An email notification will send to web@dataaeglobal.com</p>
                 <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Add CC:</div>
                    </div>
                    <input type="text" class="form-control" name="email_list" id="email_list" placeholder="">
                  </div>
                <p>CC Multiple Email for example:<i> email_1@gmail.com;email_2@gmail.com</i></p>
                <button type="botton" class="btn btn-sm btn-primary submit-comment">Submit Comment</button>
            </form>
        </div>
      </div>
      
    </div>
  </div>
{{-- END MODAL COMMENT --}}
<div class="table-responsive">
	<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">ORDER INFORMATON #{{$id}}</h4>
    <table class="table table-striped mt-4" id="dataTableAllCustomer" widtd="100%" cellspacing="0">
        <tbody>
            <tr>
                <td>ORDER</td>
                <td>ORDER STATUS</td>
                <td>ORDER DATE</td>
                <td>ORDER SELLER</td>
            </tr>
            <tr>
                <th>#{{$id}}</th>
                <th>{{($order_info->csb_status==0?"NOTPAYMENT":"PAID")}}</th>
                <th>{{$order_info->created_at}}</th>
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
                <td>CUSTOMER(View Detail)</td>
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
            <tr>
                <td colspan="2">ORDER NOTES: {{$order_info->csb_note}}</td>
                <td>
                    <button class="btn btn-sm btn-info">PRINT INVOICE</button>
                    <button class="btn btn-sm btn-info">RESEND INVOICE</button>
                </td>
                <td class="align-left"><i class="text-primary">Last sent invoice:</i></td>
            </tr>
        </tbody>
    </table>
    <table class="table mt-4 table table-hover" id="service-datatable" widtd="100%" cellspacing="0">
        <thead class="thead-light">
            <tr>
                <th style="width: 10%">SERVICE NAME</th>
                <th class="text-center" style="width: 10%">PRICE($)</th>
                <th class="text-center" style="width: 10%">ACTION</th>
                <th style="width: 70%">SERVICE ORDER FORM</th>
            </tr>
        </thead>
        
    </table>
    <table class="table mt-4 table-hover table-bordered" id="" widtd="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr>
                <th>TASK#</th>
                <th>SERVICE</th>
                <th class="text-center">PRIORITY</th>
                <th class="text-center">STATUS</th>
                <th class="text-center">DATE START</th>
                <th class="text-center">DATE END</th>
                <th class="text-center">%COMPLETE</th>
                <th class="text-center">ASSIGNEE</th>
                <th class="text-center">LAST UPDATE</th>
                <th class="text-center">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($task_list as $task)
            <tr class="text-center">
                <td><a href="" title="">#{{$task->id}}</a></td>
                <td>{{$task->subject}}</td>
                <td>{{\App\Helpers\GeneralHelper::getPriorityTask()[$task->priority]}}</td>
                <td>{{\App\Helpers\GeneralHelper::getStatusTask()[$task->status]}}</td>
                <td>{{$task->date_start!=""?\Carbon\Carbon::parse($task->date_start)->format('m/d/Y'):""}}</td>
                <td>{{$task->date_end!=""?\Carbon\Carbon::parse($task->date_end)->format('m/d/Y'):""}}</td>
                <td>{{$task->complete_percent}}</td>
                <td>{{$task->getUser->user_nickname}}</td>
                <td class="text-left">{{\Carbon\Carbon::parse($task->updated_at)->format('m/d/Y h:i A')}} by {{$task->user_nickname}}</td>
                <td class="text-primary add-comment" order_id="{{$id}}" task_id="{{$task->id}}" ><a href="javascript:void(0)" title="">Add comment</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table class="table mt-4 table-bordered table-hover" id="tracking_history" width="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr>
                <th hidden></th>
                <th style="width:20%">TRACKING HISTORY</th>
                <th style="width:10%"></th>
                <th style="width:70%"></th>
            </tr>
        </thead>
    </table>
</div>
<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">ADD NEW COMMENT</h4>
<form  enctype="multipart/form-data" accept-charset="utf-8">
    @csrf()
    <textarea  id="summernote2" class="form-control form-control-sm"  name="note"></textarea>
    <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile2()" placeholder="">
    <input type="file" hidden id="file_image_list_2" multiple name="file_image_list[]">
    <p>(The maximum upload file size: 100M)</p>
    <div style="height: 10px" class="bg-info">
    </div>
    <hr style="border-top: 1px dotted grey;">
    <p class="text-primary">An email notification will send to web@dataaeglobal.com</p>
     <div class="input-group mb-2 mr-sm-2">
        <div class="input-group-prepend">
          <div class="input-group-text">Add CC:</div>
        </div>
        <input type="text" class="form-control" name="email_list" id="email_list" placeholder="">
      </div>
    <p>CC Multiple Email for example:<i> email_1@gmail.com;email_2@gmail.com</i></p>
    <button type="botton" class="btn btn-sm btn-primary submit-comment">Submit Comment</button>
</form>

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

        $('#summernote').summernote();
        $('#summernote2').summernote();

        var table = $('#tracking_history').DataTable({
            // dom: "lBfrtip",
            order:[[0,'desc']],
            info: false,
            buttons: [
            ],  
            processing: true,
            serverSide: true,
            ajax:{ url:"{{ route('order-tracking') }}",
            data: function (d) {
                d.order_id = '{{$id}}'
            } 
        },
           columns: [
                    { data: 'created_at', name: 'created_at',class:'d-none' },
                    { data: 'user_info', name: 'user_info' },
                    { data: 'task', name: 'task',class: 'text-center' },
                    { data: 'content', name: 'content'},
                ],
        });
        var service_table = $('#service-datatable').DataTable({
            // dom: "lBfrtip",
            // order:[[0,'desc']],
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

        $('body').on('click', '.submit-comment', function(e){
            e.preventDefault();
            // var note = $("#summernote").val();
            // if(note == ""){
            //     toastr.error('Enter Comment');
            //     return;
            // }
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
                        table.draw();
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
            $("#summernote").val("");
            $("#add-comment-modal").modal('hide');
        }

        $(document).on("click",".input-form",function(){

            var input_form_type = $(this).attr('form_type_id');
            task_id = $(this).attr('task_id');
            var content_html = "";

            if(input_form_type == 1){
                content_html = `
                <div class="form-group">
                    <label for="google-link">Google Link</label>
                    <input type="text" class="form-control form-control-sm"  id="google-link" name="google_link" value="">
                </div>
                <div class="form-group">
                    <label for="worker-name">Tên Thợ nails</label>
                    <input type="text" class="form-control form-control-sm"  id="worker-name" name="worker_name" value="">
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="number_of_stars" class="col-md-6">Number of Stars</label>
                            <input type="number" class="form-control form-control-sm col-md-6"  id="number_of_stars" name="star" value="">
                        </div>
                        <div class="form-group row">
                            <label for="number_of_reviews" class="col-md-6">Số Reviews hiện tại</label>
                            <input type="number" class="form-control form-control-sm col-md-6"  id="number_of_reviews" name="current_review" value="">
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6" for="offer_of_reviews">Số reviews yêu cầu</label>
                            <input type="number" class="form-control form-control-sm col-md-6" id="offer_of_reviews" name="order_review" value="">
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6" for="offer_of_reviews">Complete Date</label>
                            <input type="text" class="form-control form-control-sm col-md-6" id="datepicker_form" name="complete_date" >
                        </div>
                    </div>
                    <div class="col-md-1" style="border-right: .5px dashed grey">
                        
                    </div>
                    <div class="col-md-5">
                        <input type="file" hidden id="file" name="" value="">
                        <input type="button" class="btn btn-sm btn-secondary" onclick="uploadFile()" value="Upload attachment files" name="">
                        <input type="file" id="upload_file" hidden class="" value="" name="list_file[]" multiple>
                    </div>
                        
                </div>
                <div class="form-group">
                    <label for="note">Notes</label>
                    <textarea class="form-control form-control-sm" name="note" rows="3"></textarea>
                </div>
                `;
            }
            if(input_form_type == 2){
                content_html = `
                <label for="product_name">Tên sản phẩm</label>
                <input type="text" class="form-control form-control-sm" id="product_name" name="product_name" value="" placeholder="">
                <label for="main_color">Màu chủ đạo</label>
                <input type="text" class="form-control form-control-sm" id="main_color" name="product_name" value="" placeholder="">
                <label for="kind_of">Thể loại hoặc phong cách khách hàng hướng đến</label>
                <input type="text" class="form-control form-control-sm" id="kind_of" name="style" value="" placeholder="">
                <label for="facebook_link">Facebook Link</label>
                <input type="text" class="form-control form-control-sm" id="facebook_link" name="product_name" value="" placeholder="">
                <label for="website">Website</label>
                <input type="text" class="form-control form-control-sm" id="website" name="product_name" value="" placeholder="">
                <div class="border border-secondary p-2 rounded">
                    <p>Upload Logo images or file</p>
                    <button class="btn btn-sm btn-secondary">Upload attachment</button><br><br>
                    <input type="file" hidden id="file"  name="">
                    <input type="text" class="form-control form-control-sm" id="file_name" name="">
                </div>
                `;
            }
            if(input_form_type == 3){
                content_html = `
                <div class="form-group">
                <label for="facebook-link">Facebook Link</label>
                <input type="text" class="form-control form-control-sm"  id="facebook-link" name="" value="">
                </div>
                <div class="form-group">
                    <label for="worker-name">Promotion</label>
                    <input type="text" class="form-control form-control-sm"  id="promotion" name="" value="">
                </div>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="number_of_stars" class="col-md-6">Số lượng bài viết</label>
                            <input type="text" class="form-control form-control-sm col-md-6"  id="number_of_stars" name="" value="">
                        </div>
                        <div class="form-group row">
                            <label for="add_admin" class="col-md-6">Đã add admin chưa?</label>
                            <input type="checkbox" class="col-md-6"  id="add_admin" name="" value="">
                        </div>
                        <div class="form-group row">
                            <label for="facebook_username" class="col-md-6">Facebook Username</label>
                            <input type="text" class="form-control form-control-sm col-md-6"  id="facebook_username" name="" value="">
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6" for="facebook_password">Facebook Password</label>
                            <input type="text" class="form-control form-control-sm col-md-6" id="facebook_password" name="" value="">
                        </div>
                       <div class="form-group row">
                            <label for="add_admin" class="col-md-6">Có lấy được hình ảnh?</label>
                            <input type="checkbox" class="col-md-6"  id="add_admin" name="" value="">
                        </div>
                    </div>
                    <div class="col-md-1" style="border-right: .5px dashed grey">
                        
                    </div>
                    <div class="col-md-5">
                        <input type="file" hidden id="file" name="" value="">
                        <input type="button" class="btn btn-sm btn-secondary" value="Upload attachment files" name="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="note">Notes</label>
                    <textarea class="form-control form-control-sm" name="" rows="3"></textarea>
                </div>
                `;
            }
            if(input_form_type == 4){
                content_html = `
                <label for="domain">Domain</label>
                <input type="text" id="domain" class="form-control form-control-sm" name="">
                <div class="col-md-12 row mt-2">
                    <div class="col-md-6 row pr-3" style="border-right: .5px dashed grey">
                        <label class="col-md-2" for="theme">Theme</label>
                        <input type="text" id="theme" class="form-control form-control-sm col-md-10" name="">
                        <input type="checkbox" class="col-md-2 mt-1" id="show_price" name="">
                        <label for="show_price" class="col-md-10 mt-1">Is show Service Price?</label>
                    </div>
                    <div class="col-md-6 pl-3">
                        <button type="button" id="file_name" class="btn btn-sm btn-secondary">Upload attachment files</button>
                        <input type="file" hidden id="file" name="">
                        <input type="text" id="file_name" class="form-control form-control-sm mt-1" name="">
                    </div>
                </div>
                <h5><b>BUSINESS INFO</b></h5>
                <div class="col-md-12 row">
                    <label class="col-md-3" for="business_name">Business Name</label>
                    <input type="text" class="col-md-3 form-control form-control-sm" id="business_name" name="">
                    <label class="col-md-3" for="business_phone">Business Phone</label>
                    <input type="number" class="col-md-3 form-control form-control-sm" id="business_phone" name="">
                    <label for="email" class="col-md-3">Email</label>
                    <input type="email" id="email" class="col-md-3 form-control form-control-sm" name="">
                    <label for="address" class="col-md-3">Address</label>
                    <input type="email" id="address" class="col-md-3 form-control form-control-sm" name="">
                </div>
                <label for="" for="note">Note</label>
                <textarea name="" id="note" rows="3" class="form-control form-control-sm"></textarea>
                `;
            }
            $("#content-form").html(content_html);
            $("#datepicker_form").datepicker({
              todayHighlight: true,
              setDate: new Date(),
            });
            $("#modal-input-form").modal('show');
        });
        $(".add-comment").click(function(){

            task_id = $(this).attr('task_id');

            $("#add-comment-modal").modal('show');
        });
        $(document).on("click",".file-comment",function(){
            $(this).parent('form').submit();
        });

        $(".submit-input-form").click(function(){

            var formData = new FormData($(this).parents('form')[0])
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
                data = JSON.parse(data);
                if(data.status == 'error'){
                    toasrt.error(data.message);
                }else{
                    toastr.success(data.message);
                    $("#content-form").html("");
                    $("#modal-input-form").modal('hide');

                }
                console.log(data);
            })
            .fail(function() {
                console.log("error");
            });
            

        })
    });
</script>
@endpush