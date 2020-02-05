@extends('layouts.app')
@section('title')
    Sellers Report
@endsection
@section('content')
    <div class="table-responsive">
        <h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">SELLERS REPORT</h4>
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
                            document.location.href = "{{route('report.sellers.export')}}";
                        }
                    }
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

        });
    </script>
@endpush
