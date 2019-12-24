@extends('layouts.app')
@section('content-title')
    {{-- Places --}}
@endsection
@push('styles')
    <style>
        .row-detail{
            margin-top: 12px;
        }
        tbody tr .disabled{
            text-decoration: line-through solid red;
        }
    </style>
@endpush
@section('content')
    <div class="col-12 ">
        <div class="card shadow mb-4 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary">Places List </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover" id="places-datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>License</th>
                            <th>Action</th>
                            {{--<th class="w-30">Action</th>--}}
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- detail place --}}
    <div class="modal fade" id="detail" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body row">
                    <div class="col-12 ">
                        <div class="">
                            <div class="card-header py-2 row ">
                                    <h6 class="m-0 font-weight-bold text-primary">Infomation Customer</h6>
                            </div>
                            <form id="add-form">
                                <div class="card-body">
                                    <div class="col-12 row">
                                            <div class="input-group mb-3 input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Customer Phone<sup class="text-danger">*</sup></span>
                                                </div>
                                                <input type="text" class="form-control customer_phone" name="customer_phone">
                                            </div>
                                            <span class="text-danger" style="font-size: 12px">Customer Phone is not with character "0" at the first</span>
                                            <span class="text-danger" style="font-size: 12px">Customer Phone includes 10 characters</span>
                                        <div class="col-12">
                                            <div class="form-group float-right">
                                                <input type="button" class="btn btn-sm btn-danger cancel" data-dismiss="modal" value="Cancel">
                                                <input type="button" class="btn btn-sm btn-primary submit" value="Submit">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                {{--
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn btn-primary">Save changes</button>
                    <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                --}}
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            placeDatatable = $('#places-datatable').DataTable({
                // dom: "lBfrtip",
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: true,
                order:[[0,'desc']],
                buttons: [
                    {
                        text: '<i class="fas fa-plus"></i> Add Place',
                        className: 'btn btn-sm btn-primary add-place',
                    },
                ],

                ajax: {
                    url: "{{ route('demo_place.datatable') }}",
                    data: function(data) {
                    },
                },
                columns: [
                    { data: 'place_id', name: 'place_id', class: "text-center" },
                    { data: 'place_name', name: 'place_name', },
                    { data: 'place_phone', name: 'place_phone', },
                    { data: 'place_ip_license', name: 'place_ip_license', class: "text-center" },
                    // { data: 'place_demo', name: 'place_demo', class: "text-center" },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
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
            });
            $(document).on('click','.switchery',function(){
                var place_demo  = $(this).siblings('input').attr('place_demo');
                var place_id = $(this).siblings('input').attr('place_id');

                $.ajax({
                    url: "{{ route('demo_place.change_demo_status') }}",
                    method: "get",
                    dataType: "html",
                    data: {
                        place_demo,
                        place_id
                    },
                    success: function(data) {
                        var data = JSON.parse(data);
                        if (data.status === 'error') {
                            toastr.error(data.message);
                        }else{
                            toastr.success(data.message);
                        }
                        placeDatatable.ajax.reload( null, false );
                    },
                    error: function() {
                        toastr.error("Failed! Change Demo Status Failed!");
                    }
                });
            });
            $(".cancel").click(function () {
               cleanModal();
            });
            function cleanModal(){
                $("#add-form")[0].reset();
                $("#detail").modal('hide');
            }
            $(".add-place").click(function(){
                $("#detail").modal('show');
            });
            $(".submit").click(function(){

                let formData = new FormData($(this).parents('form')[0]);
                formData.append('_token','{{csrf_token()}}');

                $.ajax({
                    url: '{{route('demo_place.save')}}',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    async: true,
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();
                        return myXhr;
                    },
                    success: function (data) {
                        // var data = JSON.parse(data);
                        // console.log(data);
                        // return;
                        if(data.status == 'error'){
                            if(typeof(data.message) === 'string')
                                toastr.error(data.message);
                            else
                                $.each(data.message,function(ind,val){
                                    toastr.error(val);
                                })
                        }else{
                            toastr.success(data.message);
                            cleanModal();
                            placeDatatable.draw();
                        }
                    },
                    fail: function() {
                        console.log("error");
                    }
                });
            });
            $(document).on('click','.delete',function(){
                if(confirm('Do you want to delete this demo place ?')){
                    let place_id = $(this).attr('place_id');
                    $.ajax({
                        url: '{{route('demo_place.delete')}}',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            place_id: place_id,
                            _token: '{{csrf_token()}}'
                        },
                    })
                    .done(function(data) {

                        data = JSON.parse(data);
                        // console.log(data);
                        // return;

                        if(data.status === 'error')
                            toastr.error(data.message);
                        else{
                            placeDatatable.ajax.reload( null, false );
                            toastr.success(data.message);
                        }
                    })
                    .fail(function() {
                        console.log("Failed! Delete Place Failed!");
                    });
                }else{
                    return;
                }
            });
            $(".customer_phone").keypress(function(){
                let customer_phone = $(this).val();
                if(customer_phone.length > 9){
                    toastr.error('Customer Phone not over 10 character');
                    return false;
                }
            })
        });
    </script>
@endpush
