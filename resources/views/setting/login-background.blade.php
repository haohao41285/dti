@extends('layouts.app')
@section('content-title')
Setup Login Background
@endsection
@push('scripts')

@endpush
@section('content')
<div class="row" >
                <div class="col-12 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Backgrounds List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" >
                            <table class="table table-bordered table-hover dataTable" id="datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        {{-- <th>Name</th> --}}
                                        <th>Image</th>
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

{{--modal --}}
<div class="modal fade" id="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="post" enctype='multipart/form-data'>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Background</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            {{-- <div class="form-group row col-12">
              <label class="col-2 ">Link</label>
              <input class="form-control-sm form-control col-10" type="text" name="link">
            </div> --}}
            <div class="form-group row col-12">
              <label class="col-2 ">Image</label>
              <div class="previewImage">
                  <img id="previewImageAppbanner" src="{{ asset("images/no-image.png")}}"  />
                  <input type="file" accept="image/*" name="image" class="custom-file-input"  previewImageId="previewImageAppbanner" value="" style="display: none">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-sm btn btn-primary">Save changes</button>
          <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="hidden" name="action" value="Create">
          <input type="hidden" name="id">
        </div>
    </form>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    function clear(){
      $("form")[0].reset();
      $(".previewImage img").attr('src','{{asset('images/no-image.png')}}');
    }
    function deleteById(id,url){
      if(confirm("Are you sure do you want to delete this data!")){
        var result = null;
        $.ajax({
          async:false,
          url:url,
          method:"post",
          data:{
            _token:"{{csrf_token()}}",
            id:id,
          },
          dataType:"json",
          success:function(data){
            if(data.status == 1){
              toastr.success("Deleted successfully!");
              result = true;
              return;
            }
          },
          error:function(){
            toastr.error("Failed to delete!");
            result = false;
            return;
          }
        });
      }
      return result;
    }
    function save(form_data,url){
      $.ajax({
            url:url,
            method:"post",
            dataType:"json",
            data: form_data,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
              if(data.status == 1){
                toastr.success("Saved successfully!");
              } else {
                  toastr.error(data.msg);
              }
            },
            error:function(){
              toastr.error("Failed to save!");
            }
          });
    }
    
     $(document).ready(function() {
        perviewImage();
        // var appId = null;

          var table = $('#datatable').DataTable({
           // dom: "lBfrtip",
           processing: true,
           serverSide: true,
           ajax:{ 
            url:"{{ route('datatableLoginBackground') }}",
            data:function(data){
             
            },
          },
           columns: [
              { data: 'id', name: 'id' ,class:"id"},
              // { data: 'app_banner_link', name: 'app_banner_link' ,class:"link"},
              { data: 'image', name: 'image' ,class:"image"},
              { data: 'action' , name:'action' ,orderable: false, searcheble: false,class:"text-center"},
              ],
        
           buttons: [
                {
                    text: '<i class="fas fa-plus"></i> Add',
                    className: 'btn btn-sm btn-primary add',
                },
            ],
        });

        $(document).on('click','.add',function(e){
          clear()
          $("#modal").modal("show");
          $("form").find('.modal-title').text("Add");
          $("form").find('input[name="action"]').val("Create");
        });
       
        $(document).on('click','.edit',function(e){
          e.preventDefault();
          clear()
          var id = $(this).attr("data-id");
          var image = $(this).parent().parent().find(".image img").attr('src');

          $("#modal").modal("show");
          $("form").find('.modal-title').text("Edit");
          $("form").find('input[name="action"]').val("Update");
          $("form").find('input[name="id"]').val(id);
          $(".previewImage img").attr('src',image);
        });


        $(document).on("click",".delete",function(e){
          e.preventDefault();
          var id = $(this).attr("data-id");
          var url = "{{ route('deleteLoginBackground') }}";
          if(deleteById(id,url)){
            table.ajax.reload(null,false);
          }
        });

        $("form").on('submit',function(e){
          e.preventDefault();
          var form = $(this)[0];
          var form_data = new FormData(form);
          var url = "{{ route('saveLoginBackground') }}";
          save(form_data,url);
          $("#modal").modal("hide");
          table.ajax.reload(null,false);
          // table.ajax.reload(null,false);
        });


        
     
    	});
</script>

@endpush