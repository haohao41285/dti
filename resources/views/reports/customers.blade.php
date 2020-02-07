@extends('layouts.app')
@section('content-title')
    CUSTOMERS REPORT
@endsection
@section('content')
    <div class="table-responsive">
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
                <div class="col-md-2">
                    <label for="">Address</label>
                    <input type="text" id="address" name="address" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label for="">Status</label>
                    <select id="status-customer" name="status_customer" class="form-control form-control-sm">
                        <option value="">-- ALL --</option>
                        @foreach ($status as $key =>  $element)
                            <option value="{{$key}}">{{$element}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Team</label>
                    <select id="team_id" name="team_id" class="form-control form-control-sm">
                        @foreach ($teams as $key =>  $team)
                            <option value="{{$team->id}}">{{$team->team_name}}</option>
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


        <div class="my-2" style="background: #cdcdcd; width: 100%; color: #342f2f;">
            <table cellspacing="6" cellpadding="6" border="0">
                <tbody>
                    <tr>
                        <td style="padding-right: 30px;">TOTAL NEW: <span id="totalNewCustomer" class="text-danger">0</span></td>
                        <td style="padding-right: 30px;">TOTAL SERVICED: <span id="totalServicedCustomer" class="text-danger">0</span></td>
                        <td style="padding-right: 30px;">TOTAL ASSIGNED: <span id="totalAssignedCustomer" class="text-danger">0</span></td>
                        <td style="padding-right: 30px;">TOTAL DISABLED: <span id="totalDisabledCustomer" class="text-danger">0</span></td>
                        <td style="padding-right: 30px;">TOTAL DISCOUNT($): <span id="totalDiscount" class="text-danger">0</span></td>
                        <td style="padding-right: 30px;">TOTAL CHARGED($): <span id="totalCharged" class="text-danger">0</span></td>

                    </tr>
                </tbody>
            </table>
        </div>
        <table class="table table-sm table-striped table-hover" id="dataTableAllCustomer" width="100%" cellspacing="0">
            <thead>
            <th>ID</th>
            <th>Nail Shop</th>
            <th>Contact Name</th>
            <th>Business Phone</th>
            <th>Cell Phone</th>
            <th>Status</th>
            <th>Seller</th>
            <th class="text-right">Discount Total($)</th>
            <th class="text-right">Charged Total($)</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        getTotalCustomer();
        $("#created_at").datepicker({});
        var team_id = $("#team_id").val();
        var table = $('#dataTableAllCustomer').DataTable({
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
            ajax:{ url:"{{ route('report.customers.datatable') }}",
                data: function (d) {
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.address = $("#address").val();
                    d.status_customer = $("#status-customer :selected").val();
                    d.team_id = $("#team_id").val();
                }
            },
            columns: [

                { data: 'id', name: 'id',class:'text-center' },
                { data: 'ct_salon_name', name: 'ct_salon_name' },
                { data: 'ct_fullname', name: 'ct_fullname'},
                { data: 'ct_business_phone', name: 'ct_business_phone' ,class:'text-center'},
                { data: 'ct_cell_phone', name: 'ct_cell_phone',class:'text-center' },
                { data: 'ct_status', name: 'ct_status',class:'text-center' },
                { data: 'seller', name: 'seller' },
                { data: 'discount_total', name: 'discount_total' ,class:'text-right'},
                { data: 'charged_total' , name:'charged_total' ,class:'text-right'}
            ],
        });

        $("#search-button").click(function(){
            table.draw();
            getTotalCustomer();
        });
        $("#reset-btn").on('click',function(e){
            $(this).parents('form')[0].reset();
            table.ajax.reload(null, false);
            getTotalCustomer();
        });

        function getTotalCustomer() {
            var formData = new FormData($("#customer_form")[0]);
            formData.append('_token','{{csrf_token()}}');

            $.ajax({
                url: '{{route('report.customers.total_customer')}}',
                type: 'POST',
                dataType: 'html',
                processData: false,
                contentType: false,
                data: formData,
            })
                .done(function(data) {

                    data = JSON.parse(data);
                    $("#totalNewCustomer").text(data.arrivals_total);
                    $("#totalServicedCustomer").text(data.serviced_total);
                    $("#totalAssignedCustomer").text(data.assigned_total);
                    $("#totalDisabledCustomer").text(data.disabled_total);
                    $("#totalDiscount").text(data.discount_total);
                    $("#totalCharged").text(data.charged_total);

                })
                .fail(function() {
                    toastr.error('Get List User Failed!');
                });
        }
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
                    // console.log(data);
                    // return;
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
