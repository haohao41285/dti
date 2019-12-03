@extends('layouts.app')
@section('title')
    Reviews Report
@endsection
@section('content')
    <div class="table-responsive">
        <h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">REVIEWS REPORT</h4>
        <form id="customer_form">
            <div class="form-group col-md-12 row">
                <div class="col-md-4">
                    <label for="">Created date</label>
                    <div class="input-daterange input-group" id="created_at">
                        <input type="text" class="input-sm form-control form-control-sm" id="start_date" name="start_date" />
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input-sm form-control form-control-sm" id="end_date" name="end_date" />
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">User</label>
                    <select id="team_id" name="team_id" class="form-control form-control-sm">
                        @foreach ($user_list as $key =>  $user)
                            <option value="{{$user->user_id}}">{{$user->getFullname()."(".$user->user_nickname.")"}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Status</label>
                    <select id="status-customer" name="status_customer" class="form-control form-control-sm">
                        <option value="">-- ALL --</option>
                        @foreach (getReviewStatus() as $key =>  $element)
                            <option value="{{$key}}">{{$element}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2 " style="position: relative;">
                    <div style="position: absolute;top: 50%;" class="">
                        <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
                        <input type="button" class="btn btn-secondary btn-sm" id="reset-btn" value="Reset">
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-striped table-hover" id="dataTableReviews" width="100%" cellspacing="0">
            <thead>
                <th>ID</th>
                <th>User</th>
                <th>Total Reviews</th>
                <th>Successfully Total</th>
                <th>Failed Total</th>
                <th>%Complete</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#created_at").datepicker({});
            var team_id = $("#team_id").val();
            var table = $('#dataTableReviews').DataTable({
                // dom: "lBfrtip",
                order:[[6,"desc"]],
                processing: true,
                serverSide: true,
                buttons: [
                    // {
                    //     text: '<i class="fas fa-exchange-alt"></i> Move Customers',
                    //     className: "btn-sm move-customers"
                    // },
                        {{--{--}}
                        {{--    text: '<i class="fas fa-download"></i> Import',--}}
                        {{--    className: "btn-sm import-show"--}}
                        {{--},--}}
                    {
                        text: '<i class="fas fa-upload"></i> Export',
                        className: "btn-sm",
                        action: function ( e, dt, node, config ) {
                            document.location.href = "{{route('report.customers.export')}}"+"/"+team_id
                        }
                    }
                ],
                ajax:{ url:"{{ route('report.reviews.datatable') }}",
                    data: function (d) {
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                        d.user_id = $("#user_id :selected").val();
                    }
                },
                columns: [

                    { data: 'id', name: 'id',class:'text-center' },
                    { data: 'user', name: 'user' },
                    { data: 'total_reviews', name: 'total_reviews',class:'text-right'},
                    { data: 'successfully_total', name: 'successfully_total' ,class:'text-right'},
                    { data: 'failed_total', name: 'failed_total',class:'text-right' },
                    { data: 'percent_complete', name: 'percent_complete',class:'text-right' },
                ],
            });

            $("#reset").on('click',function(e){
                e.preventDefault();
                table.ajax.reload(null, false);
            });


            $(document).on("click",".view",function(){

                var customer_id = $(this).attr('customer_id');

                $.ajax({
                    url: '{{route('get-customer-detail')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        customer_id: customer_id,
                        my_customer: 1
                    },
                })
                    .done(function(data) {
                        if(data == 0){
                            toastr.error('Get Detaill Customer Error!');
                        }else{
                            data = JSON.parse(data);
                            console.log(data);
                            if(data.customer_list.ct_salon_name==null)data.customer_list.ct_salon_name="";
                            if(data.customer_list.ct_fullname==null)data.customer_list.ct_fullname="";
                            if(data.customer_list.ct_business_phone==null)data.customer_list.ct_business_phone="";
                            if(data.customer_list.ct_cell_phone==null)data.customer_list.ct_cell_phone="";
                            if(data.customer_list.ct_email==null)data.customer_list.ct_email="";
                            if(data.customer_list.ct_address==null)data.customer_list.ct_address="";
                            if(data.customer_list.ct_website==null)data.customer_list.ct_website="";
                            if(data.customer_list.ct_note==null)data.customer_list.ct_note="";
                            if(data.customer_list.ct_status==null)data.customer_list.ct_status="";

                            var place_service = "";
                            var content_table = "";
                            if(data.place_arr.length != 0){
                                $.each(data.place_arr, function(index, val) {

                                    content_table +=  `<tr>
                  <td>`+index+`</td>
                  <td>`+val+`</td>
                </tr>;`
                                });
                                place_service = `<table class="table table-hovered table-bordered" style="width: 100%">
              <thead>
                <tr>
                  <th>Places</th>
                  <th>Service</th>
                </tr>
              </thead>
              <tbody>
                `+content_table+`
              </tbody>
            </table>`;
                            }
                            var button = ``;
                            if(data.customer_list.ct_status === 'New Arrivals')
                                button = `<button type="button" id=`+data.customer_list.id+` class="btn btn-primary btn-sm get-customer">Get</button>`;
                            $("#content-customer-detail").html(`
            <div class="row pr-5 pl-5" >
          <div class="col-md-6">
              <div class="row">
                <span class="col-md-4">Business:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_salon_name+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Contact Name:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_fullname+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Business Phone:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_business_phone+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Cell Phone:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_cell_phone+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Email:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_email+`</b></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <span class="col-md-4">Address:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_address+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Website:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_website+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Note:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_note+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Created:</span>
                <p class="col-md-8"><b>`+data.customer_list.created_at+` by `+data.customer_list.user_nickname+`</b></p>
              </div>
              <div class="row">
                <span class="col-md-4">Status:</span>
                <p class="col-md-8"><b>`+data.customer_list.ct_status+`</b></p>
              </div>

            </div>
            `+place_service+`
            <div class="col-md-12">
              <div class="row float-right">
                `+button+`
                <button type="button" class="btn btn-danger btn-sm ml-2 close-customer-detail">Close</button>
              </div>
            </div>
        </div>
            `);
                            $("#viewModal").modal('show');
                        }
                    })
                    .fail(function() {
                        console.log("error");
                    });
            });
            //CLOSE MODAL DETAI CUSTOMER
            $(document).on('click','.close-customer-detail',function(){
                $("#viewModal").modal('hide');
                $("#content-customer-detail").html(``);
            });
            //GET CUSTOMER TO MY CUSTOMER
            $(document).on('click','.get-customer',function(){

                var customer_id = $(this).attr('id');

                $.ajax({
                    url: '{{route('add-customer-to-my')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {customer_id: customer_id},
                })
                    .done(function(data) {
                        if(data == 1){
                            $("#viewModal").modal('hide');
                            table.ajax.reload(null, false);
                        }else{
                            toastr.error('Getting Error! Check again!');
                        }
                    })
                    .fail(function() {
                        console.log("error");
                    });
            });
            $(document).on('click','.import-show',function(){
                $("#import-modal").modal("show");
            });
            $(".submit-form").click(function(){

                var begin_row = $("#begin_row").val();
                var end_row = $("#end_row").val();

                var formData = new FormData();
                formData.append('begin_row', begin_row);
                formData.append('end_row', end_row);
                formData.append('_token','{{csrf_token()}}')
                formData.append('check_my_customer',1)
                // Attach file
                formData.append('file', $('#file')[0].files[0]);

                $.ajax({
                    url: '{{route('import-customer')}}',
                    type: 'POST',
                    dataType: 'html',
                    data: formData,
                    contentType: false,
                    processData: false
                })
                    .done(function(data) {
                        data = JSON.parse(data);
                        if(data.status == 'success'){
                            $("#import-modal").modal('hide');
                            table.draw();
                            toastr.success(data.message);
                        }
                        else
                            toastr.error(data.message);
                        console.log(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });
            });
            $("#search-button").click(function(){
                table.draw();
            });
            $("#reset-btn").on('click',function(e){
                $(this).parents('form')[0].reset();
                table.ajax.reload(null, false);
            });

            $(".export-customer").click(function(){
                var formData = new FormData($("#customer_form")[0]);
                formData.append('_token','{{csrf_token()}}');

                $.ajax({
                    url: '{{route('report.customers.export')}}',
                    type: 'POST',
                    dataType: 'html',
                    processData: false,
                    contentType: false,
                    data: formData,
                })
                    .done(function(data) {
                        console.log(data);
                        return;
                        data = JSON.parse(data);
                        $("#totalNewCustomer").text(data.arrivals_total);
                        $("#totalServicedCustomer").text(data.serviced_total);
                        $("#totalAssignedCustomer").text(data.assigned_total);
                        $("#totalDisabledCustomer").text(data.disabled_total);

                    })
                    .fail(function() {
                        toastr.error('Get List User Failed!');
                    });
            })
        });
    </script>
@endpush
