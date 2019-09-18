@extends('layouts.app')
@section('content-title')
		Theme Management
@endsection
@section('content')
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
			 {{--  <tbody>
						<tr>
								<td>Theme 10</td>
								<td>demo10</td>
								<td class="text-right">$100</td>               
								<td class="text-center"><img src="{{asset("images/no-image.png")}}" width="100px" height="100px"/></td>                                
								<td class="text-center"><input type="checkbox" class="js-switch" checked="checked" /></td> 
								<td>20/11/2019 10:11 AM by admin</td>
								<td class="text-center nowrap">
										<a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-link"></i> DEMO</a>
										<a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-edit"></i></a>
								</td>
						</tr>
				</tbody> --}}
		</table>
</div>

<!-- themeModal -->
<div class="modal fade" id="themeModal" tabindex="-1" role="dialog" aria-hidden="true">
		<form class="sb-form" action="edit.php">
	<div class="modal-dialog" role="document" style="max-width: 95%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add Theme</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
						<div class="form-group">
								<div class="col-md-7 form-inline">
										<label class="col-sm-4 required">Theme Name</label>
										<div class="col-sm-8">            
												 <input type='text' value="" name="name" class="form-control form-control-sm" /> 
										</div>
								</div>    
						</div>
						<div class="form-group">
								<div class="col-md-7 form-inline">
										 <label class="col-sm-4 required">Theme Code</label>
										<div class="col-sm-8">
												<input type='text' value="" name="code" class="form-control form-control-sm" /> 
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
												<input type="text" class="form-control" name="price">
										</div>
								</div>    
						</div>
						<div class="form-group">
								<div class="col-md-7 form-inline">
										 <label class="col-sm-4 required">URL</label>
										<div class="col-sm-8">
												<input type='text' name="url"  class="form-control form-control-sm" /> 
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
												<input type="checkbox" class="js-switch" checked="checked" name="status"/>
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
												 {{-- <div class="custom-file">
														
														<label class="custom-file-label" for="customFile" style="display: none">Choose image file</label>
												</div>   --}}  
												 <div class="previewImage">
														 <img id="previewImage" src="{{ asset("images/no-image.png")}}" width="50%"/>
														 <input type="file" name="image" class="custom-file-input" id="customFile" previewImageId="previewImage" style="display: none">
												 </div>
										</div>
								</div>    
										
						</div>
			 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-sm">Save changes</button>
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
					 <form action="" method="POST" role="form">
						 <div class="form-group">
										<label >Image</label>
									 <div class="previewImageSetupProperties">
											 <img id="previewImageSetupProperties" src="{{ asset("images/no-image.png")}}" width="100%">
											 <input type="file" class="custom-file-input" name="image" previewImageId="previewImageSetupProperties" style="display: none">
									 </div>
						</div>
					 
						 <button type="submit" class="btn btn-primary btn-sm">Add New Property</button>
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
								<tr>
									<td>1</td>
									<td>abc</td>
									<td><a class="btn btn-sm btn-secondary editProperties"  href="#"><i class="fas fa-edit"></i></a>
										<a class="btn btn-sm btn-secondary deleteProperties"  href="#"><i class="fas fa-trash"></i></a></td>
								</tr>
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
										<a class="btn btn-sm btn-secondary deleteValue"  href="#"><i class="fas fa-trash"></i></a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			{{-- <div class="modal-footer">
				<button type="button" class="btn btn-primary btn-sm">Save changes</button>
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>        
			</div> --}}
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
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
										var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch-datatable'));
										elems.forEach(function (html) {
												var switchery = new Switchery(html, {
														color: '#0874e8',
														className : 'switchery switchery-small'                
												});
										});
								},
		});
});
</script>
<script>
	function clear(){
		$("input[name='name']").val('');
		$("input[name='code']").val('');
		$("input[name='price']").val('');
		$("input[name='url']").val('');
		$("input[name='license']").val('');
		$("input[name='status']").val('');
		$("input[name='description']").text('');
		$("input[name='image']").attr('src',false);
	}
	$(document).ready(function(){

		$(".add").on('click',function(e){
			e.preventDefault();
			clear();
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

					$("#themeModal").modal("show");
				}
			});
			
		});
		$(document).on('click',".setup-properties",function(e){
			e.preventDefault();
			$("#setupPropertiesModal").modal("show");
		});

	});
</script>
{{-- script of themModel --}}
<script type="text/javascript">
 $(document).ready(function() {
	 perviewImage();

	 $("#previewImage").on('click',function(){
			$("input#customFile").trigger('click');
	 });

	 $("#previewImageSetupProperties").on('click',function(){
			$("input[previewImageId='previewImageSetupProperties']").trigger('click');
	 });
});
</script>
@endpush