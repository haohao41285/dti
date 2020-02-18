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
                    <select name="seller" id="seller_id" class="form-control form-control-sm">
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
        <table class="table table-sm table-striped table-hover" id="dataTableAllService" width="100%" cellspacing="0">
            <thead>
                <tr class="thead-light">
                    <th>Order ID</th>
                    <th>Rating</th>
                    <th>Note</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    
@endpush
