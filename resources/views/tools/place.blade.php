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
                <table class="table table-bordered" id="places-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>

                            <th>Phone</th>
                            <th>License</th>

                            {{-- <th>Created Date</th> --}}
                            <th width="200">Action</th>

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view" tabindex="-1" role="dialog">
    <div style="max-width: 90%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change a user password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-8 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Users List </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="user-datatable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Full name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " id="user_nickname">Change password </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="change-password-form">
                                @csrf
                                <div class="form-group row col-12">
                                    <label class="col-5">New Password</label>
                                    <input class="col-7 form-control-sm form-control" type="password" name="newPassword">
                                </div>
                                <div class="form-group row col-12">
                                    <label class="col-5">Confirm Password</label>
                                    <input class="col-7 form-control-sm form-control" type="password" name="confirmPassword">
                                </div>
                                <div class="form-group col-12 row">
                                    <label class="col-5"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Save">
                                </div>
                            </form>
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
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_tuesday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="tue-start" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_wednesday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="wed-start" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_thursday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="thustart" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_friday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="fri-start" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_saturday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="sat-start" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_sunday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="sun-start" value="23:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
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
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_tuesday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="tue-end" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_wednesday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="wed-end" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_thursday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="thu-end" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_friday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="fri-end" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_saturday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="sat-end" value="21:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
                                                </div>
                                            </div>
                                            <div class="col-day input-group-spaddon day_sunday">
                                                <div class="input-group date">
                                                    <input disabled type="text" id="sun-end" value="23:00" class="form-control form-control-sm timepicker">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
<<<<<<< HEAD
                                                    </span>
=======
                                                        </span>
                                                    </div>
>>>>>>> origin/thieu
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
<<<<<<< HEAD
=======
            {{--
            <div class="modal-footer">
                <button type="button" class="btn-sm btn btn-primary">Save changes</button>
                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            --}}
>>>>>>> origin/thieu
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
{{-- modal setting --}}
<div class="modal fade" id="setting" tabindex="-1" role="dialog">
    <div style="max-width: 95%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setting place theme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary ">Website themes </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="themes-dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>ID</th>
                                        <th>Theme Code</th>
                                        <th>Image</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary ">Website properties </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="position: relative; overflow: auto; max-height: 70vh; width: 100%;">
                                <table class="dataTable  table table-bordered table-hover dataTables_scrollBody dataTables_scroll" id="themeProperties" width="100%" cellspacing="0">
                                    <thead>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Image</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    {{-- <div class="form-group">
                        <button class="btn-sm btn btn-success btn-copy-theme">Clone Website</button>
                        <button class="btn-sm btn btn-warning btn-copy-properties">Update Website</button>
                    </div> --}}
                    <div class="card shadow mb-4 copy-theme">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary text-form">Clone Website </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="clone-update-form">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-3">License</label>
                                    <input readonly="true" name="get_license" id="get-license" class="col-9 form-control-sm form-control" type="text">
                                </div>
                                <div class="form-group row">
                                    <label class="col-3">Website</label>
                                    <input class="col-9 form-control-sm form-control" type="text" name="website">
                                </div>
                                <div class="form-group row">
                                    <label class="col-3">Branch</label>
                                    <input class="col-9 form-control-sm form-control" type="text" name="branch">
                                </div>
                                <div class="form-group row">
                                    <label class="col-3">Theme</label>
                                    <input id="get-code" name="get_code" class="col-9 form-control-sm form-control" type="text" readonly="true">
                                </div>
                                <div class="form-group row">
                                    <label class="col-3">ID Properties</label>
                                    <input name="id_properties" class="col-9 form-control-sm form-control" type="text" readonly="true">
                                </div>
                                <div class="form-group row">
                                    <label class="col-3"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Clone Website">
                                    <input type="hidden" name="action" value="clone">
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- properties -->
                    {{-- <div class="copy-properties" style="display: none">
                        <div class="card shadow mb-4">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary ">Update Website </h6>
                            </div>
                            <div class="card-body">
                                <form method="post" id="copy-properties-form">
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-3">License</label>
                                        <label id="get-license"><b></b></label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3">Website</label>
                                        <input class="col-9 form-control-sm form-control" type="text" name="website">
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3">Branch</label>
                                        <input class="col-9 form-control-sm form-control" type="text" name="branch">
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3">Theme</label>
                                        <label id="get-code"><b></b></label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-3"></label>
                                        <input class="btn-sm btn btn-primary" type="submit" value="Update Code">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn-sm btn btn-primary">Update</button> --}}
                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- custom properties modal --}}
<div class="modal fade" id="custom-properties-modal" tabindex="-1" role="dialog">
    <div style="max-width: 95%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Custom value property</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-8 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Value property list </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="value-property-datatable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Variable</th>
                                            <th>Name</th>
                                            <th>Value</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group" id="buttonSetupProperties">
                        <button class="btn-sm btn-primary btn showAdd" id="addText">Add Text</button>
                        <button class="btn-sm btn-danger btn showAdd" id="addImage">Add Image</button>
                        <button class="btn btn-sm btn-warning resetAddProperty">Reset Add</button>
                    </div>
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " id="custom-properties-title">Add</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="custom-properties-form" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row col-12">
                                    <label class="col-5">Variable</label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="variable">
                                </div>
                                <div class="form-group row col-12">
                                    <label class="col-5">Name</label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="name">
                                </div>
                                <div class="addText form-group row col-12">
                                    <label class="col-5">Value</label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="value">
                                </div>
                                <div class="addImage form-group col-12" style="display: none;">
                                    <label>Image</label>
                                    <div class="previewImage">
                                        <img id="previewImageValue" src="{{ asset('/images/no-image.png') }}">
                                        <input type="file" class="custom-file-input" name="image" previewimageid="previewImageValue">
                                    </div>
                                </div>
                                <div class="form-group col-12 row">
                                    <label class="col-5"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Save">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="valuePropertyId">
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
{{-- auto coupon modal --}}
<div class="modal fade" id="auto-coupon-modal" tabindex="-1" role="dialog">
    <div style="max-width: 95%" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Auto Coupon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-8 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Auto coupon list </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="auto-coupon-datatable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Title</th>
                                            <th>Discount</th>
                                            <th>Image</th>
                                            <th>Services</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group" id="buttonSetupProperties">
                        {{-- <button class="btn-sm btn-primary btn showAdd" id="addText">Add Text</button> --}}
                        {{-- <button class="btn-sm btn-danger btn showAdd" id="addImage">Add Image</button> --}}
                        <button class="btn btn-sm btn-warning resetAddAutoCoupon">Reset Add</button>
                    </div>
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " id="auto-coupon-title">Add</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="auto-coupon-form" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row col-12">
                                    <label class="col-5">Title</label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="title">
                                </div>
                                <div class="form-group row col-12">
                                    <label class="col-5">Discount</label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="discount">
                                </div>
                                <div class=" form-group row col-12">
                                    <label class="col-5">Discount Type </label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="discountType">
                                </div>
                                <div class="form-group col-12">
                                    <label>Image</label>
                                    <div class="previewImage">
                                        <img id="previewImageAutoCoupon" src="{{ asset('/images/no-image.png') }}">
                                        <input type="file" class="custom-file-input" name="image" previewimageid="previewImageAutoCoupon">
                                    </div>
                                </div>
                                <div class=" form-group row col-12">
                                    <label class="col-5">Services</label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="services">
                                </div>
                                <div class=" form-group row col-12">
                                    <label class="col-5">Coupon Type </label>
                                    <input class="col-7 form-control-sm form-control" type="text" name="couponType">
                                </div>
                                <div class="form-group col-12 row">
                                    <label class="col-5"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Save">
                                    <input type="hidden" name="action" value="create">
                                    <input type="hidden" name="valuePropertyId">
                                    <input type="hidden" name="id">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="extension_service" tabindex="-1" role="dialog">
    <div  class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extension Services</h5>
            </div>
            <form id="service-form">
                <div class="modal-body">
                    <h6 class="m-0 font-weight-bold text-primary">Service List </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="user-datatable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service Name</th>
                                <th>Service Price</th>
                                <th>Expire Date</th>
                                <th>New Expire Date</th>
                            </tr>
                            </thead>
                            <tbody id="service_table_body">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn btn-primary save-expire">Save changes</button>
                    <button type="button" class="btn-sm btn btn-secondary cancel-change" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">

function clear() {
    $("#change-password-form")[0].reset();
    $("#clone-update-form")[0].reset();
    $("#user_nickname").html("Change password");
    $("#custom-properties-form")[0].reset();
    $("#auto-coupon-form")[0].reset();

    $(".previewImage img").attr("src", "{{ asset('images/no-image.png') }}");
    $('#themes-datatable tbody tr.selected').removeClass('selected');
    $("#custom-properties-form").find("input[name='variable']").attr("readonly", false);
}

function listThemePropertiesByThemeId(theme_id) {
    $.ajax({
        url: "{{ route('listThemePropertiesByThemeId') }}",
        method: "get",
        dataType: "json",
        data: { theme_id },
        success: function(data) {
            if (data.status == 1) {
                var html = '';
                for (var i = 0; i < data.data.length; i++) {
                    html += '<tr properties-id=' + data.data[i].theme_properties_id + '>' +
                        '<td>' + data.data[i].theme_properties_id + '</td>' +
                        '<td>' + data.data[i].theme_properties_name + '</td>' +
                        '<td><img style="height: 5rem;" src="' + "{{env('URL_FILE_VIEW')}}" + data.data[i].theme_properties_image + '" /></td>' +
                        '</tr>'
                }

                $("#themeProperties tbody").html(html);
            }
        },
        error: function() {
            toastr.error("Failed to load Properties!");
        }
    });
}

$(document).ready(function() {
    perviewImage();
    var placeId = null;
    var userId = null;
    var license = null;
    var themeId = null;

    var placeTable = $('#places-datatable').DataTable({
        // dom: "lfrtip",    
        processing: true,
        serverSide: true,
        ajax: { url: "{{ route('getPlacesDatatable') }}" },
        columns: [

            { data: 'place_id', name: 'place_id', class: 'text-center' },
            { data: 'place_name', name: 'place_name' },
            { data: 'place_phone', name: 'place_phone', class: 'text-center' },
            { data: 'place_ip_license', name: 'place_ip_license' },
            { data: 'action', name: 'action', orderable: false, searcheble: false, class: 'text-center' }
        ],
        buttons: [

        ],
    });

    var customerTable = $('#user-datatable').DataTable({
        // dom: "lfrtip",    
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('getUsersDatatable') }}",
            data: function(data) {
                data.placeId = placeId;
            },
        },
        columns: [

            { data: 'user_id', name: 'user_id', class: 'text-center' },
            { data: 'user_nickname', name: 'user_nickname', class: 'user_nickname' },
            { data: 'user_phone', name: 'user_phone', class: 'text-center' },
            { data: 'user_email', name: 'user_email' },
            { data: 'created_at', name: 'created_at', class: 'text-center' },
            // { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
        ],
        buttons: [

        ],
    });


    var themesTable = $('#themes-dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: "{{ route('getDatatableWebsiteThemes') }}", },
        columns: [
            { data: 'theme_id', name: 'theme_id', class: "id" },
            { data: 'theme_name_temp', name: 'theme_name_temp', class: "code" },
            { data: 'theme_image', name: 'theme_image' },

        ],
        buttons: [

        ],
    });

    customPropertytable = $('#value-property-datatable').DataTable({
        // dom: "lBfrtip",
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: true,
        buttons: [

        ],

        ajax: {
            url: "{{ route('getWpDatableByPlaceId') }}",
            data: function(data) {

            },
            error:function(){
                toastr.error("Failed to load Properties!");
            }
        });
    }

    $(document).ready(function() {

      var placeId = null;
      var userId = null;
      var license = null;
      var themeId = null;

      var placeTable = $('#places-datatable').DataTable({
           // dom: "lfrtip",
           processing: true,
           serverSide: true,
           ajax:{ url:"{{ route('getPlacesDatatable') }}" },
           columns: [

                    { data: 'place_id', name: 'place_id',class:'text-center' },
                    { data: 'place_name', name: 'place_name' },
                    { data: 'place_address', name: 'place_address'},
                    // { data: 'place_email', name: 'place_email' },
                    { data: 'place_phone', name: 'place_phone',class:'text-center' },
                    { data: 'place_ip_license', name: 'place_ip_license' },
                    { data: 'created_at', name: 'created_at' ,class:'text-center'},
                    { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
            ],
            buttons: [

                ],
      });

      var customerTable = $('#user-datatable').DataTable({
           // dom: "lfrtip",
           processing: true,
           serverSide: true,
           ajax:{
              url:"{{ route('getUsersDatatable') }}",
              data:function(data){
                data.placeId = placeId;
            },

        },
        columns: [
            { data: 'wp_variable', name: 'wp_variable', class: "wp_variable" },
            { data: 'wp_name', name: 'wp_name', class: "wp_name" },
            { data: 'wp_value', name: 'wp_value', class: "wp_value" },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
        ]
    });


    $(document).on('click', '.view', function(e) {

           columns: [

                    { data: 'user_id', name: 'user_id',class:'text-center' },
                    { data: 'user_nickname', name: 'user_nickname', class:'user_nickname' },
                    { data: 'user_phone', name: 'user_phone',class:'text-center' },
                    { data: 'user_email', name: 'user_email' },
                    { data: 'created_at', name: 'created_at' ,class:'text-center'},
                    // { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
            ],
            buttons: [

                ],
      });


       var themesTable = $('#themes-dataTable').DataTable({
                 processing: true,
                 serverSide: true,
                 ajax:{ url:"{{ route('getDatatableWebsiteThemes') }}",},
                 columns: [
                      { data: 'theme_id', name: 'theme_id' ,class:"id"},
                      { data: 'theme_name_temp', name: 'theme_name_temp' ,class:"code"},
                      { data: 'theme_image', name: 'theme_image' },

                ],
                 buttons: [

                   ],
          });


      $(document).on('click','.view',function(e){

        e.preventDefault();
        placeId = $(this).attr('data-id');
        customerTable.draw();
        $("#view").modal("show");
        clear();
    });

    $("#user-datatable tbody").on('click', "tr", function() {
        $('#user-datatable tbody tr.selected').removeClass('selected');
        $(this).addClass('selected');
        userId = $(this).find("td.sorting_1").text();
        var user_nickname = $(this).find("td.user_nickname").text();

        $("#user_nickname").html("Change password of <b>" + user_nickname + "</b>");
    });

    $("#change-password-form").on('submit', function(e) {

        e.preventDefault();

        var checkSelected = $('#user-datatable tbody tr.selected');
        if (checkSelected.length == 0) {
            toastr.error("Please select the user!");
            return false;
        }

        var form = $(this).serialize();
        form += "&placeId=" + placeId;
        form += "&userId=" + userId;


        $.ajax({
            url: "{{ route('changeNewPassword') }}",
            method: "post",
            data: form,
            dataType: "json",
            success: function(data) {
                if (data.status == 1) {
                    toastr.success("Changed successfully!");
                    clear()
                } else {
                    toastr.error(data.msg);
                }
            },
            error: function() {
                toastr.error("Failed to change!");
            }
        });

    });

    $(document).on('click', ".detail", function(e) {
        e.preventDefault();
        var placeId = $(this).attr('data-id');
        $.ajax({
            url: "{{ route('getDetailPlace') }}",
            method: "get",
            dataType: "json",
            data: { placeId },
            success: function(data) {
                if (data.status == 1) {
                    var hide_service_price = data.data.place.hide_service_price == 1 ? "On" : "Off";
                    $("#detail").modal("show");

                    $("#logo img").attr('src', "{{env('URL_FILE_VIEW')}}" + data.data.place.place_logo);
                    $("#Favicon img").attr('src', "{{env('URL_FILE_VIEW')}}" + data.data.place.place_favicon);
                    $("#business-name").text(data.data.place.place_name);
                    $("#tax-code").text(data.data.place.place_taxcode);
                    $("#price-floor").text(data.data.place.place_worker_mark_bonus);
                    $("#hide-service-price").text(hide_service_price);
                    $("#address").text(data.data.place.place_address);
                    $("#email").text(data.data.place.place_email);
                    $("#interest").text(data.data.place.place_interest);
                    $("#description").text(data.data.place.place_description);
                    $("#business-phone").text(data.data.place.place_phone);
                    $("#website").text(data.data.place.place_website);

                    var monClosed = data.data.place_actiondate.mon.closed;
                    var tueClosed = data.data.place_actiondate.tue.closed;
                    var wedClosed = data.data.place_actiondate.wed.closed;
                    var thuClosed = data.data.place_actiondate.thur.closed;
                    var friClosed = data.data.place_actiondate.fri.closed;
                    var satClosed = data.data.place_actiondate.sat.closed;
                    var sunClosed = data.data.place_actiondate.sun.closed;

                    var monStart = data.data.place_actiondate.mon.start;
                    var tueStart = data.data.place_actiondate.tue.start;
                    var wedStart = data.data.place_actiondate.wed.start;
                    var thuStart = data.data.place_actiondate.thur.start;
                    var friStart = data.data.place_actiondate.fri.start;
                    var satStart = data.data.place_actiondate.sat.start;
                    var sunStart = data.data.place_actiondate.sun.start;

                    var monEnd = data.data.place_actiondate.mon.end;
                    var tueEnd = data.data.place_actiondate.tue.end;
                    var wedEnd = data.data.place_actiondate.wed.end;
                    var thuEnd = data.data.place_actiondate.thur.end;
                    var friEnd = data.data.place_actiondate.fri.end;
                    var satEnd = data.data.place_actiondate.sat.end;
                    var sunEnd = data.data.place_actiondate.sun.end;

                    $("label").removeClass("active")
                    $(".time-start input").css("visibility", "");
                    $(".time-end input").css("visibility", "");

                    if (monClosed == true) {
                        $("div[rel='monday']").find("label[rel='close']").addClass("active");
                        $("#mon-start").css("visibility", "hidden");
                        $("#mon-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='monday']").find("label[rel='open']").addClass("active");
                        $("#mon-start").val(monStart);
                        $("#mon-end").val(monEnd);
                    }

                    if (tueClosed == true) {
                        $("div[rel='tuesday']").find("label[rel='close']").addClass("active");
                        $("#tue-start").css("visibility", "hidden");
                        $("#tue-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='tuesday']").find("label[rel='open']").addClass("active");
                        $("#tue-start").val(tueStart);
                        $("#tue-end").val(tueEnd);
                    }

                    if (wedClosed == true) {
                        $("div[rel='wednesday']").find("label[rel='close']").addClass("active");
                        $("#wed-start").css("visibility", "hidden");
                        $("#wed-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='wednesday']").find("label[rel='open']").addClass("active");
                        $("#wed-start").val(wedStart);
                        $("#wed-end").val(wedEnd);
                    }

                    if (thuClosed == true) {
                        $("div[rel='thursday']").find("label[rel='close']").addClass("active");
                        $("#thu-start").css("visibility", "hidden");
                        $("#thu-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='thursday']").find("label[rel='open']").addClass("active");
                        $("#thu-start").val(thuStart);
                        $("#thu-end").val(thuEnd);
                    }

                    if (friClosed == true) {
                        $("div[rel='friday']").find("label[rel='close']").addClass("active");
                        $("#fri-start").css("visibility", "hidden");
                        $("#fri-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='friday']").find("label[rel='open']").addClass("active");
                        $("#fri-start").val(friStart);
                        $("#fri-end").val(friEnd);
                    }

                    if (satClosed == true) {
                        $("div[rel='saturday']").find("label[rel='close']").addClass("active");
                        $("#sat-start").css("visibility", "hidden");
                        $("#sat-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='saturday']").find("label[rel='open']").addClass("active");
                        $("#sat-start").val(satStart);
                        $("#sat-end").val(satEnd);
                    }

                    if (sunClosed == true) {
                        $("div[rel='sunday']").find("label[rel='close']").addClass("active");
                        $("#sun-start").css("visibility", "hidden");
                        $("#sun-end").css("visibility", "hidden");
                    } else {
                        $("div[rel='sunday']").find("label[rel='open']").addClass("active");
                        $("#sun-start").val(sunStart);
                        $("#sun-end").val(sunEnd);
                    }


                }
            },
            error: function() {
                toastr.error("Failed to get data");
            }
        });
    });


    $(document).on('click', '.setting', function(e) {
        e.preventDefault();
        clear();
        $("#setting").modal("show");
        license = $(this).attr("data-license");
        $("input#get-license").val(license);
    });
    //Create New Website
    $("#clone-update-form").on('submit', function(e) {

        e.preventDefault();

        var checkThemeProperties = $('#themeProperties tr');

        if (checkThemeProperties.length > 1) {
            var checkSelected = $('#themeProperties tr.selected');
            if (checkSelected.length == 0) {
                toastr.error("You have not selected website properties");
                return false;
            }
        }

        var form = $(this).serialize();

        $.ajax({
            url: "{{ route('cloneUpdateWebsite') }}",
            method: "post",
            data: form,
            dataType: "json",
            success: function(data) {
                if (data.status == 1) {
                    toastr.success(data.msg);
                } else {
                    toastr.error(data.msg);
                }
            },
            error: function() {
                toastr.error("Failed to change!");
            }
        });
    });

    $("#themes-datatable tbody").on('click', "tr", function() {
        $('#themes-datatable tbody tr.selected').removeClass('selected');
        $(this).addClass('selected');
        themeId = $(this).find("td.id").text();
        var code = $(this).find("td.code").text();
        $("input#get-code").val(code);
        listThemePropertiesByThemeId(themeId);

        $("input[name='id_properties']").val('');
    });

    $(".btn-copy-theme").on('click', function() {
        $(".text-form").text("Clone Website");
        $("#clone-update-form").find("input[type=submit]").val("Clone Website");
        $("#clone-update-form").find("input[name=action]").val("clone");
    });
    $(".btn-copy-properties").on('click', function() {
        $(".text-form").text("Update Website");
        $("#clone-update-form").find("input[type=submit]").val("Update Website");
        $("#clone-update-form").find("input[name=action]").val("update");
    });

    $("#themeProperties tbody").on('click', "tr", function() {
        $('#themeProperties tbody tr.selected').removeClass('selected');
        $(this).addClass('selected');
        id = $(this).attr("properties-id");
        $("input[name='id_properties']").val(id);
    });

    // custom properties
    $(document).on('click', ".btn-custom-properties", function(e) {
        e.preventDefault();
        placeId = $(this).attr('data-id');
        customPropertytable.draw();
        $("#custom-properties-modal").modal("show");
        $(".resetAddProperty").trigger('click');
    });

    $("#addText").on('click', function() {
        $(".addText").show(200);
        $(".addImage").hide(200);
    });

    $("#addImage").on('click', function() {
        $(".addImage").show(200);
        $(".addText").hide(200);
    });

    $(".resetAddProperty").on('click', function() {
        clear();
        $("#custom-properties-title").text("Add");
        $("#custom-properties-form").find("input[name='action']").val("create");

    });

    $(document).on("click", ".editValueProperty", function(e) {
        e.preventDefault();
        var variable = $(this).attr('data-id');
        var name = $(this).parent().parent().find(".wp_name").text();
        var value = $(this).parent().parent().find(".wp_value").text();
        var img = $(this).parent().parent().find(".wp_value img").attr('src');

        if (img) {
            $("#addImage").trigger("click");
        } else {
            $("#addText").trigger("click");
        }

        $("#custom-properties-title").text("Edit");
        $("#custom-properties-form").find("input[name='action']").val("update");
        $("#custom-properties-form").find("input[name='valuePropertyId']").val(variable);

        $("#custom-properties-form").find("input[name='variable']").val(variable);
        $("#custom-properties-form").find("input[name='name']").val(name);
        $("#custom-properties-form").find("input[name='value']").val(value);
        $("#custom-properties-form").find("#previewImageValue").attr("src", img);

        $("#custom-properties-form").find("input[name='variable']").attr("readonly", true);
    });

    $(document).on("click", ".deleteValueProperty", function(e) {
        e.preventDefault();
        if (confirm("Are you sure do you want to delete this data?")) {
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ route('deleteValueProperty') }}",
                method: "get",
                data: {
                    __token: "{{csrf_token()}}",
                    id,
                    placeId,
                },
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                        toastr.success("Deleted successfully!");
                        customPropertytable.draw();
                    }
                },
                error: function() {
                    toastr.error("Failed to delete!");
                }
            });
        };
    });

    $("#custom-properties-form").on("submit", function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var form_data = new FormData(form);
        form_data.append('placeId', placeId);
        $.ajax({
            url: "{{ route('saveCustomValueProperty') }}",
            method: "post",
            dataType: "json",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                customPropertytable.draw();
                toastr.success("Saved successfully!");
                clear();

            },
            error: function() {
                toastr.error("Failed to save!");
            }
        });
    });

    //=============================
    //auto coupon
    $(document).on('click', ".btn-auto-coupon", function(e) {
        e.preventDefault();
        placeId = $(this).attr('data-id');
        autoCouponTable.draw();
        $("#auto-coupon-modal").modal("show");
        $(".resetAddAutoCoupon").trigger("click");
    });

    $(".resetAddAutoCoupon").on('click', function() {
        clear();
        $("#auto-coupon-title").text("Add");
    });

    autoCouponTable = $('#auto-coupon-datatable').DataTable({
        // dom: "lBfrtip",
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: true,
        buttons: [

        ],

        ajax: {
            url: "{{ route('getAutoCouponDatatable') }}",
            data: function(data) {
                data.placeId = placeId;
            },
        },
        columns: [
            { data: 'template_id', name: 'template_id', class: "template_id" },
            { data: 'template_title', name: 'template_title', class: "template_title" },
            { data: 'template_discount', name: 'template_discount', class: "template_discount" },
            { data: 'template_linkimage', name: 'template_linkimage', class: "template_linkimage" },
            { data: 'template_list_service', name: 'template_list_service', class: "template_list_service" },
            { data: 'template_type_id', name: 'template_type_id', class: "template_type_id" },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' }
        ]
    });
    //save
    $("#auto-coupon-form").on("submit", function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var form_data = new FormData(form);
        form_data.append('placeId', placeId);
        $.ajax({
            url: "{{ route('saveAutoCoupon') }}",
            method: "post",
            dataType: "json",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                autoCouponTable.draw();
                toastr.success("Saved successfully!");
                clear();

            },
            error: function() {
                toastr.error("Failed to save!");
            }
        });
    });

    $(document).on("click", ".editAutoCoupon", function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");

        $.ajax({
            url: "{{ route('getAutoCouponById') }}",
            method: "get",
            data: {
                id,
                placeId,
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    $("#auto-coupon-title").text("Update");
                    $("input[name='action']").val("update");

                    $("input[name='title']").val(data.data.template_title);
                    $("input[name='discount']").val(data.data.template_discount);
                    $("input[name='discountType']").val(data.data.template_type);
                    $("#previewImageAutoCoupon").src(data.data.template_title);
                    $("input[name='services']").val("sada");
                    $("input[name='couponType']").val("sdf");
                }
            },
            error: function() {
                toastr.error("Failed to get!");
            }
        });



    });

    $(document).on("click", ".deleteAutoCoupon", function(e) {
        if (!confirm("Are you sure you want to delete this data?")) {
            return false;
        }

        var id = $(this).attr("data-id");
        $.ajax({
            url: "{{ route('deleteAutoCoupon') }}",
            method: "get",
            data: {
                id,
                placeId,
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    toastr.success("Deleted successfully!");
                    autoCouponTable.draw();
                }
            },
            error: function() {
                toastr.error("Failed to delete!");
            }
        });
    });


});
      });

      $("#themes-datatable tbody").on('click',"tr",function(){
             $('#themes-datatable tbody tr.selected').removeClass('selected');
             $(this).addClass('selected');
             themeId = $(this).find("td.id").text();
             var code = $(this).find("td.code").text();
             $("input#get-code").val(code);
             listThemePropertiesByThemeId(themeId);

             $("input[name='id_properties']").val('');
       });

      $(".btn-copy-theme").on('click',function(){
            $(".text-form").text("Clone Website");
            $("#clone-update-form").find("input[type=submit]").val("Clone Website");
            $("#clone-update-form").find("input[name=action]").val("clone");
      });
      $(".btn-copy-properties").on('click',function(){
            $(".text-form").text("Update Website");
            $("#clone-update-form").find("input[type=submit]").val("Update Website");
            $("#clone-update-form").find("input[name=action]").val("update");
      });
      $("#themeProperties tbody").on('click',"tr",function(){
            $('#themeProperties tbody tr.selected').removeClass('selected');
            $(this).addClass('selected');
            id = $(this).attr("properties-id");
            $("input[name='id_properties']").val(id);
       });
      $(document).on('click','.extension-service',function(){
          // $("#extension_service").modal('show');
          // return;
          let place_id = $(this).attr('place_id');
          $.ajax({
              url:"{{ route('get-service-place') }}",
              method:"get",
              dataType:"html",
              data:{place_id},
              success:function(data){
                  data = JSON.parse(data);
                  if(data.status == 'error')
                      toastr.error(data.message);
                  else{
                      let service_html = "";
                      $.each(data.service_combo_list,function(ind,val){
                          service_html += `
                              <tr>
                                  <td><input type="hidden" name="cs_id[]" value="`+val['get_combo_service']['id']+`">`+val['get_combo_service']['id']+`</td>
                                  <td>`+val['get_combo_service']['cs_name']+`</td>
                                  <td class="text-right">`+val['get_combo_service']['cs_price']+`</td>
                                  <td>`+val['cs_date_expire']+`</td>
                                  <td><input name="expire_date[]" type="text" expire_date="`+val['cs_date_expire']+`" class="new_date_expire form-control form-control-sm"></td>
                              </tr>
                          `;
                      });
                      service_html += `<input type="hidden" name="place_id" value="`+place_id+`">`;
                      $("#service_table_body").html(service_html);
                      $("#extension_service").modal('show');
                  }
              },
              error:function(){
                  toastr.error("Get Service List Failed!");
              }
          });
      });

      $(document).on('focus','.new_date_expire',function(){
          let expire_date = $(this).attr('expire_date');
          $(this).datepicker({
              todayHighlight: true,
              startDate: expire_date,
              minDate:0,
          });
      });

      $(".save-expire").click(function () {
          var formData = new FormData($(this).parents('form')[0]);
          formData.append('_token','{{csrf_token()}}');
          $.ajax({
              url:"{{ route('save-expire-date') }}",
              method:"post",
              dataType:"html",
              data:formData,
              contentType: false,
              processData:false,

              success:function(data){
                  data = JSON.parse(data);
                  if(data.status == 'error')
                      toastr.error(data.message);
                  else{
                      toastr.success(data.message);
                      $("#service-form")[0].reset();
                      $("#extension_service").modal('hide');
                  }
              },
              error:function(){
                  toastr.error("Change Expire Date Failed!");
              }
          });
      })
        $(".cancel-change").click(function(){
            $("#service-form")[0].reset();
            $("#extension_service").modal('hide');
        })
    });


</script>
@endpush
