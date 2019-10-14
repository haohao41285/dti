@extends('layouts.app')
@section('content-title')
News
@endsection
@push('styles')
  <style>
    .panel-heading{
         background: #f8f9fc;
      }
  </style>
@endpush
@section('content')
<div class="row" >
                <div class="col-6 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">News Type List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" >
                            <table class="table table-bordered table-hover dataTable" id="news-type-datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th >Title</th>
                                        <th>Created at</th>
                                        <th width="100">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary news-list-title">News List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" >
                            <table class="table table-bordered table-hover dataTable" id="news-datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th >Title</th>
                                        <th>Image</th>
                                        <th>Created at</th>
                                        <th width="100">Action</th>
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

{{-- news type modal  --}}
<div class="modal fade" id="news-type-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form mothod="post" id="news-type-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add News Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row col-12">
            <label class="col-2 ">title</label>
            <input class="form-control-sm form-control col-10" type="text" name="title">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-sm btn btn-primary">Save changes</button>
          <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="hidden" name="action" value="Create">
          <input type="hidden" name="newsTypeId">
        </div>
      </div>
    </form>
  </div>
</div>
{{-- news modal --}}
<div class="modal fade" id="news-modal" tabindex="-1" role="dialog">
  <div style="max-width: 90%" class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <form method="post" id="news-form" enctype='multipart/form-data'>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add News</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" style="position: relative;overflow: auto;max-height:70vh;width: 100%;">
            <div class="form-group row col-12">
              <label class="col-2 ">Title</label>
              <input class="form-control-sm form-control col-10" type="text" name="title">
            </div>
            <div class="form-group row col-12">
              <label class="col-2 ">Image</label>
              <div class="previewImage">
                  <img id="previewImageNews" src="{{ asset("images/no-image.png")}}"  />
                  <input type="file" accept="image/*" name="image" class="custom-file-input"  previewImageId="previewImageNews" value="" style="display: none">
              </div>
            </div>
            <div class="form-group row col-12">
              <label class="col-2 ">Short Content</label>
              <input class="form-control-sm form-control col-10" type="text" name="short_content">
            </div>
            <div class="form-group row col-12">
              <label class="col-2 ">Content</label>
              <div class="col-10 row">                     
                    <textarea name="content" class="form-control summernote col-12"></textarea>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-sm btn btn-primary">Save changes</button>
          <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="hidden" name="action" value="Create">
          <input type="hidden" name="newsTypeId">
          <input type="hidden" name="newsId">
        </div>
    </form>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    function clear(){
      $("#news-form")[0].reset();
      $("#news-type-form")[0].reset();
      $(".note-editable").html('');

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
        summernote();
        perviewImage();
        var newsTypeId = null;

    		var newsTypeTable = $('#news-type-datatable').DataTable({
             // dom: "lBfrtip",
    				 processing: true,
    				 serverSide: true,
    				 ajax:{ url:"{{ route('getNewsTypeDatatable') }}",},
    				 columns: [
                { data: 'news_type_id', name: 'news_type_id' ,class:"id"},
    						{ data: 'title', name: 'title' ,class:"title"},
    						{ data: 'created_at', name: 'created_at' },
    						{ data: 'action' , name:'action' ,orderable: false, searcheble: false,class:"text-center"},
                ],
          
    				 buttons: [
    							{
    									text: '<i class="fas fa-plus"></i> Add News Type',
    									className: 'btn btn-sm btn-primary add-news-type',
    							},
    					],
    		});

            var newsTable = $('#news-datatable').DataTable({
             // dom: "lBfrtip",
             processing: true,
             serverSide: true,
             ajax:{ 
              url:"{{ route('getNewsDatatable') }}",
              data:function(data){
                data.newsTypeId = newsTypeId;
              },
            },
             columns: [
                { data: 'news_id', name: 'news_id' ,class:"id"},
                { data: 'title', name: 'title' ,class:"title"},
                { data: 'image', name: 'image' ,class:"image"},
                { data: 'created_at', name: 'created_at' },
                { data: 'action' , name:'action' ,orderable: false, searcheble: false,class:"text-center"},
                ],
          
             buttons: [
                  {
                      text: '<i class="fas fa-plus"></i> Add News',
                      className: 'btn btn-sm btn-primary add-news',
                  },
              ],
        });

        $(document).on('click','.add-news-type',function(e){
          clear()
          $("#news-type-modal").modal("show");
          $("#news-type-form").find('.modal-title').text("Add News Type");
          $("#news-type-form").find('input[name="action"]').val("Create");
        });
       
        $(document).on('click','.edit-news-type',function(e){
          e.preventDefault();
          clear()
          var id = $(this).attr("data-id");
          var title = $(this).parent().parent().find(".title").text();

          $("input[name='title']").val(title);

          $("#news-type-modal").modal("show");
          $("#news-type-form").find('.modal-title').text("Edit News Type");
          $("#news-type-form").find('input[name="action"]').val("Update");
          $("#news-type-form").find('input[name="newsTypeId"]').val(id);
        });

        $(document).on('click','.edit-news',function(e){
          e.preventDefault();
          clear()
          var id = $(this).attr("data-id");

          $.ajax({
            url:"{{ route('getNewsbyId') }}",
            method:"get",
            data:{id},
            dataType:"json",
            success:function(data){
                if(data.status == true){
                    console.log("{{env('URL_FILE_VIEW')}}" + data.data.image);
                    $("input[name='title']").val(data.data.title);
                    $("input[name='short_content']").val(data.data.short_content);
                    $("#previewImageNews").attr("src","{{env('URL_FILE_VIEW')}}" + data.data.image);
                    $(".note-editable").html(data.data.content);

                    $("#news-modal").modal("show");
                    $("#news-form").find('.modal-title').text("Edit News Type");
                    $("#news-form").find('input[name="action"]').val("Update");
                    $("#news-form").find('input[name="newsId"]').val(id);
                }
            },
            error:function(){
                toastr.error("Failed to get data");
            }
          });

          
        });

        $(document).on("click",".delete-news-type",function(e){
          e.preventDefault();
          var id = $(this).attr("data-id");
          var url = "{{ route('deleteNewsType') }}";
          if(deleteById(id,url)){
            newsTypeTable.ajax.reload(null,false);
          }
        });

        $(document).on("click",".delete-news",function(e){
          e.preventDefault();

          var id = $(this).attr("data-id");
          var url = "{{ route('deleteNews') }}";
          if(deleteById(id,url)){
            newsTable.ajax.reload(null,false);
          }
        });

        $(document).on('click','.add-news',function(e){
          var checkSelected = $('#news-type-datatable tbody tr.selected');
          if(checkSelected.length == 0) {
            toastr.warning("Please select the news type");
            return false;
          }
          clear()
          $("#news-modal").modal("show");
          $("#news-form").find('.modal-title').text("Add News");
          $("#news-form").find('input[name="action"]').val("Create");
        });

        $("#news-type-form").on('submit',function(e){
          e.preventDefault();
          var form = $(this)[0];
          var form_data = new FormData(form);
          var url = "{{ route('saveNewsType') }}";
          save(form_data,url);
          $("#news-type-modal").modal("hide");
          newsTypeTable.ajax.reload(null,false);
        });

        $("#news-form").on('submit',function(e){
          e.preventDefault();
          var form = $(this)[0];
          var form_data = new FormData(form);
          var url = "{{ route('saveNews') }}";
          save(form_data,url);
          newsTable.ajax.reload(null,false);
          // newsTable.ajax.reload(null,false);
          $("#news-modal").modal("hide");
        });
        //load News Type banner list by News Type id
        $("#news-type-datatable tbody").on('click',"tr",function(){
            $('#news-type-datatable tbody tr.selected').removeClass('selected');
            $(this).addClass('selected');
            newsTypeId = $(this).find("td a.edit-news-type").attr('data-id');
            title = $(this).find("td.title").text();
            $("#news-form").find("input[name='newsTypeId']").val(newsTypeId);
            $(".news-list-title").html("New List Of <b>"+title+"</b>");
            newsTable.draw();
        });

        
     
    	});
</script>

@endpush