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
            <h6 class="m-0 font-weight-bold text-primary">Services List </h6>
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
                    {{-- <input type="submit" class="btn btn-primary btn-sm" value="Search"> --}}
                    {{-- <br> --}}
                </div>
            </form>
            <div class="form-group form-group-sm search-group">
                {{-- <span class="search search-month" style="display: none;">
                    <button id="load-click" class=" btn btn-sm btn-secondary">Jan</button>
                    <button class="btn btn-sm btn-secondary ">Feb</button>
                    <button class="btn btn-sm btn-secondary ">Mar</button>
                    <button class="btn btn-sm btn-secondary ">Apr</button>
                    <button class="btn btn-sm btn-secondary ">May</button>
                    <button class="btn btn-sm btn-secondary ">Jun</button>
                    <button class="btn btn-sm btn-secondary ">Jul</button>
                    <button class="btn btn-sm btn-secondary ">Aug</button>
                    <button class="btn btn-sm btn-secondary ">Sep</button>
                    <button class="btn btn-sm btn-secondary ">Oct</button>
                    <button class="btn btn-sm btn-secondary ">Nov</button>
                    <button class="btn btn-sm btn-secondary ">Dec</button>
                </span> --}}
                <span class="search search-quarter" style="display: none">
                    <button class=" btn btn-sm btn-secondary first" value-quarter="first">First quarter</button>
                    <button class=" btn btn-sm btn-secondary second" value-quarter="second">Second quarter</button>
                    <button class=" btn btn-sm btn-secondary third" value-quarter="third">Third quarter</button>
                    <button class=" btn btn-sm btn-secondary fourth" value-quarter="fourth">Fourth quarter</button>
                </span>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered" id="service-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    {{-- <tbody>
                        @for($i = 0; $i < 20; $i++) <tr>
                            <td>{{$i}}</td>
                            <td>{{$i}}</td>
                            <td>{{$i}}</td>
                            </tr>
                            @endfor
                    </tbody> --}}
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    var type = null;
    var valueQuarter = null;

    var table = $('#service-datatable').DataTable({
        dom: "lrtip",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('statisticServiceDatable') }}",
            data: function(data) {
                var date = $("input[name='dateSearch']").val();
                data.date = date;
                data.type = type;
                data.valueQuarter = valueQuarter;
            },
        },
        buttons: [],
        columns: [
            { data: 'nameService', name: 'nameService', class: 'text-center' },
            { data: 'count', name: 'count' },
            { data: 'priceService', name: 'priceService' },
            { data: 'totalPrice', name: 'totalPrice' },
            // { data: 'created_at', name: 'created_at'},
        ],
    });
    $(".datepicker").datepicker({
        defaultDate: new Date(),
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
