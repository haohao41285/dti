@extends('layouts.app')
@section('content-title')
App Banners 
@endsection
@push('scripts')

@endpush
@section('content')
<div class="row" >
                <div class="col-6 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">App List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" >
                            <table class="table table-bordered table-hover dataTable" id="app-datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Desc</th>
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

                <div class="col-6 ">
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">App Banners List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" >
                            <table class="table table-bordered table-hover dataTable" id="app-banner-datatable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Link</th>
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

{{-- app modal  --}}
<div class="modal fade" id="app-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form mothod="post" id="save-app">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add App</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group row col-12">
            <label class="col-2 ">Name</label>
            <input class="form-control-sm form-control col-10" type="text" name="name">
          </div>
          <div class="form-group row col-12">
            <label class="col-2 ">Desc</label>
            <input class="form-control-sm form-control col-10" type="text" name="desc">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-sm btn btn-primary">Save changes</button>
          <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
          <input type="hidden" name="action" value="Create">
          <input type="hidden" name="appId">
        </div>
      </div>
    </form>
  </div>
</div>
{{-- app banner modal --}}
<div class="modal fade" id="app-banner-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="post" id="save-app-banner" enctype='multipart/form-data'>
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add App Banner</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group row col-12">
              <label class="col-2 ">Link</label>
              <input class="form-control-sm form-control col-10" type="text" name="link">
            </div>
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
          <input type="hidden" name="appId">
          <input type="hidden" name="appBannerId">
        </div>
    </form>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    function clear(){
      $("#save-app-banner")[0].reset();
      $("input[name='name']").val('');
      $("input[name='desc']").val('');
      $("input[name='link']").val('');
      $("input[name='file']").val('');
      // $("input[name='appId']").val('');
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
        var appId = null;

    		var appTable = $('#app-datatable').DataTable({
             // dom: "lBfrtip",
    				 processing: true,
    				 serverSide: true,
    				 ajax:{ url:"{{ route('appDataTable') }}",},
    				 columns: [
                { data: 'app_id', name: 'app_id' ,class:"id"},
    						{ data: 'app_name', name: 'app_name' ,class:"name"},
    						{ data: 'app_desc', name: 'app_desc',class:"desc" },
    						{ data: 'action' , name:'action' ,orderable: false, searcheble: false,class:"text-center"},
                ],
          
    				 buttons: [
    							{
    									text: '<i class="fas fa-plus"></i> Add App',
    									className: 'btn btn-sm btn-primary add-app',
    							},
    					],
    		});

            var appBannerTable = $('#app-banner-datatable').DataTable({
             // dom: "lBfrtip",
             processing: true,
             serverSide: true,
             ajax:{ 
              url:"{{ route('appBannerDataTable') }}",
              data:function(data){
                data.appId = appId;
              },
            },
             columns: [
                { data: 'app_banner_id', name: 'app_banner_id' ,class:"id"},
                { data: 'app_banner_link', name: 'app_banner_link' ,class:"link"},
                { data: 'app_banner_image', name: 'app_banner_image' ,class:"image"},
                { data: 'action' , name:'action' ,orderable: false, searcheble: false,class:"text-center"},
                ],
          
             buttons: [
                  {
                      text: '<i class="fas fa-plus"></i> Add App Banner',
                      className: 'btn btn-sm btn-primary add-app-banner',
                  },
              ],
        });

        $(document).on('click','.add-app',function(e){
          clear()
          $("#app-modal").modal("show");
          $("#save-app").find('.modal-title').text("Add App");
          $("#save-app").find('input[name="action"]').val("Create");
        });
       
        $(document).on('click','.edit-app',function(e){
          e.preventDefault();
          clear()
          var id = $(this).attr("data-id");
          var name = $(this).parent().parent().find(".name").text();
          var desc = $(this).parent().parent().find(".desc").text();

          $("input[name='name']").val(name);
          $("input[name='desc']").val(desc);

          $("#app-modal").modal("show");
          $("#save-app").find('.modal-title').text("Edit App");
          $("#save-app").find('input[name="action"]').val("Update");
          $("#save-app").find('input[name="appId"]').val(id);
        });

        $(document).on('click','.edit-app-banner',function(e){
          e.preventDefault();
          clear()
          var id = $(this).attr("data-id");
          var link = $(this).parent().parent().find(".link").text();
          var img = $(this).parent().parent().find(".image img").attr("src");

          $("input[name='link']").val(link);
          $("#previewImageAppbanner").attr("src",img);

          $("#app-banner-modal").modal("show");
          $("#save-app-banner").find('.modal-title').text("Edit App");
          $("#save-app-banner").find('input[name="action"]').val("Update");
          $("#save-app-banner").find('input[name="appBannerId"]').val(id);
        });

        $(document).on("click",".delete-app",function(e){
          e.preventDefault();
          var id = $(this).attr("data-id");
          var url = "{{ route('deleteApp') }}";
          if(deleteById(id,url)){
            appTable.ajax.reload(null,false);
          }
        });

        $(document).on("click",".delete-app-banner",function(e){
          e.preventDefault();

          var id = $(this).attr("data-id");
          var url = "{{ route('deleteAppBanner') }}";
          if(deleteById(id,url)){
            appBannerTable.ajax.reload(null,false);
          }
        });

        $(document).on('click','.add-app-banner',function(e){
          var checkSelected = $('#app-datatable tbody tr.selected');
          if(checkSelected.length == 0) {
            toastr.warning("Please select the app list");
            return false;
          }
          clear()
          $("#app-banner-modal").modal("show");
          $("#save-app-banner").find('.modal-title').text("Add App Banner");
          $("#save-app-banner").find('input[name="action"]').val("Create");
        });

        $("#save-app").on('submit',function(e){
          e.preventDefault();
          var form = $(this)[0];
          var form_data = new FormData(form);
          var url = "{{ route('saveApp') }}";
          save(form_data,url);
          $("#app-modal").modal("hide");
          appTable.ajax.reload(null,false);
        });

        $("#save-app-banner").on('submit',function(e){
          e.preventDefault();
          var form = $(this)[0];
          var form_data = new FormData(form);
          var url = "{{ route('saveAppBanner') }}";
          save(form_data,url);
          appBannerTable.draw();
          $("#app-banner-modal").modal("hide");
        });
        //load app banner list by app id
        $("#app-datatable tbody").on('click',"tr",function(){
            $('#app-datatable tbody tr.selected').removeClass('selected');
            $(this).addClass('selected');
            appId = $(this).find("td a.edit-app").attr('data-id');
            $("#save-app-banner").find("input[name='appId']").val(appId);
            appBannerTable.draw();
        });

        
     
    	});
</script>

@endpush