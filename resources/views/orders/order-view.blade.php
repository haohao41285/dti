@extends('layouts.app')
@section('title')
    My Orders
@endsection
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
        <div class="modal-body">
           {{--  <label for="product_name">Tên sản phẩm</label>
            <input type="text" class="form-control form-control-sm" id="product_name" name="product_name" value="" placeholder="">
            <label for="main_color">Màu chủ đạo</label>
            <input type="text" class="form-control form-control-sm" id="main_color" name="product_name" value="" placeholder="">
            <label for="kind_of">Thể loại hoặc phong cách khách hàng hướng đến</label>
            <input type="text" class="form-control form-control-sm" id="kind_of" name="product_name" value="" placeholder="">
            <label for="facebook_link">Facebook Link</label>
            <input type="text" class="form-control form-control-sm" id="facebook_link" name="product_name" value="" placeholder="">
            <label for="website">Website</label>
            <input type="text" class="form-control form-control-sm" id="website" name="product_name" value="" placeholder="">
            <div class="border border-secondary p-2 rounded">
                <p>Upload Logo images or file</p>
                <button class="btn btn-sm btn-secondary">Upload attachment</button><br><br>
                <input type="file" hidden id="file"  name="">
                <input type="text" class="form-control form-control-sm" id="file_name" name="">
            </div> --}}
{{-- FACEBOOK --}}
            {{-- <div class="form-group">
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
            </div> --}}
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary">Submit</button>
        </div>
      </div>
      
    </div>
  </div>
{{-- END MODAL --}}
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
    <table class="table table-horvered table-striped mt-4" id="" widtd="100%" cellspacing="0">
        <tbody>
            <tr>
                <td>SERVICE NAME</td>
                <td class="text-center">PRICE($)</td>
                <td class="text-center">ACTION</td>
                <td>SERVICE ORDER FORM</td>
            </tr>
            @foreach($service_list as $service)
            <tr>
                <th>{{$service->cs_name}}</th>
                <th class="text-center">{{$service->cs_price}}</th>
                <th class="text-center">
                    <button type="button" form_type_id="{{$service->cs_form_type}}" class="btn btn-sm btn-secondary input-form">INPUT FORM</button>
                </th>
                <th></th>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

    $(document).ready(function() {
        $("#datepicker").datepicker({
          todayHighlight: true,
          setDate: new Date(),
        });
        $(".input-form").click(function(){

            var input_form_type = $(this).attr('form_type_id');
            var content_html = "";

            // if(input_form_type == 1){
            //     content_html = `modal-content`;
            // }
            $("#modal-input-form").modal('show');
        });
    });
</script>
@endpush