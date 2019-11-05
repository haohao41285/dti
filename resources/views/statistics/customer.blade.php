@extends('layouts.app')
@section('content-title')
{{-- Places --}}
@endsection
@push('styles')
<style>
    .row-detail{
    margin-top: 12px;
    }
</style>
@endpush
@section('content')
<div class="col-12 ">
    <div class="card shadow mb-4 ">
        <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-primary">New Customers List </h6>
        </div>
        <div class="card-body">
            <form method="get" id="search-datatable row">
                <div class="col-12 row  form-inline">
                    <input type="text" name="dateSearch" class="form-control-sm form-control datepicker">
                    <span class="search-by" style="padding-left: 10px">
                        <button class="btn btn-sm btn-secondary daily">Daily</button>
                        <button class="btn btn-sm btn-secondary monthly">Monthly</button>
                        <button class="btn btn-sm btn-secondary quarterly">Quarterly</button>
                        <button class="btn btn-sm btn-secondary yearly">Yearly</button>
                    </span>
                </div>
            </form>
            <div class="form-group form-group-sm search-group">
                <span class="search search-quarter" style="display: none">
                    <button class=" btn btn-sm btn-secondary first" value-quarter="first">First quarter</button>
                    <button class=" btn btn-sm btn-secondary second" value-quarter="second">Second quarter</button>
                    <button class=" btn btn-sm btn-secondary third" value-quarter="third">Third quarter</button>
                    <button class=" btn btn-sm btn-secondary fourth" value-quarter="fourth">Fourth quarter</button>
                </span>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered" id="customer-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Created Date</th>
                            {{-- <th>Created Month</th> --}}
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    $(".datepicker").datepicker({});
    var type = "Daily";
    var valueQuarter = null;

    var table = $('#customer-datatable').DataTable({
        // dom: "lfrtip",    
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('statisticCustomerDatable') }}",
            data: function(data) {
                var date = $("input[name='dateSearch']").val();
                data.date = date;
                data.type = type;
                data.valueQuarter = valueQuarter;
            },
        },
        columns: [

            { data: 'customer_id', name: 'customer_id', class: 'text-center' },
            { data: 'customer_fullname', name: 'customer_fullname' },
            { data: 'customer_email', name: 'customer_email' },
            { data: 'customer_phone', name: 'customer_phone', class: 'text-center' },
            { data: 'created_at', name: 'created_at', class: 'text-center' },
            // { data: 'created_month', name: 'created_month' },
        ],
        buttons: [

        ],

    });

    //
    $(".daily").on('click', function(e) {
        e.preventDefault();
        $(".search-group .search").hide(200);
        type = $(this).text();

        table.draw();
    });
    $(".monthly").on('click', function(e) {
        e.preventDefault();
        $(".search-group .search").hide(200);
        $(".search-month").show(200);
        type = $(this).text();
        table.draw();
    });
    $(".quarterly").on('click', function(e) {
        e.preventDefault();
        $(".search-group .search").hide(200);
        $(".search-quarter").show(200);
        $(".first").trigger('click');

        type = $(this).text();
        table.draw();
    });
    $(".yearly").on('click', function(e) {
        e.preventDefault();
        $(".search-group .search").hide(200);
        type = $(this).text();
        table.draw();
    });

    $(".search-by button").on('click', function() {
        $(this).parent().find(".bg-primary").removeClass("bg-primary");
        $(this).addClass("bg-primary");
    });

    $(".search-quarter button").on("click", function() {
        $(this).parent().find(".bg-primary").removeClass("bg-primary");
        $(this).addClass("bg-primary");
        valueQuarter = $(this).attr('value-quarter');

        table.draw();
    });

    $(".daily").trigger('click');



});

</script>
@endpush
