@extends('layouts.app')
@section('content-title')
    {{isset($service_type)?"EDIT #".$service_type->web_service_type_name:"ADD SERVICE TYPE"}}
@endsection
@push('scripts')
@endpush
@section('content')
<div class="card shadow mb-4 ">
    <div class="card-body">
        <form action="{{route('web_service.save')}}" method="POST">
            @csrf
            <input type="hidden" value="{{$id}}" name="web_service_type_id">
            <div class="table-responsive">
                <div class="">
                    <label for="web_service_type_name">Service Type Name</label>
                    <input type="text" class="form-control form-control-sm"
                    id="web_service_type_name" name="web_service_type_name"
                    value="{{isset($service_type)?$service_type->web_service_type_name:""}}">
                </div>
                @if(isset($service_type))
                    <label for="">List Service</label>
                    <div class="row px-4 py-2">
                        @foreach($service_type->services as $service)
                            <div style="position:relative" id="service-{{$service->web_service_id}}">
                                <img style='height: 3rem;' class="m-2" src='{{env('URL_FILE_VIEW').$service->web_service_image}}' alt=''>
                                <span class="text-danger remove-service" service={{$service->web_service_id}} style="position:absolute;top:2px;right:5px"><i class="fas fa-times"></i></span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="">
                    <span class="text-primary">Drag multiple files to the box below for multi upload or click to select files. This is for demonstration purposes only, the files are not uploaded to any server.</span>
                    <div id="multiUploadImages" required class="dropzone"></div>
                </div>
                <div class="list_image"></div>
                <div class="float-right mt-2">
                    <button class="btn btn-sm btn-primary btn-submit" type="button">Submit</button>
                    <button class="btn btn-sm btn-danger" type="button">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
        
Dropzone.autoDiscover = false;    
function initializeDropZone() {
    myDropzone = new Dropzone('div#multiUploadImages', {
        url: '{{ route('web_service.upload_multi_image') }}',
        headers: {
            'X-CSRF-TOKEN': '{!! csrf_token() !!}'
        },
        addRemoveLinks: true,
        autoProcessQueue: true,
        uploadMultiple: true,
        parallelUploads: 10,
        maxFiles: 10,
        maxFilesize: 2,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        dictFileTooBig: 'Image is bigger than 2MB',
        addRemoveLinks: true,
        removedfile: function(file) {
            var name = file.name;
            $('#'+name.replace(/[^A-Z0-9]+/ig,'_')).val('');
            var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
            },
            init: function () {

        var myDropzone = this;

        this.on('sending', function (file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $("#service_form").serializeArray();
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            });
            // console.log(formData);

        });
        },
        successmultiple: function (file, response) {
            // console.log(file);
            // console.log(response);
            $.each( response, function( i, val ) {
                var str = val.slice(val.lastIndexOf("/")+1);
                
            $('.list_image').append('<input type="hidden" name="web_service_image[]" id="'+str.replace(/[^A-Z0-9]+/ig,'_')+'" value="'+val+'">');
            });

            $("#success-icon").attr("class", "fas fa-thumbs-up");
            $("#success-text").html(response.message);
        },
    });
}
function readURL(input) {
    if (input.files && input.files[0]) {
        $('img').show();
        var reader = new FileReader();
        reader.onload = function(e) {
            $($(input).attr("data-target")).attr('src', e.target.result);
            $($(input).attr("data-target")).hide();
            $($(input).attr("data-target")).fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }      
}
$(document).ready(function(){
    initializeDropZone();
    $("input[type=file]").change(function() {
        readURL(this);
    });
    $(".btn-submit").click(function(){
        let web_service_type_name = $("#web_service_type_name").val();
        if(web_service_type_name == ""){
            toastr.error('Serivce Type Name is required!');
            return;
        }
        $(this).parents('form')[0].submit();
    });
    $(".remove-service").click(function(){
        if(confirm('Do you want to delete this service image?')){
            let service_id = $(this).attr('service');
            $.ajax({
                type: "POST",
                url: "{{route('web_service.delete_service')}}",
                data: {
                    service_id: service_id,
                    _token: '{{csrf_token()}}',
                },
                dataType: "html",
                success: function (response) {
                    response = JSON.parse(response);
                    if(response.status == 'error')
                        toastr.error(response.message);
                    else{
                        $('#service-'+service_id).remove();
                        toastr.success(response.message);
                    }
                },
                error: function(){
                    toastr.error('Failed!');
                }
            });
        }
        else
            return;
    });
})
</script>
@endpush