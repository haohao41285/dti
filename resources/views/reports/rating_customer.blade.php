@extends('layouts.app')
@section('content-title')
    Customer's Rating
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
                    <label for="">Rating</label>
                    <select name="rating_level" id="rating_level" class="form-control form-control-sm">
                            <option value="">Tất Cả</option>
                        @foreach(ratingCustomer() as $key => $rating)
                            <option value="{{$key}}">{{$rating}}</option>
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
        <table class="table table-sm table-striped table-bordered table-hover" id="rating-customer" width="100%" cellspacing="0">
            <thead>
                <tr class="thead-light">
                    <th style="width: 10%">Order ID</th>
                    <th style="width: 20%">Rating</th>
                    <th>Note</th>
                    <th style="width: 20%" class="text-center">Created At</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function($) {

            $("#created_at").datepicker({});

            var table = $('#rating-customer').DataTable({
                // dom: "lBfrtip",
                // order:[[6,"desc"]],
                processing: true,
                serverSide: true,
                buttons: [
                ],
                ajax:{ url:"{{ route('report.rating-customer.datatable') }}",
                    data: function (d) {
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                        d.rating_level = $("#rating_level").val();
                    }
                },
                columns: [
                    { data: 'order_id', name: 'order_id',class:'text-center' },
                    { data: 'rating_level', name: 'rating_level' },
                    { data: 'note', name: 'note',},
                    { data: 'created_at', name: 'created_at' ,class:'text-center'},
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
