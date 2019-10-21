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
            <div class="form-group form-group-sm search-month form-inline" style="padding-right: 10px">     
                <form method="get" id="search-datatable">    
                {{-- <span class="search-month">
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
                     <input type="text" name="dateSearch" class="form-control-sm form-control datepicker">
                     <input type="submit" class="btn btn-primary btn-sm" value="Search">
                 </form>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered" id="customer-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            {{-- <th>Email</th>                             --}}
                            <th>Quantity</th>
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
<script>
    $(document).ready(function() {
        $(".datepicker").datepicker({});
    });
</script>
@endpush