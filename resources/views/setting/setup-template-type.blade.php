@extends('layouts.app')
@section('content-title')
Template Type
@endsection
@push('scripts')
@endpush
@section('content')
<div class="row col-12">
    <div class="col-12 ">
        <div class="form-group">
            <button class="btn-sm btn-primary btn " id="btn-coupon">Coupon</button>
            <button class="btn-sm btn-danger btn " id="btn-promotion">Promotion</button>
        </div>
        <div class="card shadow mb-4 ">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary table-title">Auto coupon list</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive dataTables_scrollBody dataTables_scroll">
                    <table class="table table-sm table-bordered table-hover dataTable" id="coupon-type-datatable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="thead-light">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- app modal --}}
<div class="modal fade" id="coupon-type-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form mothod="post" id="save-coupon-type">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Coupon Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row col-12">
                        <label class="col-2 ">Name</label>
                        <input class="form-control-sm form-control col-10" type="text" name="name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-sm btn btn-primary">Save changes</button>
                    <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="hidden" name="action" value="Create">
                    <input type="hidden" name="typeId">
                </div>
        </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
function clear() {
    $("input[name='name']").val('');

    // $(".previewImage img").attr('src', "{{asset('images/no-image.png')}}");
}

function deleteById(id, url) {
    if (confirm("Are you sure do you want to delete this data!")) {
        var result = null;
        $.ajax({
            async: false,
            url: url,
            method: "post",
            data: {
                _token: "{{csrf_token()}}",
                id: id,
            },
            dataType: "json",
            success: function(data) {
                if (data.status == 1) {
                    toastr.success("Deleted successfully!");
                    result = true;
                    return;
                }
            },
            error: function() {
                toastr.error("Failed to delete!");
                result = false;
                return;
            }
        });
    }
    return result;
}

function save(form_data, url) {
    $.ajax({
        url: url,
        method: "post",
        dataType: "json",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.status == 1) {
                toastr.success("Saved successfully!");
            } else {
                toastr.error(data.msg);
            }
        },
        error: function() {
            toastr.error("Failed to save!");
        }
    });
}

$(document).ready(function() {
    var type = null;
    var table = $('#coupon-type-datatable').DataTable({
        // dom: "lBfrtip",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('getDatatableSetupTypeTemplate') }}",
            data: function(data) {
                data.type = type;
            },
        },
        columns: [
            { data: 'template_type_id', name: 'template_type_id', class: "id" },
            { data: 'template_type_name', name: 'template_type_name', class: "name" },
            { data: 'action', name: 'action', orderable: false, searcheble: false, class: "text-center" },
        ],

        buttons: [{
            text: '<i class="fas fa-plus"></i> Add Coupon Type',
            className: 'btn btn-sm btn-primary add-coupon-type',
        }, ],
    });


    $(document).on('click', '.add-coupon-type', function(e) {
        clear()
        $("#coupon-type-modal").modal("show");
        $("#save-coupon-type").find('.modal-title').text("Add Type");
        $("#save-coupon-type").find('input[name="action"]').val("Create");
    });

    $(document).on('click', '.edit-coupon-type', function(e) {
        e.preventDefault();
        clear()
        var id = $(this).attr("data");
        var name = $(this).parent().parent().find(".name").text();

        $("input[name='name']").val(name);

        $("#coupon-type-modal").modal("show");
        $("#save-coupon-type").find('.modal-title').text("Edit Type");
        $("#save-coupon-type").find('input[name="action"]').val("Update");
        $("#save-coupon-type").find('input[name="typeId"]').val(id);
    });

    $(document).on("click", ".delete-coupon-type", function(e) {
        e.preventDefault();
        var id = $(this).attr("data");
        var url = "{{ route('deleteSetupTypeTemplate') }}";

        if (deleteById(id, url)) {
            table.ajax.reload(null, false);
        }
    });

    $("#save-coupon-type").on('submit', function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var form_data = new FormData(form);
        var url = "{{ route('saveSetupTypeTemplate') }}";
        form_data.append('type', type);
        save(form_data, url);
        $("#coupon-type-modal").modal("hide");
        table.ajax.reload(null, false);
    });

    $("#btn-coupon").on('click', function(e) {
        e.preventDefault();
        type = 1;
        $(".table-title").text("Auto coupon list");
        table.draw();
    });

    $("#btn-promotion").on('click', function(e) {
        e.preventDefault();
        type = 2;
        $(".table-title").text("Auto promotion list");
        table.draw();
    });


    $("#btn-coupon").trigger("click");
});

</script>
@endpush
