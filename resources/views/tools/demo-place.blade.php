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
                            <th>Demo Place</th>
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
        <div style="max-width: 90%" class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{--
                    <h5 class="modal-title">Detail place</h5>
                    --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-12 ">
                        <div class="card shadow mb-4 ">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">Detail place</h6>
                            </div>
                            <div class="card-body">
                                <div class="col-12 row">
                                    <div class="col-6">
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Logo</label>
                                            <div class="previewImage" id="logo">
                                                <img id="previewImageAppbanner" src="http://localhost:8000/images/no-image.png">
                                            </div>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Business name</label>
                                            <label class="col-sm-8" id="bussiness-name">Business name</label>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Tax code</label>
                                            <label class="col-sm-8" id="tax-code">Tax code</label>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Business phone</label>
                                            <label class="col-sm-8" id="business-phone">Business phone</label>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Price floor</label>
                                            <label class="col-sm-8" id="price-floor">Price floor</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Favicon</label>
                                            <div class="previewImage" id="favicon">
                                                <img id="previewImageAppbanner" src="http://localhost:8000/images/no-image.png">
                                            </div>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Address</label>
                                            <label class="col-sm-8" id="address">Address</label>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Email</label>
                                            <label class="col-sm-8" id="email">Email</label>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Website</label>
                                            <label class="col-sm-8" id="website">Website</label>
                                        </div>
                                        <div class="row col-12 row-detail">
                                            <label class="col-sm-4">Interest($)</label>
                                            <label class="col-sm-8" id="interest">Interest($)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row col-12 row-detail">
                                        <label class="col-sm-2">Hide service price</label>
                                        <label class="col-sm-8 row" id="hide-service-price">Hide service price</label>
                                    </div>
                                    <div class=" form-group">
                                        <div class="col-md-12 row row-detail">
                                            <label class="col-sm-2" ">Working Day</label>
                                            <div class=" col-sm-10 workingtime">
                                                <div class="col-day">
                                                    <label>Monday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="monday">
                                                        <label class="btn btn-sm btn-day " rel="open">
                                                            <input disabled value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day " rel="close">
                                                            <input disabled value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-day">
                                                    <label>Tuesday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="tuesday">
                                                        <label class="btn btn-sm btn-day " rel="open">
                                                            <input disabled name="work_tue" value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day " rel="close">
                                                            <input disabled name="work_tue" value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-day">
                                                    <label>Wednesday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="wednesday">
                                                        <label class="btn btn-sm btn-day  " rel="open">
                                                            <input disabled name="work_wed" value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day " rel="close">
                                                            <input disabled name="work_wed" value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-day">
                                                    <label>Thursday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="thursday">
                                                        <label class="btn btn-sm btn-day " rel="open">
                                                            <input disabled name="work_thur" value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day " rel="close">
                                                            <input disabled name="work_thur" value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-day">
                                                    <label>Friday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="friday">
                                                        <label class="btn btn-sm btn-day " rel="open">
                                                            <input disabled name="work_fri" value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day" rel="close">
                                                            <input disabled name="work_fri" value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-day">
                                                    <label>Saturday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="saturday">
                                                        <label class="btn btn-sm btn-day " rel="open">
                                                            <input disabled name="work_sat" value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day " rel="close">
                                                            <input disabled name="work_sat" value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-day">
                                                    <label>Sunday</label>
                                                    <div class="btn-group btn-group-toggle working-day" rel="sunday">
                                                        <label class="btn btn-sm btn-day" rel="open">
                                                            <input disabled name="work_sun" value="1" type="radio"> Open
                                                        </label>
                                                        <label class="btn btn-sm btn-day " rel="close">
                                                            <input disabled name="work_sun" value="0" type="radio"> Close
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class=" form-group">
                                            <div class="col-md-12 row">
                                                <label class="col-sm-2">Time Start</label>
                                                <div class="col-sm-10 time-start workingtime">
                                                    <div class="col-day input-group-spaddon day_monday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="mon-start" value="19:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_tuesday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="tue-start" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_wednesday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="wed-start" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_thursday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="thustart" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_friday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="fri-start" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_saturday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="sat-start" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_sunday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="sun-start" value="23:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=" form-group">
                                            <div class="col-md-12 row">
                                                <label class="col-sm-2">Time End</label>
                                                <div class="col-sm-10 time-end workingtime">
                                                    <div class="col-day input-group-spaddon day_monday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="mon-end" value="19:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_tuesday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="tue-end" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_wednesday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="wed-end" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_thursday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="thu-end" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_friday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="fri-end" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_saturday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="sat-end" value="21:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-day input-group-spaddon day_sunday">
                                                        <div class="input-group date">
                                                            <input disabled type="text" id="sun-end" value="23:00" class="form-control form-control-sm timepicker">
                                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="col-sm-2">Description</label>
                                            <label class="col-sm-8" id="description">Description</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                    { data: 'place_demo', name: 'place_demo', class: "text-center" },
                    // { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
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
            })
        });
    </script>
@endpush
