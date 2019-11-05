@extends('layouts.app')
@section('title')
    Setup Service Type
@stop
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h5><b>Service Type List</b></h5>
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Discount(%)</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-5 offset-md-1" style="padding-top: 0px">
            <form action="" id="setup-form">
                <h5><b class="tip">Add Service Type</b></h5>
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" class="form-control form-control-sm" name="" id="name">
                </div>
                <div class="form-group">
                    <label for="">Max Discount(%)(Max:100)</label>
                    <input type="text" onkeypress=" return isNumberKey(event)" class="form-control form-control-sm" name="" id="max-discount">
                </div>
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea class="form-control form-control-sm" rows="3" id="description" ></textarea>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-danger float-right cancel-service-type ml-2">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary float-right submit-service-type">Submit</button>
                </div>
            </form>

        </div>
    </div>

@stop
@push('scripts')
    <script type="text/javascript">
        //DEFINE VAR
        var service_type_id = 0;

        $(document).ready(function($) {
            dataTable = $("#dataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                buttons: [
                ],
                ajax:{ url:"{{route('service-type-datatable')}}"},
                columns:[
                    {data:'id', name:'id',class:'text-center'},
                    {data:'name', name:'name'},
                    {data:'description', name:'description'},
                    {data:'max_discount', name:'max_discount',class:'text-right'},
                    {data:'status', name:'status',class: 'text-center'},
                ],
                fnDrawCallback:function (oSettings) {
                    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elems.forEach(function (html) {
                        var switchery = new Switchery(html, {
                            color: '#0874e8',
                            className : 'switchery switchery-small'
                        });
                    });
                }
            })
            $(document).on('click','.switchery',function(){

                var id = $(this).siblings('input').attr('id');
                var status = $(this).siblings('input').attr('status');
                clearView();

                $.ajax({
                    url: '{{route('change-status-service-type')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        status: status,
                        id: id
                    },
                })
                    .done(function(data) {
                        data = JSON.parse(data);
                        if(data.status == 'error')
                            toastr.error(data.message);
                        else{
                            toastr.success(data.message);
                            dataTable.draw();
                        }
                    })
                    .fail(function(data) {
                        toastr.error('Change Status Failed!');
                    });
            });
            $('#dataTable tbody').on( 'click', 'tr', function () {

                $("#name").val(dataTable.row(this).data()['name']);
                $("#description").val(dataTable.row(this).data()['description']);
                $("#max-discount").val(dataTable.row(this).data()['max_discount']);
                $(".tip").text("Edit Service Type");
                service_type_id = dataTable.row(this).data()['id'];

            });
            $(document).on('click','.submit-service-type',function(){

                var description = $("#description").val();
                var name = $("#name").val();
                var max_discount = $("#max-discount").val();

                if( name != ""){
                    $.ajax({
                        url: '{{route('add-service-type')}}',
                        type: 'GET',
                        dataType: 'html',
                        data: {
                            description: description,
                            name: name,
                            service_type_id: service_type_id,
                            max_discount: max_discount
                        },
                    })
                        .done(function(data) {
                            data = JSON.parse(data);

                            if(data.status == 'error')
                                toastr.error(data.message);
                            else{
                                clearView();
                                dataTable.draw();
                            }
                        })
                        .fail(function() {
                            toastr.error('Save Service Type Error!');
                        });
                }else
                    toastr.error('Enter Name!');
            });
            $(".cancel-service-type").click(function(){
                clearView();
            })
            function clearView(){
                $(".tip").text("Add Service Type");
                $("#setup-form")[0].reset();
                service_type_id = 0;
            }
            $("#max-discount").keyup(function () {
                var max_discount = $(this).val();
                if(max_discount > 100)
                    $(this).val(100);
            })
        });
        function isNumberKey(evt){

            var charCode = (evt.which) ? evt.which : evt.keyCode;

            if ( (charCode > 31 && (charCode < 48 || charCode > 57)) )
                return false;
            return true;
        }

    </script>
@endpush
