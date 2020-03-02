@extends('layouts.app')
@section('content-title')
    DETAIL CUSTOMER
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
        .loader {
          border: 8px solid #f3f3f3;
          border-radius: 50%;
          border-top: 8px solid blue;
          border-right: 8px solid green;
          border-bottom: 8px solid red;
          border-left: 8px solid pink;
          width: 80px;
          height: 80px;
          -webkit-animation: spin 2s linear infinite; /* Safari */
          animation: spin 2s linear infinite;
          position: fixed;
          top: 50%;
          left: 50%;
          z-index: 100000;
          display: none; 
        }
        /* Safari */
        @-webkit-keyframes spin {
          0% { -webkit-transform: rotate(0deg); }
          100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-6">
            <span class="col-md-4"><b>FullName:</b></span>
            <span class="col-md-8 text-uppercase">{{$template_customer_info->getFullname()}}</span><br>
            <span class="col-md-4"><b>Cell Phone:</b></span>
            <span class="col-md-8">{{$template_customer_info->ct_cell_phone}}</span><br>
            <span class="col-md-4"><b>Business Phone:</b></span>
            <span class="col-md-8">{{$template_customer_info->ct_business_phone}}</span><br>
            <span class="col-md-4"><b>Business Name:</b></span>
            <span class="col-md-8">
                @foreach($main_customer_info->getPlaces as $key => $place)
                    {{ $key+1 }} - <span class="text-uppercase text-info">{{$place->place_name}}</span>
                @endforeach
            </span><br>
        </div>
        <div class="col-md-6 row">
            <div class="col-md-4"><b>Status:</b></div>
            <div class="col-md-8 text-info">SERVICE</div>
            <div class="col-md-4"><b>Seller:</b></div>
            <div class="col-md-8">
                @foreach($seller_list as $seller)
                    <span class="">{{ $seller->user_lastname." ".$seller->user_firstname }}</span>
                    <span class="">-{{ $seller->user_email }}</span><br>
                @endforeach
            </div>
        </div>
        <div class="col-md-12">
            <span class="col-md-12"><b>Note:</b></span><br>
            <div class="col-md-12" style="padding-left: 50px;">{{$template_customer_info->ct_note}}</div>
        </div>
    </div>

<div class="table-responsive mt-5">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-info" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">SERVICE ORDERS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-info" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">PLACE ORDERS</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <table class="table table-hover table-sm table-bordered" id="task-datatable" widtd="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr class="text-center">
                <th>ORDER ID</th>
                <th>PLACE NAME</th>
                <th>ORDER DATE</th>
                <th>Services</th>
                <th>Subtotal($)</th>
                <th>Discount(&)</th>
                <th>Charge</th>
                <th>Tasks</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($main_customer_info->getOrder as $order)
            @php
            $cs_list = "";
            $task_list = "";
            $status = 0;
            $count = 0;
            $order_status = 0;

            $combo_service_arr = explode(";",$order->csb_combo_service_id);
            $order_detail_list = \App\Models\MainComboService::whereIn('id',$combo_service_arr)->get();
            foreach ($order_detail_list as $order_detail){
                $cs_list .= '<span class="text-info">'.$order_detail->cs_name." - $".$order_detail->cs_price.'</span><br>';
            }
             foreach($order->getTasks as $task_detail){
                    $task_list .= "<a href='".route('task-detail',$task_detail->id)."'><span>#".$task_detail->id."-".$task_detail->subject."<span></a><br>";
                    $status += $task_detail->status;
                    $count++;
                }
             $seller_id = $order->created_by;
             if($count != 0)
                $order_status= $status/$count;
            //GET PLACE OF ORDER
            $place_name = App\Models\PosPlace::where('place_id',$order->csb_place_id)->first();

            @endphp
            @if($order_status != 0)
            <tr>
                <td class="text-center"><a href="{{route('order-view',$order->id)}}">#{{$order->id}}</a></td>
                <td class="text-info">{{ isset($place_name)?$place_name->place_name:""}}</td>
                <td class="text-center">{{format_datetime($order->created_at)}}</td>
                <td>{!! $cs_list !!}</td>
                <td class="text-right">${{$order->csb_amount?$order->csb_amount:0}}</td>
                <td class="text-right">${{$order->csb_amount_deal!=""?$order->csb_amount_deal:0}}</td>
                <td class="text-right">${{$order->csb_charge!=""?$order->csb_charge:0}}</td>
                <td>{!! $task_list !!}</td>
                <td class="text-center">{{getOrderStatus()[$order_status]}}</td>
            </tr>
            @endif
        @endforeach
        </tbody>
    </table>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <table class="table table-hover table-sm table-bordered" id="place-order-datatable" widtd="100%" cellspacing="0">
                <thead  class="thead-light">
                <tr>
                    <th>Place Name</th>
                    <th>Service Name</th>
                    <th class="text-center">Expired date</th>
                    <th class="text-center">Created At</th>
                    <th class="text-center">Order</th>
                </tr>
                </thead>
                <tbody>
                @foreach($place_list as $key => $places)
                    <tr>
                        <td class="text-uppercase text-info">{{ $key }}</td>
                        <td class="text-info">
                            @foreach($places as $place)
                                {{$place->getComboService->cs_name}}<br>
                            @endforeach
                        </td>
                        <td class="text-center">
                            @foreach($places as $place)
                                {{format_date($place->cs_date_expire)}}<br>
                            @endforeach
                        </td>
                        <td class="text-center">
                            @foreach($places as $place)
                                {{format_datetime($place->customer_created_at)}}<br>
                            @endforeach
                        </td>
                        <td class="text-center text-info"><a href="{{route('add-order',$id)}}"><i class="fas fa-shopping-cart"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table mt-4 table-hover table-bordered" id="tracking-datatable" widtd="100%" cellspacing="0">
        <thead  class="thead-light">
        <tr>
            <th hidden></th>
            <th style="width: 20%">CUSTOMER COMMENTS</th>
            <th style="width: 80%"></th>
        </tr>
        </thead>
    </table>
</div>
<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info mt-5">ADD NEW COMMENT</h4>
<form  enctype="multipart/form-data" accept-charset="utf-8" id="comment-form">
    @csrf()
    <textarea  id="summernote2" class="form-control form-control-sm"  name="note" placeholder="Text Content..."></textarea>
    <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile2()" placeholder=""><br>
    <span id="file_names"></span>
    <span id="total_file_size"></span>
    <input type="file" hidden id="file_image_list_2" multiple name="file_image_list[]">
    <input type="hidden" id="email_seller" name="email_seller" value="">
    <p class="text-danger">- The maximum upload file size: 50M<br>- The maximum amount of files: 20 files</p>
    <div style="height: 10px" class="bg-info">
    </div>
    <input type="hidden" value="" id="receiver_id">
    <hr style="border-top: 1px dotted grey;">
            Send this comment as notification to email:
    <div class="input-group mb-2 mr-sm-2">
        <div class="input-group-prepend">
            <div class="input-group-text">Add CC:</div>
        </div>
        <select name="email_list[]" id="email_list" class="form-control form-control-m" multiple>
            @foreach($user_list as $user)
                <option value="{{ $user->user_email }}">{{ $user->user_nickname."( ".$user->user_email." )" }}</option>
            @endforeach
        </select>
       
    </div>
    
    <button type="botton" class="btn btn-sm btn-primary submit-comment">Submit Comment</button>
</form>
@endsection
@push('scripts')
    <script>
        function getFile2(){
            $("#file_image_list_2").click();
        }
        $(document).ready(function () {

            var file_size_total = 0;
            var file_image_list = [];

            $("#email_list").multiselect();

            var table = $('#tracking-datatable').DataTable({
                // dom: "lBfrtip",
                order:[[0,'desc']],
                buttons: [
                ],
                // processing: true,
                serverSide: true,
                ajax:{ url:"{{ route('customer-tracking') }}",
                    data: function (d) {
                        d.customer_id = '{{$id}}'
                    }
                },
                columns: [

                    { data: 'created_at', name: 'created_at',class:'d-none' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'content', name: 'content'},
                ],
            });
            $('body').on('click', '.submit-comment', function(e){
                e.preventDefault();
                var formData = new FormData($(this).parents('form')[0]);
                formData.append('customer_id',{{$id}});
                formData.append('_token','{{csrf_token()}}');
                amount_files = file_image_list.length;

                if(amount_files > 20){
                    toastr.error("Amount of files maximum is 20 files");
                    return;
                }
                if(file_size_total > 50){
                    toastr.error('Total Files Size maximum is 50 MB!');
                    return;
                }
                ableProcessingLoader();

                $.ajax({
                    url: '{{route('post-comment-customer')}}',
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
                        // console.log(data);return;
                        let message = "";
                        if(data.status == 'success'){
                            toastr.success(data.message);
                            table.draw();
                            clearView();
                        }else{
                            if($.type(data.message) == 'string'){
                                toastr.error(data.message);
                            }else{
                                $.each(data.message, function(index, val) {
                                    message += val+'\n';
                                });
                                toastr.error(message);
                            }
                        }
                        unableProcessingLoader();
                    },
                    fail: function() {
                        console.log("error");
                        unableProcessingLoader();
                    }
                });
                return false;
            });
            function clearView(){
                 $("#email_list_2").val("");
                $('#summernote2').val("");
                $("#file_names").text("");
                file_image_list = [];
                $('#comment-form')[0].reset();
                $("#total_file_size").text("");
            }
            $(document).on("click",".file-comment",function(){
                $(this).parent('form').submit();
            });

            $(document).on('change','#file_image_list_2',function(e){
                file_size_total = 0;
                file_image_list = Array.from(e.target.files);
                console.log(file_image_list);

                var names = [];
                var name_html = "";
                var stt = 0;

                for (var i = 0; i < $(this).get(0).files.length; ++i) {
                    stt = i +1;
                    names.push($(this).get(0).files[i].name);
                    file_size_total += parseFloat($(this).get(0).files[i].size/1048576);
                    name_html += "<span>"+"<span class='text-danger '>"+stt+"-</span>"+$(this).get(0).files[i].name+ "</span><br>";
                }
                $("#file_names").html(name_html);
                file_size_total = file_size_total.toFixed(2); 
                $("#total_file_size").html("<b>TOTAL FILES SIZE: "+file_size_total+" MB<br>TOTAL FILES: "+stt+" files</b>");
                console.log(file_size_total);
            });

        function ableProcessingLoader(){
            $('.loader').css('display','inline');
            $("#content").css('opacity',.5);
        }
        function unableProcessingLoader(){
            $('.loader').css('display','none');
            $("#content").css('opacity',1);
        }
        });
    </script>
@endpush
