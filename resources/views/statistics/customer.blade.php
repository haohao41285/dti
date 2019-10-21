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
            <div class="form-group form-group-sm search-month form-inline" style="padding-right: 10px">     
                <form method="get" id="search-datatable">    
                <span class="search-month">
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
                 </span>
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
                            <th>Name</th>
                            <th>Email</th>                            
                            <th>Phone</th>
                            <th>Created Date</th>
                            <th>Created Month</th>
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
        var date = null;
      var table = $('#customer-datatable').DataTable({
           // dom: "lfrtip",    
           processing: true,
           serverSide: true,
           ajax:{ 
            url:"{{ route('statisticCustomerDatable') }}",
            data:function(data){
                data.date =date
            },
        },
           columns: [
    
                    { data: 'customer_id', name: 'customer_id',class:'text-center' },
                    { data: 'customer_fullname', name: 'customer_fullname' },
                    { data: 'customer_email', name: 'customer_email'},
                    { data: 'customer_phone', name: 'customer_phone' ,class:'text-center'},
                    { data: 'created_at', name: 'created_at' ,class:'text-center'},                 
                    { data: 'created_month', name: 'created_month' },                 
            ], 
            buttons: [
    
                ],   
            "columnDefs": [
            {
                "targets": [ 5 ],
                "visible": false,
            },            
        ]   
      });

      $(".search-month button").on("click",function(e){
            e.preventDefault();
            $(this).parent().find(".btn-primary").addClass("btn-secondary");
            $(this).parent().find(".btn-primary").removeClass("btn-primary");
            $(this).addClass("btn-primary");
            $(this).removeClass("btn-secondary");

            var value = $(this).text();
            table.columns(5).search(value).draw();
      });
      $("#search-datatable").on("submit",function(e){
        e.preventDefault();
        date = $("input[name='dateSearch']").val();
        table.draw();
    });

      $( "div.search-month button#load-click" ).trigger("click");


    });  
      
    
    
    
</script>
@endpush