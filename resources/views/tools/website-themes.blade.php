@extends('layouts.app')
@section('content-title')
Theme Management
@endsection
@push('scripts')

@endpush
@section('content')
<div class="card shadow mb-4 ">
    <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary " >Website themes </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <th>#</th>
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
                                <input type="checkbox" name="status" value="1" class="js-switch">
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
                    <button class="btn btn-sm btn-warning resetAddProperty" >Reset Add</button>
                    <form action="" method="POST" role="form" id="setup-properties" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="theme_properties_name" class="form-control-sm form-control">
                        </div>
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
                        <input type="hidden" name="theme_properties_id">
                    </form>
                </div>
                <div class="col-5 ">
                    {{-- <br><br> --}}
                    <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Properties List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" style="position: relative; overflow: auto; height: 70vh; width: 100%;">
                            <table class="table table-bordered table-hover dataTable" id="themeProperties" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
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
                    </div>
                </div>
                <div class="col-5">
                    {{-- <br><br><br> --}}
                    <div class="form-group" id="buttonSetupProperties">
                        <button class="btn-sm btn-primary btn showAdd" id="addText">Add Text</button>
                        <button class="btn-sm btn-danger btn showAdd" id="addImage">Add Image</button>
                        <button class="btn-sm btn-success btn" id="hide">Hide</button>
                        <button class="btn btn-sm btn-warning resetAddProperty" >Reset Add</button>
                    </div>
                    <div id="toggleAdd">
                    <form id="formSaveValueProperties" method="post" enctype="multipart/form-data">
                        @csrf()
                        <div class="form-group">
                            <label >Name</label>
                            <input type="text" class="form-control-sm form-control" name="name">
                        </div>
                        <div class="form-group">
                            <div class="addText data">
                                <label>Value</label>                            
                                <input type="text" class="form-control-sm form-control" name="value">
                            </div>
                            <div class="addImage data">
                                <label >Image</label>
                                <div class="previewImage">
                                    <img id="previewImageValue" src="{{ asset("images/no-image.png")}}" >
                                    <input type="file" class="custom-file-input" name="image" previewImageId="previewImageValue" style="display: none">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn-sm btn btn-primary" value="Save">
                            <input type="hidden" name="action" value="Create">
                            <input type="hidden" name="propertyId">
                            <input type="hidden" name="idValue">
                        </div>
                    </form>
                    </div>
                    <div class="table-responsive">
                        <div class="card shadow mb-4 ">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary">Value List </h6>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive dataTables_scrollBody dataTables_scroll" style="position: relative; overflow: auto; max-height: 65vh; width: 100%;">
                        <table class="table table-bordered" id="listValueProperties" width="100%" cellspacing="0">
                             <thead>
                                <tr>
                                    <th>Variable name</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               {{--  <tr>
                                    <td>1</td>
                                    <td>abc</td>
                                    <td><a class="btn btn-sm btn-secondary editValue"  href="#"><i class="fas fa-edit"></i></a>
                                        <a class="btn btn-sm btn-secondary deleteValue"  href="#"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
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
        $("input[name='value']").val('');
    	$("input[name='idValue']").val('');

    	// $("input[name='theme_id']").val('');
    	$("textarea[name='description']").text('');
    	$(".previewImage img").attr('src','{{asset('images/no-image.png')}}');
        $("#setup-properties").find("input[name='theme_properties_name']").val("");
    }
    
     $(document).ready(function() {
    		var table = $('#dataTable').DataTable({
    				 processing: true,
    				 serverSide: true,
    				 ajax:{ url:"{{ route('getDatatableWebsiteThemes') }}",},
    				 columns: [
                            { data: 'theme_id', name: 'theme_id' },
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
    										var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch-datatable'));
                                            elems.forEach(function (html) {
                                                var switchery = new Switchery(html, {
                                                    color: '#0874e8',
                                                    className : 'switchery switchery-small changeStatus'                
                                                });
                                            });
    								},
    		});
    
    		$(document).on('click','.changeStatus',function(e){
    			e.preventDefault();
    			var id = $(this).siblings('input').attr('data');

    			$.ajax({
    				url:"{{ route('changeStatusThemes') }}",
    				method:"get",
    				data:{
    					id,
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
    						$(".previewImage img").attr('src',"{{env('URL_FILE_VIEW')}}"+data.data.theme_image);
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
			    		toastr.success(data.msg);
                        table.ajax.reload(null, false);
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

    
   
    });
</script>
{{-- setup-properties --}}
<script>
    function listThemePropertiesByThemeId(theme_id){
        $.ajax({
            url:"{{ route('listThemePropertiesByThemeId') }}",
            method:"get",
            dataType:"json",
            data:{theme_id},
            success:function(data){
                if(data.status == 1){
                    var html = '';
                    for(var i = 0; i < data.data.length; i++){
                            html    +='<tr properties-id='+data.data[i].theme_properties_id+'>'
                                    +'<td>'+data.data[i].theme_properties_id+'</td>'
                                    +'<td>'+data.data[i].theme_properties_name +'</td>'
                                    +'<td><img style="height: 5rem;" src="'+"{{env('URL_FILE_VIEW')}}" + data.data[i].theme_properties_image+'" /></td>'
                                    +'<td><a style="margin-right: 5px;" class="btn btn-sm btn-secondary editProperties" properties-id='+data.data[i].theme_properties_id+' href="#"><i class="fas fa-edit"></i></a>'
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

    function listValueProperties(propertyId){
        $.ajax({
            url:"{{ route('listValueProperties') }}",
            method:"get",
            dataType:"json",
            data:{propertyId},
            success:function(data){
                if(data.status == 1){
                    if(!data.data){
                        // toastr.error('Failed to get list data!');
                        $("#listValueProperties tbody").html("");
                        return false;
                    }
                    // console.log(count);
                    var html = '';
                    var name = '';
                    var value = '';
                    for(var i = 0; i < data.data.length; i++){
                        name = data.data[i].name;
                        
                        if(checkURL("{{env('URL_FILE_VIEW')}}"+data.data[i].value) == true){
                            value = "<img style='height: 5rem;' src= '{{env('URL_FILE_VIEW')}} "+data.data[i].value+"'/>";
                        } else {
                            value = data.data[i].value;
                        }
                          
                        html    +='<tr>'
                                +'<td class="name">'+name+'</td>'
                                +'<td class="value">'+value+'</td>'
                                +'<td data-id="'+data.data[i].id+'"><a style="margin-right: 5px;" class="btn btn-sm btn-secondary editValueProperties"  href="#"><i class="fas fa-edit"></i></a>'
                                +'<a class="btn btn-sm btn-secondary deleteValueProperties"  href="#"><i class="fas fa-trash"></i></a>'
                                +'</td>'
                                +'</tr>'
                    }

                    $("#listValueProperties tbody").html(html);
                }
            },
            error:function(){
                toastr.error("Failed to load data!");
            }
        })
    }
    function checkURL(url) {
        return(url.match(/\.(jpeg|jpg|gif|png)$/) != null);
    }
    $(document).ready(function(){
        var theme_id = null;
        var propertyId = null;
        $(document).on('click',".setup-properties",function(e){
                e.preventDefault();
                clear();
                theme_id = $(this).attr('data');
                $("#setup-properties").find("input[name='theme_id']").val(theme_id);
                // $("#setup-properties").find("input[name='theme_properties_name']").val(theme_properties_name);
                listThemePropertiesByThemeId(theme_id);
                $("#setupPropertiesModal").modal("show");
                $(".resetAddProperty").trigger('click');
                $("#hide").trigger('click');
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
                        toastr.success(data.msg);
                        listThemePropertiesByThemeId(theme_id);
                        clear();
                        $(".resetAddProperty").trigger('click');
                    }
                },
                error:function(){
                    toastr.error("Failed to save!");
                }
              });
        });
        $(".resetAddProperty").on('click',function(){
            clear();
            $("#setup-properties").find("button").text("Create Property");
            $("#setup-properties").find("input[name='action']").val("Create");
            $("#setup-properties").find("input[name='theme_properties_id']").val("");
        });

        $(document).on('click',".editProperties",function(e){
            e.preventDefault();
            var properties_id = $(this).attr('properties-id');
             $.ajax({
                    url:"{{ route('editWebsiteThemesProperty') }}",
                    data:{
                        properties_id,
                    },
                    method:"get",
                    success:function(data){
                        clear();
                        $("#setup-properties").find(".previewImage img").attr('src',"{{env('URL_FILE_VIEW')}}"+data.data.theme_properties_image);
                        $("#setup-properties").find("button").text("Update Property");
                        $("#setup-properties").find("input[name='action']").val("Update");
                        $("#setup-properties").find("input[name='theme_properties_id']").val(properties_id);
                        $("#setup-properties").find("input[name='theme_properties_name']").val(data.data.theme_properties_name);
                    },
                    error:function(){
                        toastr.error("Failed to get data!");
                    }
                });
        });
        $(document).on('click',".deleteProperties",function(e){
            e.preventDefault();
            var properties_id = $(this).attr('properties-id');
            if(confirm("Are you sure do you want to delete this data?")){
                $.ajax({
                    url:"{{ route('deleteWebsiteThemesProperty') }}",
                    data:{
                        _token:"{{csrf_token()}}",
                        properties_id,
                    },
                    method:"post",
                    success:function(){
                        toastr.success("Deleted successfully!");
                        clear();
                        listThemePropertiesByThemeId(theme_id);
                    },
                    error:function(){
                        toastr.error("Failed to delete data!");
                    }
                });
            }
        });

        $("#themeProperties tbody").on('click',"tr",function(){
            $('#themeProperties tbody tr.selected').removeClass('selected');
            $(this).addClass('selected');
            propertyId = $(this).attr('properties-id');
            // console.log(propertyId);
            $("input[name='propertyId']").val(propertyId);
            listValueProperties(propertyId);
        });

        $("#hide").on('click',function(){
            $("#toggleAdd").hide(200);
        });

        $(".showAdd").on('click',function(){
            var checkSelected = $('#themeProperties tbody').find("tr.selected");
            if(checkSelected.length == 0){
                toastr.error("Please choose properties list!");
                return false;
            }

            $("#toggleAdd").show(200);
            var id = $(this).attr('id');

            $("#toggleAdd .data").hide();
            $("#toggleAdd .data input").attr('disabled',true);
            $("."+id+"").show(200);
            $("."+id+"").find("input").removeAttr("disabled");
        });

        $("#formSaveValueProperties").on('submit',function(e){
            e.preventDefault();
            var form = $(this)[0];
            var form_data = new FormData(form);
            $.ajax({
                url:"{{ route('saveValueProperties') }}",
                method:"post",
                data: form_data,
                cache:false,
                contentType: false,
                processData: false,
                success:function(data){
                    if(data.status == 1){
                        toastr.success(data.msg);
                        listValueProperties(propertyId);
                        clear();
                        $("#hide").trigger('click');
                    } else {
                        toastr.error(data.msg);
                    }
                },
                error:function(){
                    toastr.error("Failed to save!");
                }
              });
        });

        $(document).on('click',".editValueProperties",function(e){
            e.preventDefault();
            var getName = $(this).parent().parent().find(".name").text();
            var getValue = $(this).parent().parent().find(".value").text();
            var getImage = $(this).parent().parent().find(".value").children().attr('src');
            var getIdValue = $(this).parent().attr('data-id');

            if(!getValue){
                $("#addImage").trigger('click');
                $("#formSaveValueProperties").find(".previewImage img").attr("src",getImage);
            } else {
                $("#addText").trigger('click');
                $("#formSaveValueProperties").find("input[name='value']").val(getValue);
            }
            $("#formSaveValueProperties").find("input[name='name']").val(getName);
            $("#formSaveValueProperties").find("input[name='idValue']").val(getIdValue);
        });
        $(document).on('click',".deleteValueProperties",function(e){
            e.preventDefault();
            if(confirm("Are you sure do you want delete this data!")){
                idValue = $(this).parent().attr('data-id');
                $.ajax({
                    url:"{{ route('deleteValueProperties') }}",
                    data:{
                        _token:"{{csrf_token()}}",
                        idValue,
                        propertyId,
                    },
                    method:"post",
                    dataType:"json",
                    success:function(data){
                        if(data.status == 1){
                            listValueProperties(propertyId);
                            clear();
                            toastr.success("Deleted successfully!");
                        }
                    },
                    error:function(){
                        toastr.error("Failed to delete!");
                    }

                });
            }
        });


    });
</script>
@endpush