@extends('layouts.app')
@section('content-title')
    SELLERS REPORT
@endsection
@push('styles')
<style type="text/css" media="screen">
    .date{
        width:40px;
        line-height:30px;
        border:1px solid #ebecef;
        border-radius: 5px 5px;
        box-shadow: 5px 5px #c1c0c0;
        font-size: 14px;
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
{{-- MODAL FOR CALL LOG --}}
 <div class="modal fade" id="call-log-modal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 90%">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title text-info">CALL LOG</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
            <form id="call-log-form">
                <div class="form-group col-md-12 row">
                    <div class="col-md-4">
                        <label for="">Created date</label>
                        <div class="input-daterange input-group" id="from_to_date">
                            <input type="text" class="input-sm form-control form-control-sm" id="from_date" name="from_date" />
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control form-control-sm" id="to_date" name="to_date" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">Seller</label>
                        <select name="extension" id="seller_id" onchange="getLogList()" class="form-control form-control-sm">
                            @foreach($sellers as $seller)
                                <option value="{{$seller->user_phone_call}}" user-id={{$seller->user_id}}>{{$seller->getFullname()."(".$seller->user_nickname.")-".$seller->user_target_sale}}</option>
                            @endforeach
                        </select>
                    </div><div class="col-md-3">
                        <label for="">InOutInternal</label>
                        <div class="form-inline">
                            <div class="custom-control custom-checkbox mb-3">
                              <input type="checkbox" class="custom-control-input" id="In" value="In" name="in_out_internal[]">
                              <label class="custom-control-label" for="In">In </label>
                            </div>
                            <div class="custom-control custom-checkbox mb-3">
                              <input type="checkbox" class="custom-control-input" id="Out" value="Out" name="in_out_internal[]">
                              <label class="custom-control-label" for="Out">Out </label>
                            </div>
                            <div class="custom-control custom-checkbox mb-3">
                              <input type="checkbox" class="custom-control-input" id="Internal" value="Internal" name="in_out_internal[]">
                              <label class="custom-control-label" for="Internal">Internal</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 " style="position: relative;">
                        <div style="position: absolute;top: 50%;" class="">
                            <input type="button" class="btn btn-primary btn-sm" id="search_call_log" value="Search">
                        </div>
                    </div>
                </div>
            </form>
            <div class="row py-2 px-2" id="log_list">
                
            </div>
            <div class="content-call-log">
            </div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
    <div class="table-responsive">
        <form id="search-form">
            <div class="form-group col-md-12 row">
                <div class="col-md-4">
                    <label for="">Created date</label>
                    <div class="input-daterange input-group" id="created_at">
                        <input type="text" class="input-sm form-control form-control-sm" value="{{today()->subMonths(6)->format('m/d/Y')}}" id="start_date" name="start" />
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input-sm form-control form-control-sm" value="{{today()->format('m/d/Y')}}" id="end_date" name="end" />
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">Seller</label>
                    <select name="seller" id="seller_id" class="form-control form-control-sm">
                        <option value="">--All--</option>
                        @foreach($sellers as $seller)
                            <option value="{{$seller->user_id}}">{{$seller->getFullname()."(".$seller->user_nickname.")"}}</option>
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
        <table class="table table-sm table-striped table-hover" id="dataTableAllService" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Fullname</th>
                <th>Email</th>
                <th>Total Assigned Customers</th>
                <th>Total Serviced Customers</th>
                <th>Total Orders</th>
                <th>Total Discount($)</th>
                <th>Total Charged($)</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
    getLogList();
        $(document).ready(function() {
            $("#created_at,#from_to_date").datepicker({});
            var table = $('#dataTableAllService').DataTable({
                // dom: "lBfrtip",
                // order:[[6,"desc"]],
                processing: true,
                serverSide: true,
                buttons: [
                    {
                        text: '<i class="fas fa-upload"></i> Export',
                        className: "btn-sm export",
                        action: function ( e, dt, node, config ) {
                            document.location.href = "{{route('report.sellers.export')}}";
                        }
                    },
                    @if(\Gate::allows('permission','call-log-admin') || \Gate::allows('permission','call-log-seller'))
                    {
                        text: '<i class="fas fa-address-book"></i> Call Log',
                        className: "btn-sm call_log",
                        action: function ( e, dt, node, config ) {
                            document.location.href = "javascript:void(0)";
                        }
                    }
                    @endif
                ],
                ajax:{ url:"{{ route('report.sellers.datatable') }}",
                    data: function (d) {
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                        // d.address = $("#address").val();
                        d.seller_id = $("#seller_id").val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id',class:'text-center' },
                    { data: 'user_nickname', name: 'user_nickname' },
                    { data: 'user_fullname', name: 'user_fullname', class:'text-capitalize'},
                    { data: 'email', name: 'email' ,},
                    { data: 'total_assigned', name: 'total_assigned',class:'text-right' },
                    { data: 'total_serviced', name: 'total_serviced',class:'text-right' },
                    { data: 'total_orders', name: 'total_orders',class:'text-right' },
                    { data: 'total_discount', name: 'total_discount',class:'text-right' },
                    { data: 'total_charged', name: 'total_charged',class:'text-right' },

                ],
            });

            $("#search-button").click(function(){
                table.draw();
            });
            $("#reset-btn").on('click',function(e){
                $(this).parents('form')[0].reset();
                e.preventDefault();
                table.ajax.reload(null, false);
            });
            $(document).on('click','.call_log',function(){
                // let phone = $(this).attr('phone');
                $("#call-log-modal").modal('show');
            });
            $("#search_call_log").click(function(){

                var formData = new FormData($(this).parents('form')[0]);

                formData.append('_token','{{ csrf_token() }}');
                ableProcessingLoader();

                $.ajax({
                    url: '{{ route('report.sellers.call_history') }}',
                    type: 'POST',
                    dataType: 'html',
                    processData: false,
                    contentType: false,
                    data: formData,
                })
                .done(function(data) {
                    data = JSON.parse(data);
                    if(data.status == 'error')
                        toastr.error(data.message);
                    else{
                        // console.log(data.data);return;
                        let content = $.parseXML(data.data);
                        $content = $( content );
                        console.log($content);
                        var content_html = '';
                        let row = "";

                        $($content).find('Table1').each(function(i, v) {
                            let rowIndex = $(v).find('rowIndex').text();
                            let id = $(v).find('id').text();
                            let direction = $(v).find('direction').text();
                            let startCall = $(v).find('startCall').text();
                            let startCall7 = $(v).find('startCall7').text();
                            let start = $(v).find('start').text();
                            let endCall = $(v).find('endCall').text();
                            let billsec = $(v).find('billsec').text();
                            let src = $(v).find('src').text();
                            let dst = $(v).find('dst').text();
                            let recordfile = "";
                            @if(\Gate::allows('permission','call-log-admin'))
                                recordfile = $(v).find('recordfile').text();
                            @endif
                            row +=`
                                <tr>
                                    <td>`+rowIndex+`</td>
                                    <td>`+id+`</td>
                                    <td>`+direction+`</td>
                                    <td>`+startCall+`</td>
                                    <td>`+startCall7+`</td>
                                    <td>`+start+`</td>
                                    <td>`+endCall+`</td>
                                    <td>`+billsec+`</td>
                                    <td>`+src+`</td>
                                    <td>`+dst+`</td>
                                    <td>`+recordfile+`</td>
                                </tr>
                            `;
                            // $(".content-call-log").html($(this).find('recordfile').text());
                        });
                        content_html = `
                            <table class="table table-hover table-stripped">
                                <thead>
                                    <tr class="thead-light">
                                        <th>RowIndex</th>
                                        <th>ID</th>
                                        <th>Direction</th>
                                        <th>StartCall</th>
                                        <th>StartCall7</th>
                                        <th>Start</th>
                                        <th>EndCall</th>
                                        <th>BillSec</th>
                                        <th>src</th>
                                        <th>dst</th>
                                        <th>recordfile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    `+row+`
                                </tbody>
                            </table>
                        `;
                        // alert(id);
                        $(".content-call-log").html(content_html);
                    }
                    // console.log(data);
                })
                .fail(function() {
                    toastr.error('Failed!');
                })
                .always(function() {
                    unableProcessingLoader();
                });
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
            function getLogList(){
                let user_id = $("#seller_id :selected").attr('user-id');

                $.ajax({
                    type: "POST",
                    url: "{{route('report.sellers.log_list')}}",
                    data: {
                        user_id:user_id,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'html',
                    success: function (data) {
                        data = JSON.parse(data);
                        if(data.status == 'error')
                            toastr.error(data.message);
                        else{
                            $("#log_list").html(data.log_list);
                        }
                    }
                });
            }
    </script>
@endpush
