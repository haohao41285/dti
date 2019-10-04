@extends('layouts.app')
@section('title')
    My Tasks
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

<h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info">CUSTOMER INFORMATION</h4>
    <div class="row">
        <div class="col-md-6">
            <span class="col-md-4"><b>FullName:</b></span>
            <span class="col-md-8 text-uppercase">{{$main_customer_info->present()->getFullname()}}</span><br>
            <span class="col-md-4"><b>Cell Phone:</b></span>
            <span class="col-md-8">{{$template_customer_info->ct_cell_phone}}</span><br>
            <span class="col-md-4"><b>Business Phone:</b></span>
            <span class="col-md-8">{{$main_customer_info->customer_phone}}</span><br>
            <span class="col-md-4"><b>Business Name:</b></span>
            <span class="col-md-8">
                @foreach($main_customer_info->getPlaces as $key => $place)
                    <span class="text-uppercase text-info">{{$place->place_name}}{{$key>0?",":""}}</span>
                @endforeach
            </span><br>
        </div>
        <div class="col-md-6">
            <span class="col-md-4"><b>Status:</b></span>
            <span class="col-md-8 text-info">SERVICE</span><br>
            <span class="col-md-4"><b>Seller:</b></span>
            <span class="col-md-8"><span class="fullname_seller"></span><span class="email_seller"></span></span><br>
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
            <table class="table table-hover table-bordered" id="task-datatable" widtd="100%" cellspacing="0">
        <thead  class="thead-light">
            <tr class="text-center">
                <th>ORDER ID</th>
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

            @endphp
            @if($order_status == 1 ||$order_status == 2)
            <tr>
                <td class="text-center"><a href="{{route('order-view',$order->id)}}">#{{$order->id}}</a></td>
                <td class="text-center">{{format_datetime($order->created_at)}}</td>
                <td>{!! $cs_list !!}</td>
                <td class="text-right">${{$order->csb_amount?$order->csb_amount:0}}</td>
                <td class="text-right">${{$order->csb_amount_deal!=""?$order->csb_amount_deal:0}}</td>
                <td class="text-right">${{$order->csb_charge!=""?$order->csb_charge:0}}</td>
                <td>{!! $task_list !!}</td>
                <td class="text-center">{{$order_status==1?"NEW":"PROCESSING"}}</td>
            </tr>
            @endif
        @endforeach
        </tbody>
    </table>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <table class="table table-hover table-bordered" id="place-order-datatable" widtd="100%" cellspacing="0">
                <thead  class="thead-light">
                <tr class="text-center">
                    <th>Place Name</th>
                    <th>Serivce Name</th>
                    <th>Expired date</th>
                    <th>Created At</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($place_service as $key => $places)
                    <tr>
                        <td class="text-uppercase">{{\App\Models\PosPlace::where('place_id',$key)->first()->place_name}}</td>
                        <td class="">
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
                                {{format_datetime($place->created_at)." by ".$place->getCreatedBy->user_nickname}}<br>
                            @endforeach
                        </td>
                        <td class="text-center"><a href="{{route('add-order',$id)}}"><button class="btn btn-sm btn-secondary">Order</button></a></td>
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
<form  enctype="multipart/form-data" accept-charset="utf-8">
    @csrf()
    <textarea  id="summernote2" class="form-control form-control-sm"  name="note"></textarea>
    <input type="button" class="btn btn-sm btn-secondary mt-2" name="" value="Upload attchment's file" onclick="getFile2()" placeholder="">
    <input type="file" hidden id="file_image_list_2" multiple name="file_image_list[]">
    <input type="hidden" id="email_seller" name="email_seller" value="">
    <p>(The maximum upload file size: 100M)</p>
    <div style="height: 10px" class="bg-info">
    </div>
    <hr style="border-top: 1px dotted grey;">
    <div class="input-group mb-2 mr-sm-2">
        <div class="input-group-prepend">
            Send this comment as notification to email:
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
        $(document).ready(function () {
            $('#summernote2').summernote();
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
                        // console.log(data);
                        // return;

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
            $(document).on("click",".file-comment",function(){
                $(this).parent('form').submit();
            });


            $.ajax({
                url: '{{route('get-seller')}}',
                type: 'GET',
                dataType: 'html',
                data: {
                    seller_id: {{$seller_id}},
                },
            })
                .done(function(data) {
                    data = JSON.parse(data);
                    if(data.status == 'error'){
                        toastr.error(data.message);
                    }else{
                        $(".email_seller").text(" ("+data.email+")");
                        $(".fullname_seller").text(data.fullname);
                        $("#email_seller").val(data.email);
                    }
                })
                .fail(function() {
                    toastr.error('Saving Error!');
                });
        });
    </script>
@endpush
