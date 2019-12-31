@extends('layouts.app')
@section('content-title')
@endsection
@push('styles')
@endpush
@section('content')
@include('tools.partials.modals.add_category')
<div class="col-12 ">
    <div class="card shadow mb-4 ">
        <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-primary">Webbuilder</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
				  <!-- Nav tabs -->
				  <ul class="nav nav-tabs" role="tablist">
				    <li class="nav-item">
				      <a class="nav-link active" data-toggle="tab" href="#service_categories">Service Categories</a>
				    </li>
				    <li class="nav-item">
				      <a class="nav-link" data-toggle="tab" href="#services">Servivce</a>
				    </li>
				    <li class="nav-item">
				      <a class="nav-link" data-toggle="tab" href="#menus">Menus</a>
				    </li>
				    <li class="nav-item">
				      <a class="nav-link" data-toggle="tab" href="#banners">Banners</a>
				    </li>
				    <li class="nav-item">
				      <a class="nav-link" data-toggle="tab" href="#social_network">Social Network</a>
				    </li>
				    <li class="nav-item">
				      <a class="nav-link" data-toggle="tab" href="#web_seo">Web Seo</a>
				    </li>
				  </ul>

				<!-- Tab panes -->
				<div class="tab-content mx-2">
				    <div id="service_categories" class="tab-pane active"><br>
				    	@include('tools.partials.service_categories')
				    </div>
				    <div id="services" class="tab-pane fade"><br>
				    	@include('tools.partials.services')
				    </div>
				    <div id="menus" class="tab-pane fade"><br>
				    	@include('tools.partials.menus')
				    </div>
				    <div id="banners" class="tab-pane fade"><br>
				    	@include('tools.partials.banners')
				    </div>
				    <div id="web_seo" class="tab-pane fade"><br>
				    	@include('tools.partials.web_seo')
				    </div>
				    <div id="social_network" class="tab-pane fade"><br>
				    	@include('tools.partials.social_network')
				    </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
	$(document).ready(function($) {

		var place_id = '{{ $place_id }}';
		$(".summernote").summernote();

		serviceCategoriesTable=$('#service_categories_datatable').DataTable({
             // dom: "lBfrtip",
             processing: true,
             serverSide: true,
             autoWidth: true,
             buttons: [
                 {
                    text: '<i class="glyphicon glyphicon-plus fa fa-plus"></i> Add New',                    
                    className: "btn-sm categories-add",
                }, {
                    text: '<i class="glyphicon glyphicon-import"></i> Import',
                    className: 'btn-sm',
                    action: function ( e, dt, node, config ) {
                        document.location.href = "service/import";
                    }
                },
                { 
                    extend:'excel',
                    text: '<i class="glyphicon glyphicon-export"></i> Export',
                    className: "btn-sm",
                    action: function ( e, dt, node, config ) {
                        document.location.href = "";
                    },
                }

             ],

             ajax:{ url:"{{ route('places.cateservice') }}",
             data:function(d){
             	d.place_id = place_id;
             }
         },
	        columns: [
	            { data: 'cateservice_id', name: 'cateservice_id' },
	            { data: 'cateservice_name', name: 'cateservice_name' },
	            { data: 'cateservice_description', name: 'cateservice_description' },
	            { data: 'cateservice_index', name: 'cateservice_index' },
	            { data: 'cateservice_image', name: 'cateservice_image' },
	            { data: 'updated_at', name:'updated_at'},
	            { data: 'action' , name: 'action',  orderable: false, searchable: false }
	            ]    
        });
		servicesTable = $('#services_datatable').DataTable({
             // dom: "lBfrtip",
            serverSide: true,
        	responsive:false,
            buttons: [
                {
                    text: '<i class="glyphicon glyphicon-trash "></i> Delete More',                    
                    className: "btn-sm delete_button",
                    
                },
                {
                    text: '<i class="glyphicon glyphicon-plus "></i> Add New',                    
                    className: "btn-sm btn-add",
                    action: function ( e, dt, node, config ) {
                        document.location.href = "";
                    }
                },
                {   
                     extend: 'excel', 
                     text: '<i class="glyphicon glyphicon-export"></i> Export',
                     className: "btn-sm",
                    action: function ( e, dt, node, config ) {
                        document.location.href = "";
                    }
                },
                {
                    text: '<i class="glyphicon glyphicon-import"></i> Import',
                    className: 'btn-sm',
                    action: function ( e, dt, node, config ) {
                        document.location.href = "";
                    }
                }
             ],
             columnDefs: [
             ],
             ajax:{ url:"{{ route('places.services') }}",
                 data: function (d) {
                        d.search_service_cate = $('#search_service_cate :selected').val();
                        d.search_service_status = $('#search_service_status :selected').val();
                        d.search_service_booking = $('#search_service_booking :selected').val();
                        d.place_id = place_id;
                    }
                  },
                 columns: [

                          // { data: 'delete', name: 'delete' },
                          { data: 'service_id', name: 'service_id',class:'text-center' },
                          { data: 'cateservice_name', name: 'cateservice_name' , searchable: false ,class:'text-left'},
                          { data: 'service_name', name: 'service_name' ,class:'text-left'},
                          { data: 'service_price', name: 'service_price',class:'text-right' },
                          { data: 'service_duration', name: 'service_duration',class:'text-right' },
                          { data: 'service_index', name: 'service_index',class:'text-right'},
                          { data: 'action1' , name: 'action1',  orderable: false, searchable: false, class:'text-center' },
                          { data: 'action2' , name: 'action2',  orderable: false, searchable: false,class:'text-center' },
                          { data: 'updated_at', name: 'updated_at',class:'text-center'},
                          { data: 'action' , name: 'action',  orderable: false, searchable: false, class:'text-center' }
                ],
                fnDrawCallback:function (oSettings) {                   
                  var elemsStatus = Array.prototype.slice.call(document.querySelectorAll('.status'));
                  elemsStatus.forEach(function (html) {
                      var switcheryStatus = new Switchery(html, {
                          color: '#0874e8',
                          className : 'switchery switchery-small',                        
                      });
                      switcheryStatus.bindClick = changeStatus;
                  });

                var elemsOnlineBooking = Array.prototype.slice.call(document.querySelectorAll('.online_booking'));
                elemsOnlineBooking.forEach(function (html) {
                    var switcheryOnlineBooking= new Switchery(html, {
                        color: '#0874e8',
                        className : 'switchery switchery-small',                        
                    });                   
                    switcheryOnlineBooking.bindClick = change_online_booking;
                });

                }
                                       
        });
 		menuTable = $('#menus_datatable').DataTable({
             // dom: "lBfrtip",
            serverSide: true,
        	responsive:false,
            buttons: [
                {
                    text: '<i class="glyphicon glyphicon-plus fa fa-plus"></i> Add New',                    
                    className: "btn-sm btn-add",
                    action: function ( e, dt, node, config ) {
                        document.location.href = "menu/0";
                    }
                },
                {
                    text: '<i class="glyphicon glyphicon-import"></i> Import',
                    className: 'btn-sm',
                    action: function ( e, dt, node, config ) {
                        document.location.href = "menus/import";
                    }
                },
             ],
             columnDefs: [
             ],
             ajax:{ url:"{{ route('places.menus') }}",
             data:function(d){
             	d.place_id = place_id;
             }
         	},
                 columns: [
                    { data: 'menu_id', name: 'menu_id',class:'text-center' },
                    { data: 'menu_name', name: 'menu_name' },
                    { data: 'parent_name', name: 'parent_name' },
                    { data: 'menu_url', name: 'menu_url' },
                    { data: 'menu_index', name: 'menu_index' },
                    { data: 'menu_type', name:'menu_type' },
                    { data: 'updated_at', name:'updated_at',class:'text-center'},
                    { data: 'action' , name: 'action',  orderable: false, searchable: false,class:'text-center' }
                ],
                fnDrawCallback:function (oSettings) {
                    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elems.forEach(function (html) {
                        var switchery = new Switchery(html, {
                            color: '#0874e8',
                            className : 'switchery switchery-small'                
                        });
                        switchery.bindClick = change_stt;
                        // e.preventDefault();
                    });
                }
                                       
        });
 		bannersTable = $('#banners_datatable').DataTable({
            // dom: "lBfrtip",
            processing: true, //important
            serverSide: true, //important
            responsive: false, //important
            // autoWidth: true, //important
            buttons: [{
                text: '<i class="glyphicon glyphicon-plus fa fa-plus"></i> Add New',
                className: "btn-sm btn-add",
                action: function(e, dt, node, config) {
                    document.location.href = "banner/0";
                }
            }],
            columnDefs: [
            ],
            ajax: { url: "{{ route('places.banners') }}" },
            columns: [
                { data: 'ba_id', name: 'ba_id', class:'text-center' },
                { data: 'ba_name', name: 'ba_name' },
                { data: 'ba_descript', name: 'ba_descript' },
                { data: 'ba_index', name: 'ba_index',class:'text-center' },
                { data: 'ba_image', name: 'ba_image',class:'text-center' },
                { data: 'updated_at', name: 'updated_at',class:'text-center' },
                { data: 'ba_style', name: 'ba_style' },
                { data: 'action', name: 'action', orderable: false, searchable: false, class:'text-center' }
            ],
            fnDrawCallback: function(oSettings) {
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                elems.forEach(function(html) {
                    var switchery = new Switchery(html, {
                        color: '#0874e8',
                        className: 'switchery switchery-small'
                    });
                    switchery.bindClick = change_stt;
                });
            }
        });
        socialTable = $('#social_network_datatable').DataTable({
            // dom: "lBfrtip",
            processing: true, //important
            serverSide: true, //important
            responsive: false, //important
            // autoWidth: true, //important
            buttons: [{
                text: '<i class="glyphicon glyphicon-plus fa fa-edit"></i>Edit',
                className: "btn-sm social-edit",
            }],
            columnDefs: [
            ],
            ajax: { url: "{{ route('places.socail_network') }}",
            	data:function(d){
            		d.place_id = place_id;
            	}
             },
            columns: [
                { data: 'position', name: 'position',class:'text-center'},
                { data: 'name', name: 'name',},
                { data: 'link', name: 'link' },
            ],
        });
        //SERVICE CATEGORIES
        $(".categories-add").click(function(){
        	$("#add-cate-modal").modal('show');
        });
        $("input[type=file]").change(function() {
	        readURL(this);
	    });

		function readURL(input) {
		    if (input.files[0] && input.files[0]) {
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
		
		$("input[type=file]").change(function() {
		    readURL(this);
		});

	});
</script>
@endpush