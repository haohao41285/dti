@extends('layouts.app')
@section('content-title')
    SETUP TERM SERVICE
@stop
@section('content')
<div class="col-12 row">
    <div class="col-md-8">
        <div class="card shadow mb-3 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary">Team Term Service</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="thead-light">
                            <th class="text-center">ID</th>
                            <th hidden></th>
                            <th>Service</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-center" style="width:100px">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="card shadow mb-3 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary tt-tip">Add Term Service</h6>
            </div>
            <div class="card-body">
                <form id="form-add-edit-term">
                    <div class="form-group">
                        <label for=""><b>Service</b></label>
                        <select name="service_id" class="form-control form-control-sm" id="service_id">
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->cs_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="height: 300px">
                        <button type="button" class="btn btn-sm btn-primary" id="upload-file">Upload Files</button><br>

                        <div class="form-group mt-3">
                            <label><b>File Name</b></label>
                            <input type="hidden" class="form-control form-control-sm" value="" name="file_name" id="file_name_hidden">
                            <input type="text" class="form-control form-control-sm" value="" disabled name="file_name" id="file_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-primary float-right submit-tt ml-2">Submit</button>
                        <button type="button" class="btn btn-sm btn-danger float-right cancel-tt">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- MODAL UPLOAD FILE PDF --}}
<div class="modal fade" id="modal-upload-file">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title text-center">Choose File</h4>
          <button type="button" class="close btn-close" >&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
            <form id="form-upload-file">
                 <input type="file" id="image" hidden name="image" onchange="loadFile(event)">
                <button type="button" onclick="ChangeImage()" id="date" class="btn btn-sm btn-info"><span class="fas fa-plus"></span> Upload File</button>
                <div class="file-list row">
                    
                </div>
                <div  class="text-center mt-1" style="">
                    {{-- <img src="" id="preview-image" style="max-width:80%"  alt=""> --}}
                    {{-- <embed src="" id="preview-image" style="max-width:100%" type="application/pdf"> --}}
                    <iframe src="" id="preview-image" style="border: none;width: 100%;height: 300px" ></iframe>
                </div>
            </form>
               
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm btn-close">Close</button>
          <button type="button" class="btn btn-primary btn-sm accept" >Accept</button>
        </div>
        
      </div>
    </div>
  </div>
@stop
@push('scripts')
<script type="text/javascript">
//DEFINE VAR
var id = 0;
var old_service = 0;

    var loadFile = function(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var preview_image = document.getElementById('preview-image');
                preview_image.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        };

    function ChangeImage(){
        old_service = 1;
        $("#image").click();
    }
$(document).ready(function($) {

    function getFiles(){
        $.ajax({
            url: '{{ route('setup_term_service.get_files') }}',
            type: 'GET',
            dataType: 'html',
        })
        .done(function(data) {
            data = JSON.parse(data);

            if(data.files.length > 0){

                var original_src = '{{ asset('/') }}';
                var file_html = '';

                $.each(data.files, function(index, val) {
                    file_html += `
                    <div class="col-md-4">
                        <embed src="`+original_src+val.file_name+`" style="max-width:100%" type="application/pdf">
                        <input type="radio" name="file_name_old" id="`+val.id+`" class="file_name_old" value="`+val.file_name+`" />
                        <label for="`+val.id+`">`+val.file_name+`</label>
                    </div>
                    `;
                });

                $(".file-list").html(file_html);
            }
                
        })
        .fail(function() {
            console.log("error");
        });
    }

    var old_team_type_name = "";

    dataTable = $("#dataTable").DataTable({
        processing: true,
        serverSide: true,
        autoWidth: true,
        buttons: [],
        ajax: { url: "{{route('setup_term_service.datatable')}}" },
        columns: [
            { data: 'id', name: 'id', class: 'text-center' },
            { data: 'service_id', name: 'service_id',class:'d-none' },
            { data: 'service_name', name: 'service_name' },
            { data: 'file_name', name: 'file_name' },
            { data: 'status', name: 'status', class: 'text-center' },
            { data: 'created_at', name: 'created_at', class: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
        ],
        fnDrawCallback: function(oSettings) {
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function(html) {
                var switchery = new Switchery(html, {
                    color: '#0874e8',
                    className: 'switchery switchery-small'
                });
            });
        }
    })
    $("#upload-file").click(function(){
        getFiles();
        $('#modal-upload-file').modal('show');
    })
    
    $('#dataTable tbody').on('click', 'tr', function() {

        $("#service_id").val(dataTable.row(this).data()['service_id']);
        $("#file_name").val(dataTable.row(this).data()['file_name']);
        $("#file_name_hidden").val(dataTable.row(this).data()['file_name']);
        $(".tt-tip").text("Edit Term Service");
        id = dataTable.row(this).data()['id'];
    });
    $(document).on('click', '.submit-tt', function() {

        var formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{ csrf_token() }}');
        formData.append('id',id);

        $.ajax({
            url: '{{route('setup_term_sevice.save')}}',
            type: 'POST',
            contentType: false,
            processData: false,
            dataType: 'html',
            data: formData,
            })
            .done(function(data) {
                data = JSON.parse(data);
                if (data.status === 'error')
                    toastr.error(data.message);
                else {
                    clearView();
                    dataTable.draw();
                }
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                toastr.error('Error!');
            });
    });
    $(".cancel-tt").click(function() {
        clearView();
    });

    $(document).on("click", ".delete-tt", function() {
        
        if (confirm("Do you want to delete this team type?")) {

            var tt_id = $(this).attr('tt_id');

            $.ajax({
                    url: '{{route('delete-team-type')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: { 
                        tt_id: tt_id,
                        old_team_type_name: old_team_type_name
                    },
                })
                .done(function(data) {
                    data = JSON.parse(data);
                    if (data.status == 'error')
                        toastr.error(data.message);
                    else {
                        toastr.success(data.message);
                        dataTable.draw();
                        clearView();
                    }
                })
                .fail(function() {
                    console.log("error");
                });
        }
    });
    $(".accept").click(function(){
        if(old_service !== 0){
            var formData = new FormData($("#form-upload-file")[0]);
            formData.append('_token','{{ csrf_token() }}');
            $.ajax({
                url: '{{ route('setup_term_service.upload_file') }}',
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'html',
                data: formData,
            })
            .done(function(data) {
                data = JSON.parse(data);
                if(data.status === 'error')
                    toastr.error(data.message);
                else{
                    $("#file_name").val(data.file_name);
                    $("#file_name_hidden").val(data.file_name);
                    clearModal();
                }
            })
            .fail(function() {
                console.log();
            });
        }else{
            let old_service = $("input[type='radio']:checked").val();
            $("#file_name").val(old_service);
            $("#file_name_hidden").val(old_service);
            clearModal();
        }
            
    });
    $(".btn-close").click(function(){
        clearModal();
    });

    function clearModal(){
        $("#form-upload-file")[0].reset();
        $("#modal-upload-file").modal('hide');
        $("#preview-image").attr('src','');
        old_service = 0;
        id = 0;
    }
    function clearView(){
        $("#form-add-edit-term")[0].reset();
        old_service = 0;
        id = 0;
        $(".tt-tip").text('Add Term Service');
    }
    $(document).on('click','.custom-control-input',function(){
        $.ajax({
            url: '{{ route('setup_term_service.change_status') }}',
            type: 'GET',
            dataType: 'html',
            data: {id: id},
        })
        .done(function(data) {
            data = JSON.parse(data);
            if(data.status === 'error')
                toastr.error(data.message);
            else{
                toastr.success(data.message);
            }
            dataTable.draw();
            clearView();
        })
        .fail(function() {
            toastr.error("Failed!");
        });
    })
    $(document).on('click','.file_name_old',function(){
        var file_name = $(this).val();
        $("#preview-image").attr('src',"{{ asset('/') }}"+file_name);
    });
    $(document).on('click','.btn-delete',function(){
        if(confirm('Do you want to delete this Term Service?')){
             $.ajax({
                url: '{{ route('setup_term_service.delete') }}',
                type: 'POST',
                dataType: 'html',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
            })
            .done(function(data) {
                data = JSON.parse(data);
                if(data.status === 'error')
                    toastr.error(data.message);
                else{
                    toastr.success(data.message);
                }
                dataTable.draw();
                clearView();
            })
            .fail(function() {
                toastr.error("error");
            });
        }
    })
});

</script>
@endpush
