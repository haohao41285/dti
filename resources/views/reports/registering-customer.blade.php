@extends('layouts.app')
@section('content-title')
    REGISTERING CUSTOMER
@endsection
@section('content')
<div class="position:relative;">
    
        {{-- <img class="img-zoom"
        src="https://www.adorama.com/alc/wp-content/uploads/2017/11/shutterstock_114802408-1024x683.jpg" alt=""> --}}

        <div class="thumbnail bg-info img-zoom" style="position: fixed;min-height:300px;min-width:300px;display:none;left: 50%;top: 50%;transform: translate(-50%, -50%);z-index:1000;border-radius:10px 10px" >
            <div class="caption" style="color:white">
                <p class="text-center">#43.</p>
                <span class="text-danger close-image" style="position:absolute;top:5px;right:10px"><i class="fas fa-times"></i></span>
            </div>
            <img class="image-service text-center" src="" alt="Load Image Failed" style="width:100%">
              
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
                <div class="col-md-2">
                    <label for="">Status</label>
                    <select name="" class="form-control form-control-sm" id="status">
                        <option value="">All</option>
                        <option value="1">NEW</option>
                        <option value="2">DONE</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Service Type</label>
                    <select name="" class="form-control form-control-sm" id="type">
                        <option value="">All</option>
                        @foreach(orderType() as $key => $type)
                            <option value="{{$key}}">{{$type}}</option>
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
                    <th>Order Type</th>
                    <th>Service Id</th>
                    <th>Customer Information</th>
                    <th>Note</th>
                    <th>Status</th>
                    <th>Check</th>
                    <th>Created At</th>
                    <th>Updated At</th>
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
                ],
                ajax:{ url:"{{ route('report.registering-customer.datatable') }}",
                    data: function (d) {
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                        d.status = $("#status").val();
                        d.type = $("#type").val();
                    }
                },
                columns: [
                    { data: 'web_order_id', name: 'web_order_id',class:'text-center' },
                    { data: 'web_order_type', name: 'web_order_type' },
                    { data: 'web_service_id', name: 'web_service_id',class:'text-center'},
                    { data: 'customer_information', name: 'customer_information' ,},
                    { data: 'note', name: 'note',},
                    { data: 'status', name: 'status',class:'text-center'},
                    { data: 'web_order_status', name: 'web_order_status',searchable:false,oderable:false,class:'text-center'},
                    { data: 'created_order', name: 'created_order',class:'text-center'},
                    { data: 'updated_order', name: 'updated_order',class:'text-center'},
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

            $('.close-image').click(function(){
                $(".img-zoom").css('display','none');
            });
            $(document).on('click','.service-image',function(){
                let src = $(this).attr('src');
                $(".image-service").attr('src',src);
                $(".img-zoom").css('display','');
            });
            $(document).on('click','.update-order',function(){
                let o = $(this).attr('order');
                $.ajax({
                    type: "GET",
                    url: "{{route('report.registering-customer.change-status')}}",
                    data: {
                        order_id: o,
                    },
                    dataType: "html",
                    success: function (data) {
                        data = JSON.parse(data);
                        if(data.status == 'error')
                            toastr.error(data.message);
                        else
                            toastr.success(data.message);
                        console.log(data);
                        table.ajax.reload(null,false);
                    }
                });
            })
        });
    </script>
@endpush
