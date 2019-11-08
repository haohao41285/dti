@extends('layouts.app')
@section('content-title')
{{-- Places --}}
@endsection
@push('styles')
@endpush
@section('content')
<div class="col-12 row">
    <div class="col-8 ">
        <div class="form-group">
            <button class="btn-sm btn-primary btn " id="btn-coupon">Coupon</button>
            <button class="btn-sm btn-danger btn " id="btn-promotion">Promotion</button>
        </div>
        <div class="card shadow mb-4 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary table-title">Auto coupon list </h6>
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
                                <th>Categories</th>
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
            <button class="btn btn-sm btn-warning resetAdd">Reset Add</button>
        </div>
        <div class="card shadow mb-4 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary " id="auto-template-title">Add</h6>
            </div>
            <div class="card-body">
                <form method="post" id="auto-template-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row col-12">
                        <label class="col-5">Title</label>
                        <input class="col-7 form-control-sm form-control" type="text" name="title">
                    </div>
                    <div class="form-group row col-12">
                        <label class="col-5">Discount</label>
                        <input class="col-7 form-control-sm form-control" type="number" name="discount">
                    </div>
                    <div class=" form-group row col-12">
                        <label class="col-5">Discount Type </label>
                        {{-- <input class="col-7 form-control-sm form-control" type="text" name="discountType"> --}}
                        <select class="col-7 form-control-sm form-control" name="discountType">
                            <option value="1">$</option>
                            <option value="0">%</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label>Image</label>
                        <div class="previewImage">
                            <img id="previewImageAutoCoupon" src="{{ asset('/images/no-image.png') }}">
                            <input type="file" class="custom-file-input" name="image" previewimageid="previewImageAutoCoupon">
                        </div>
                    </div>
                    <div class=" form-group row col-12">
                        <label class="col-5">Cate Services</label>
                        <input type="text" name="services">
                    </div>
                    <div class=" form-group row col-12">
                        <label class="col-5">Template Type </label>
                        {{-- <input class="col-7 form-control-sm form-control" type="text" name="couponType"> --}}
                        <select class="col-7 form-control-sm form-control" name="templateType">
                            <option>-- Template Type --</option>
                            @foreach ($templateType as $element)
                            <option class="{{$element->template_type_table_type == 1 ? "coupon" : "promotion"}}" value="{{$element->template_type_id}}">{{$element->template_type_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 row">
                        <label class="col-5"></label>
                        <input class="btn-sm btn btn-primary" type="submit" value="Save">
                        <input type="hidden" name="action" value="create">
                        {{-- <input type="hidden" name="valuePropertyId"> --}}
                        <input type="hidden" name="id">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function reset() {
    $("#auto-template-title").text("Add");
    $("input[name='action']").val("create");
    $("#auto-template-form")[0].reset();
    $(".previewImage img").attr("src", "{{ asset('images/no-image.png') }}");
    $("input[name='id']").val('');

    $("select[name='templateType']").find("option:selected").attr("selected", false);
    $("select[name='templateType']:first-child").attr("selected", true);
}
$(document).ready(function() {
    var type = null;
    perviewImage();

    $("#btn-coupon").on('click', function(e) {
        e.preventDefault();
        type = 1;
        $(".table-title").text("Auto coupon list");
        autoTemplateTable.draw();
        reset();
        $(".coupon").show();
        $(".promotion").hide();
    });

    $("#btn-promotion").on('click', function(e) {
        e.preventDefault();
        type = 2;
        $(".table-title").text("Auto promotion list");
        autoTemplateTable.draw();
        reset();
        $(".coupon").hide();
        $(".promotion").show();
    });

    autoTemplateTable = $('#auto-coupon-datatable').DataTable({
        // dom: "lBfrtip",
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: true,
        buttons: [

        ],

        ajax: {
            url: "{{ route('getAutoTemplateDatatable') }}",
            data: function(data) {
                // data.placeId = placeId;
                data.type = type;
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
    $("#auto-template-form").on("submit", function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var form_data = new FormData(form);
        form_data.append('type', type);
        $.ajax({
            url: "{{ route('saveAutoTemplate') }}",
            method: "post",
            dataType: "json",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                autoTemplateTable.draw();
                toastr.success("Saved successfully!");
                $(".resetAdd").trigger("click");

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
            url: "{{ route('getAutoTemplateById') }}",
            method: "get",
            data: {
                id,
                // placeId,
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    $("#auto-template-title").text("Update");
                    $("input[name='action']").val("update");

                    $("input[name='id']").val(id);
                    $("input[name='title']").val(data.data.template_title);
                    $("input[name='discount']").val(data.data.template_discount);

                    $("select[name='discountType']").find("option:selected").attr("selected", false);

                    $("select[name='discountType']").find("option[value='" + data.data.template_discount_type + "']").attr("selected", true);


                    $("#previewImageAutoCoupon").attr("src", "{{env('URL_FILE_VIEW')}}" + data.data.template_linkimage);
                    $("input[name='services']").val("sada");

                    $("select[name='templateType']").find("option:selected").attr("selected", false);
                    $("select[name='templateType']").find("option[value='" + data.data.template_type_id + "']").attr("selected", true);

                }
            },
            error: function() {
                toastr.error("Failed to get!");
            }
        });



    });

    $(document).on("click", ".deleteAutoCoupon", function(e) {
        e.preventDefault();

        if (!confirm("Are you sure you want to delete this data?")) {
            return false;
        }

        var id = $(this).attr("data-id");
        $.ajax({
            url: "{{ route('deleteAutoTemplate') }}",
            method: "get",
            data: {
                id,
                // placeId,
            },
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    toastr.success("Deleted successfully!");
                    autoTemplateTable.draw();
                }
            },
            error: function() {
                toastr.error("Failed to delete!");
            }
        });
    });

    $(".resetAdd").on("click", function(e) {
        e.preventDefault();
        reset();
    });

    $("#btn-coupon").trigger("click");
});

</script>
@endpush
