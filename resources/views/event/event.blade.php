@extends('layouts.app')
@section('title')
    Event List
@stop
@section('content')
    <div class="col-12 row">
        <div class="col-md-6">
            <h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info">EVENT LIST</h4>
            <table class="table table-sm table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th hidden></th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-5 offset-md-1" style="padding-top: 0px">
         <form action="">
            <h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info event-tip">ADD EVENT</h4>
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control form-control-sm" name="name" id="name">
            </div>
            <div class="form-group">
                <label for="">Date</label>
                <input type="text" id="date" name="date" class="form-control form-control-sm">
            </div>
            <div class="form-group">
                <input type="file" id="image" hidden name="image" onchange="loadFile(event)">
                <input type="button" onclick="ChangeImage()" value="Change Image" id="date" class="btn btn-sm btn-info">
                <div class="text-center mt-1">
                    <img src="" id="preview-image" style="max-width:80%"  alt="">
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-sm btn-danger float-right cancel-role ml-2">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary float-right submit-event">Submit</button>
            </div>
         </form>
        </div>
    </div>

@stop
@push('scripts')
    <script type="text/javascript">

        var loadFile = function(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var preview_image = document.getElementById('preview-image');
                preview_image.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        };

        function ChangeImage(){
            $("#image").click();
        }
        $("#date").datepicker();
        //DEFINE VAR
        var event_id = 0;
        $(document).ready(function($) {
            dataTable = $("#dataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: true,
                buttons: [
                ],
                ajax:{ url:"{{route('event-datatable')}}"},
                columns:[
                    {data:'id', name:'id',class:'text-center'},
                    {data:'image_hidden', name:'image_hidden',class:'d-none'},
                    {data:'name', name:'name'},
                    {data:'date', name:'date',class:'text-center'},
                    {data:'image', name:'image',class:'text-center'},
                    {data:'status', name:'status',class:'text-center'},
                    {data:'action', name:'action',orderable: false, searchable: false,class:'text-center'},
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
                    url: '{{route('change-status-event')}}',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        status: status,
                        id: id,
                        _token: '{{csrf_token()}}'
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
                        data = JSON.parse(data.responseText);
                        alert(data.message);
                        dataTable.draw();
                    });

            });
            $('#dataTable tbody').on( 'click', 'tr', function () {

                $("#name").val(dataTable.row(this).data()['name']);
                $("#date").val(dataTable.row(this).data()['date']);
                $(".event-tip").text("EDIT EVENT");
                $("#preview-image").attr('src',dataTable.row(this).data()['image_hidden'])
                event_id = dataTable.row(this).data()['id'];

            });
            $(document).on('click','.submit-event',function(e){

                var date = $("#date").val();
                var name = $("#name").val();

                if(date == "" && name == ""){
                    toastr.error('Enter Information!');
                    return;
                    e.preventDefault();
                }

                if(date != "" && name != ""){

                    let formData = new FormData($(this).parents('form')[0]);
                    formData.append('_token','{{csrf_token()}}');
                    formData.append('id',event_id);

                    $.ajax({
                        url: '{{route('add-event')}}',
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
                    })
                        .done(function(data) {
                            let message = '';
                            if(data.status == 'error'){
                                if( typeof(data.message) == 'string')
                                    toastr.error(data.message);
                                else{
                                    $.each(data.message,function (index,val) {
                                        message += val+'\n';
                                    });
                                    toastr.error(message);
                                }
                            }else{
                                toastr.success(data.message);
                                console.log(data);
                                dataTable.draw();
                                clearView();
                            }
                        })
                        .fail(function() {
                            toastr.error('Failed!');
                        });
                }
            });
            $(".cancel-role").click(function(){
                clearView();
            })
            function clearView(){
                $(".event-tip").text("ADD EVENT");
                $("#name").val("");
                $("#date").val("");
                event_id = 0;
                $("#preview-image").attr('src',"");
            }
            $(document).on('click','.event-delete',function () {
                clearView();
                if(confirm('Do you want to delete this event?')){
                    let id = $(this).attr('id');
                    $.ajax({
                        url: '{{route('delete-event')}}',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            id:id,
                            _token: '{{csrf_token()}}'
                        },
                    })
                        .done(function(data) {
                            data = JSON.parse(data);
                            if(data.status == 'error')
                                toastr.error(data.message);
                            else{
                                toastr.success(data.message);
                                dataTable.draw();
                                clearView();
                            }
                        })
                        .fail(function() {
                            toastr.error('Failed!');
                        });
                }
            })
        });
    </script>
@endpush
