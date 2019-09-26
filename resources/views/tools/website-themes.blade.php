@extends('layouts.app')
@section('content-title')
Theme Management
@endsection
@section('content')
<div class="card shadow mb-4 ">
    <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary " >Website themes </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <th>Theme Name</th>
                    <th>Theme Code</th>
                    <th>Price($)</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Last Update</th>
                    <th >Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- themeModal -->
<div class="modal fade" id="themeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="sb-form" id="formTheme" enctype="multipart/form-data">
        @csrf
        <div class="modal-dialog" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModalTheme">Add Theme</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Theme Name</label>
                            <div class="col-sm-8">            
                                <input required="" type='text' value="" name="name" class="form-control form-control-sm" /> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Theme Code</label>
                            <div class="col-sm-8">
                                <input required="" type='text' value="" name="code" class="form-control form-control-sm" /> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Price</label>
                            <div class="col-sm-8 input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input required="" type="text" class="form-control" name="price">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">URL</label>
                            <div class="col-sm-8">
                                <input required="" type='text' name="url"  class="form-control form-control-sm" /> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4">License</label>
                            <div class="col-sm-8">
                                <input type='text' name="license"  class="form-control form-control-sm" /> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Status</label>
                            <div class="col-sm-8">
                                <input type="checkbox" name="status" value="1" class="toggleButton">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4">Description</label>
                            <div class="col-sm-8">
                                <textarea name="description" class="form-control form-control-sm"></textarea> 
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 align-self-baseline">Image</label>
                            <div class="col-sm-8">
                                {{-- 
                                <div class="custom-file">
                                    <label class="custom-file-label" for="customFile" style="display: none">Choose image file</label>
                                </div>
                                --}}  
                                <div class="previewImage">
                                    <img id="previewImageTheme" src="{{ asset("images/no-image.png")}}"  />
                                    <input type="file" name="image" class="custom-file-input"  previewImageId="previewImageTheme" style="display: none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                	<input type="hidden" name="theme_id">
                    <input type="hidden" name="action">
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>        
                </div>
            </div>
        </div>
    </form>
</div>
<!-- setupPropertiesModal -->
<div class="modal fade" id="setupPropertiesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Setup Properties</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-2">
                    <form action="" method="POST" role="form" id="setup-properties">
                        <div class="form-group">
                            <label >Image</label>
                            <div class="previewImage">
                                <img id="previewImageSetupProperties" src="{{ asset("images/no-image.png")}}" >
                                <input type="file" class="custom-file-input" name="image" previewImageId="previewImageSetupProperties" style="display: none">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Add New Property </button>
                        <input type="hidden" name="action" value="Create">
                        <input type="hidden" name="theme_id">
                    </form>
                </div>
                <div class="col-5 ">
                    <br>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="themeProperties" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               {{--  <tr>
                                    <td>1</td>
                                    <td>abc</td>
                                    <td><a class="btn btn-sm btn-secondary editProperties"  href="#"><i class="fas fa-edit"></i></a>
                                        <a class="btn btn-sm btn-secondary deleteProperties"  href="#"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group">
                        <div class="col-12 form-inline">
                            <label class="col-sm-1 ">Name</label>
                            <div class="col-sm-4 input-group">
                                <input type="text" class="form-control-sm form-control">
                            </div>
                            <label class="col-sm-1 ">Value</label>
                            <div class="col-sm-4 input-group">
                                <input type="text" class="form-control-sm form-control">
                            </div>
                            <input type="submit" class="btn btn-sm btn-secondary" value="Add">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Value</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>abc</td>
                                    <td><a class="btn btn-sm btn-secondary editValue"  href="#"><i class="fas fa-edit"></i></a>
                                        <a class="btn btn-sm btn-secondary deleteValue"  href="#"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- 
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm">Save changes</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>        
            </div>
            --}}
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    function clear(){
    	$("input[name='name']").val('');
    	$("input[name='code']").val('');
    	$("input[name='price']").val('');
    	$("input[name='url']").val('');
    	$("input[name='license']").val('');
    	$("input[name='theme_id']").val('');
    	$("textarea[name='description']").text('');
    	$("#previewImage").attr('src','{{asset('images/no-image.png')}}');
    }
    
     $(document).ready(function() {
    		var table = $('#dataTable').DataTable({
    				 processing: true,
    				 serverSide: true,
    				 ajax:{ url:"{{ route('getDatatableWebsiteThemes') }}",},
    				 columns: [
    						{ data: 'theme_name', name: 'theme_name' },
    						{ data: 'theme_name_temp', name: 'theme_name_temp' },
    						{ data: 'theme_price', name: 'theme_price' },
    						{ data: 'theme_image', name: 'theme_image' },
    						{ data: 'theme_status', name: 'theme_istatus' },
    						{ data: 'updated_at' , name:'updated_at'},
    						{ data: 'action' , name:'action' ,orderable: false, searcheble: false,class:"text-center"}
    
    				],       
    				 buttons: [
    							{
    									text: '<i class="fas fa-plus"></i> Add Theme',
    									className: 'btn btn-sm btn-primary add',
    									action: function ( e, dt, node, config ) {
    										 // document.location.href = "{{ route('addTheme') }}";
    									}
    							},
    							// { text : '<i class="fas fa-download"></i> Export',
    							//   extend: 'csvHtml5', 
    							//   className: 'btn btn-sm btn-primary' 
    							// }
    					],
    					fnDrawCallback:function (oSettings) {
    										$('.checkboxToggleDatatable').bootstrapToggle();
    								},
    		});
    
    		$(document).on('change','.changeStatus',function(e){
    			e.preventDefault();
    			var id = $(this).attr('data');
    			var check = $(this).is(':checked'); 
    			if(check == true) {
    				check = 1;
    			} else {
    				check = 0;
    			}
    			$.ajax({
    				url:"{{ route('changeStatusThemes') }}",
    				method:"get",
    				data:{
    					id,check,
    				},
    				success:function(data){
    					if(data){
    						toastr.success("Changed successfully!");
    						table.ajax.reload(null, false);
    					}
    				},
    				error:function(){
    					toastr.error("Failed to change!");
    				}
    			})
    		});
    
    		$(document).on('click','.delete',function(e){
    			e.preventDefault();
    			var id = $(this).attr("data");
    			if(confirm("Are you sure you want to delete this data!")){
    			$.ajax({
    				url:"{{ route('deleteThemes') }}",
    				method:"get",
    				data:{id},
    				success:function(data){
    					if(data){
    						toastr.success("Deleted successfully!");
    						table.ajax.reload(null, false);
    					}
    				},
    				error:function(){
    					toastr.error("Failed to delete!");
    				}
    			});
    			}
    		});
    
    		$(".add").on('click',function(e){
    			e.preventDefault();
    			clear();
    			$("#titleModalTheme").text("Add Theme");
    			$("input[name='action']").val("Create");
    			$("#themeModal").modal("show");
    		});
    
    		$(document).on('click',".edit",function(e){
    			e.preventDefault();
    			clear();
    			var id = $(this).attr('data');
    			$.ajax({
    				url:"{{ route('getWebsiteThemesById') }}",
    				method:"get",
    				data:{
    					id,
    				},
    				success:function(data){
    					if(data.status == 1){
    						$("#titleModalTheme").text("Edit Theme");
    						$("input[name='theme_id']").val(id);
    						$("input[name='action']").val("Update");
    						$("input[name='name']").val(data.data.theme_name);
    						$("input[name='code']").val(data.data.theme_name_temp);
    						$("input[name='price']").val(data.data.theme_price);
    						$("input[name='url']").val(data.data.theme_url);
    						$("input[name='license']").val(data.data.theme_license);

    						if(data.data.status == 1){
    							$("input[name='status']").attr("checked",true);
    						}
    						
    						$("input[name='description']").text(data.data.theme_descript);
    						$("#previewImage").attr('src',data.data.theme_image);
    						$("#themeModal").modal("show");
    					}
    				}
    			});
    		});
    
    
			$(document).on("submit","#formTheme",function(e){
				// var check = $("#formTheme']").find("input[type='checkbox").is(':checked'); 
				// alert(check);
				e.preventDefault();
				var form = $(this)[0];
			  	var form_data = new FormData(form);
			  $.ajax({
			  	url:"{{ route('saveWebsiteThemes') }}",
			  	method:"post",
			  	data: form_data,
			    cache:false,
			    contentType: false,
			    processData: false,
			    success:function(data){
			    	if(data.status == 1){
                        $("#themeModal").modal("hide");
			    		toastr.success("Saved successfully!");
			    	}
			    },
			    error:function(){
			    	toastr.error("Failed to save!");
			    }
			  });
			});
    
    
    		
    
    		
     
    	});
</script>
{{-- script of themModel --}}
<script type="text/javascript">
    $(document).ready(function() {
     perviewImage();
     $('.toggleButton').bootstrapToggle();
    
     $(".previewImage img").on('click',function(){
     		var id = $(this).attr("id");
    		$("input[previewImageId='"+id+"']").trigger('click');
     });
    
   
    });
</script>
{{-- setup-properties --}}
<script>
    function listThemePropertiesByThemeId(theme_id){
        $.ajax({
            url:"{{ route('listThemePropertiesByThemeId') }}",
            method:"get",
            dataType:"json",
            success:function(data){
                if(data.status == 1){
                    var html = '';
                    for(var i = 0; i < data.data.length; i++){
                            html    +='<tr>'
                                    +'<td>'+data.data[i].theme_properties_id+'</td>'
                                    +'<td>abc</td>'
                                    +'<td><a class="btn btn-sm btn-secondary editProperties" properties-id='+data.data[i].theme_properties_id+' href="#"><i class="fas fa-edit"></i></a>'
                                    +'<a class="btn btn-sm btn-secondary deleteProperties" properties-id='+data.data[i].theme_properties_id+' href="#"><i class="fas fa-trash"></i></a>'
                                    +'</td>'
                                    +'</tr>'
                    }

                    $("#themeProperties tbody").html(html);
                }
            }, 
            error:function(){
                toastr.error("Failed to load Properties!");
            }
        });
    }
    $(document).ready(function(){
        $(document).on('click',".setup-properties",function(e){
                e.preventDefault();
                var theme_id = $(this).attr('data');
                $("#setup-properties").find("input[name='theme_id']").val(theme_id);
                listThemePropertiesByThemeId(theme_id)
                $("#setupPropertiesModal").modal("show");
            });



        $("#setup-properties").on('submit',function(e){
            e.preventDefault();
            var form = $(this)[0];
            var form_data = new FormData(form);
              $.ajax({
                url:"{{ route('saveWebsiteThemesProperty') }}",
                method:"post",
                data: form_data,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    if(data.status == 1){
                        toastr.success("Saved successfully!");
                    }
                },
                error:function(){
                    toastr.error("Failed to save!");
                }
              });
        });

    });
</script>
@endpush