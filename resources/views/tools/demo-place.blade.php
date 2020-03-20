@extends('layouts.app')
@section('content-title')
    DEMO PLACES
@endsection
@push('styles')
    <style>
        .row-detail{
            margin-top: 12px;
        }
        tbody tr .disabled{
            text-decoration: line-through solid red;
        }
        .mycard-footer {
            height: 25px;
            background: #333333;
            font-size: 15px;
            text-indent: 10px;
           /* border-radius: 0 0px 4px 4px;*/
        }

        .gallery-card {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: .25rem;
                height: 132px;
                margin-bottom:14px;
        }
        .gallery-card-body {
            -webkit-box-flex: 1;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            /*padding: 1.25rem;*/
        }
        .gallery-card img {
            height: 100px;
            width: 100%;
        }
        label{
            margin-bottom: 0 !important;
        }
        /*--checkbox--*/

        .block-check {
            display: block;
            position: relative;
           
           
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .block-check input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            cursor: pointer;
        }

        /* On mouse-over, add a grey background color */
        .block-check:hover input ~ .checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .block-check input:checked ~ .checkmark {
            background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .block-check input:checked ~ .checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .block-check .checkmark:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
        .card-link{
            color: white;
        }
    </style>
@endpush
@section('content')
    <div class="col-12 ">
        <div class="card shadow mb-4 ">
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
        <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width:80%" role="document">
            <form id="add-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="card-header py-2 row " style="border: none">
                            <h6 class="m-0 font-weight-bold text-primary">Information Place</h6>
                        </div>
                    </div>
                    <div class="modal-body row">
                        <div class="col-12 row">
                            <div class="col-md-8">
                                <div class="row">
                                         <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">First name</span>
                                        </div>
                                        <input type="text" class="form-control" name="customer_firstname">
                                    </div>
                                    <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Last Name</span>
                                        </div>
                                        <input type="text" class="form-control" name="customer_lastname">
                                    </div>
                                    <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Business Name</span>
                                        </div>
                                        <input type="text" class="form-control" name="business_name">
                                    </div>
                                    <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Phone<sup class="text-danger">*</sup></span>
                                        </div>
                                        <input type="text" onkeypress="return isNumberKey(event)" class="form-control customer_phone" name="customer_phone">
                                    </div>
                                    <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Email</span>
                                        </div>
                                        <input type="email" class="form-control" name="customer_email">
                                    </div>
                                    <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Address</span>
                                        </div>
                                        <input type="text" class="form-control" name="customer_address">
                                    </div>
                                    <div class="input-group mb-3 input-group-sm col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Website</span>
                                        </div>
                                        <input type="text" class="form-control" name="customer_website">
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-danger" style="font-size: 12px">*Customer Phone is not with character "0" at the first</span><br>
                                        <span class="text-danger" style="font-size: 12px">*Customer Phone includes 10 characters</span>
                                    </div>
                                </div>
                                
                                <hr>
                                <div class="custom-control custom-checkbox mb-3">
                                  <input type="checkbox" class="custom-control-input" id="place_demo" value="1" name="place_demo">
                                  <label class="custom-control-label" for="place_demo">Place Demo</label>
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-12 row border-bottom border-dark">
                                    @foreach($theme_list as $theme)
                                        <div class="col-md-3">
                                          <div class="gallery-card">
                                            <div class="gallery-card-body">
                                              <label class="block-check">
                                             <img src="{{env('URL_FILE_VIEW').$theme->theme_image}}" class="img-responsive" />
                                            <input type="radio" name="theme_website" value="{{ $theme->theme_name_temp }}">
                                              <span class="checkmark"></span>
                                              </label>
                                               <div class="mycard-footer">
                                                <a href="javascript:void(0)" class="card-link">{{ $theme->theme_name }}</a>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label><b>Combos/ Services</b></label>
                                
                                @foreach($services as $service)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="service_{{ $service->id }}" value="{{ $service->id }}" name="services[]">
                                        <label class="custom-control-label" for="service_{{ $service->id }}">{{ $service->cs_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-header">
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
            });
            
        });
    </script>
@endpush
