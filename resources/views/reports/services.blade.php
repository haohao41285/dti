@extends('layouts.app')
@section('content-title')
    SERVICES REPORT
@endsection
@section('content')
    <div class="table-responsive">
        <form id="search-form">
            <div class="form-group col-md-12 row">
                <div class="col-md-4">
                    <label for="">Created date</label>
                    <div class="input-daterange input-group" id="created_at">
                        <input type="text" class="input-sm form-control form-control-sm" id="start_date" name="start" />
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input-sm form-control form-control-sm" id="end_date" name="end" />
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">Address</label>
                    <input type="text" id="address" name="address" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label for="">Seller</label>
                    <select name="seller" id="seller_id" class="form-control form-control-sm">
                        @if(\Gate::allows('permission','service-report-admin') || \Gate::allows('permission','service-report-leader'))
                        <option value="">--All--</option>
                        @endif
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
                    <th>Service</th>
                    <th>Service Price($)</th>
                    <th>Total Customers</th>
                    <th>Total Orders</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#created_at").datepicker({});
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
                           document.location.href = "{{route('report.services.export')}}";
                       }
                    }
                ],
                ajax:{ url:"{{ route('report.services.datatable') }}",
                    data: function (d) {
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                        d.address = $("#address").val();
                        d.seller_id = $("#seller_id").val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id',class:'text-center' },
                    { data: 'service_name', name: 'service_name' },
                    { data: 'service_price', name: 'service_price',class:'text-right'},
                    { data: 'customer_total', name: 'customer_total' ,class:'text-right'},
                    { data: 'order_total', name: 'order_total',class:'text-right' },
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

        });
    </script>
@endpush
