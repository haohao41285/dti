@extends('layouts.app')
@section('title')
    Role List
@stop
@section('content')
    <div class="row">
        <div class="col-md-6">
            <h5><b>Role List</b></h5>
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th>Permission Name</th>
                    <th>Menu Name</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th hidden></th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-5 offset-md-1" style="padding-top: 0px">
            <form id="add-edit-form">
                <h5><b class="tip">Add Permission</b></h5>
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" class="form-control form-control-sm" name="permission_name" id="permission_name">
                </div>
                <div class="form-group">
                    <label for="">Menu</label>
                    <select name="menu_id" class="form-control form-control-sm" id="menu_id">
                        <option value="">Other</option>
                        @foreach($menu_list as $menu)
                             <option value="{{$menu->id}}">{{$menu->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-danger float-right cancel-permission ml-2">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary float-right submit-permission">Submit</button>
                </div>
            </form>
        </div>
    </div>

@stop
@push('scripts')
    <script type="text/javascript">
        //DEFINE VAR
        var permission_id = 0;
        $(document).ready(function($) {
            dataTable = $("#dataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                buttons: [
                ],
                ajax:{ url:"{{route('permission-datatable')}}"},
                columns:[
                    {data:'id', name:'id',class: 'text-center'},
                    {data:'permission_name', name:'permission_name'},
                    {data:'menu_name', name:'menu_name'},
                    {data:'status', name:'status',class:'text-center'},
                    {data:'action', name:'action',searching:false,orderable:false,class:'text-center'},
                    {data:'menu_id', name:'menu_id',class: 'd-none'},
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
                var status = $(this).siblings('input').attr('status');
                $.ajax({
                    url: '{{route('change-status-permission')}}',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        status: status,
                        permission_id: permission_id,
                        _token: '{{csrf_token()}}'
                    },
                })
                    .done(function(data) {
                        data = JSON.parse(data);
                        if(data.status == 'error'){
                            toastr.error(data.message);
                        }
                        else
                            toastr.success(data.message);

                        dataTable.ajax.reload(null, false);
                        clearView();
                    })
                    .fail(function(xhr, ajaxOptions, thrownError) {
                         toastr.error('Change Failed!');
                        // console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    });

            });
            $('#dataTable tbody').on( 'click', 'tr', function () {

                $("#permission_name").val(dataTable.row(this).data()['permission_name']);
                $("#menu_id").val(dataTable.row(this).data()['menu_id']);
                $(".tip").text("Edit Permission");
                permission_id = dataTable.row(this).data()['id'];

            });
            $(document).on('click','.submit-permission',function(){

                var formData = $("#add-edit-form").serialize();
                formData += '&permission_id='+permission_id;

                $.ajax({
                    url: '{{route('save-permission')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: formData,
                })
                    .done(function(data) {
                        data = JSON.parse(data);
                        console.log(data);
                        if(data.status == 'error'){
                            if(typeof(data.message) == 'string')
                                toastr.error(data.message);
                            else
                                $.each(data.message,function(ind,val){
                                    toastr.error(val);
                                });
                        }
                        else{
                            toastr.success(data.message);
                            dataTable.draw();
                            clearView();
                        }
                    })
                    .fail(function(xhr, ajaxOptions, thrownError) {
                        toastr.error('Save Failed!');
                        console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    });
            });
            $(".cancel-permission").click(function(){
                clearView();
            })
            function clearView(){
                $("tip").text("Add Permission");
                $("#add-edit-form")[0].reset();
                permission_id = 0;
            }
            $(document).on('click','.delete-permission',function () {
                if(confirm("Do you want delete this permission?")){
                    $.ajax({
                        url: '{{route('delete-permission')}}',
                        type: 'DELETE',
                        dataType: 'html',
                        data: {
                            permission_id: permission_id,
                            _token: '{{csrf_token()}}'
                        },
                    })
                        .done(function(data) {
                            data = JSON.parse(data);
                            console.log(data);
                            if(data.status == 'error'){
                                toastr.error(data.message);
                            }
                            else{
                                toastr.success(data.message);
                                dataTable.ajax.reload(null, false);
                                clearView();
                            }
                        })
                        .fail(function(xhr, ajaxOptions, thrownError) {
                            toastr.error('Save Failed!');
                            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        });
                }
            })
        });
    </script>
@endpush
