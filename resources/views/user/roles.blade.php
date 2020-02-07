@extends('layouts.app')
@section('content-title')
	ROLES
@stop
@section('content')
<div class="row">
	<div class="col-md-6">
		<h5><b>Role List</b></h5>
        <table class="table table-sm table-bordered" id="dataTable" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th class="text-center">ID</th>
					<th>Role Name</th>
					<th>Description</th>
					<th>Status</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="col-md-5 offset-md-1" style="padding-top: 0px">
		<h5><b class="role-tip">Add Role</b></h5>
		<div class="form-group">
			<label for="">Name</label>
			<input type="text" class="form-control form-control-sm" name="" id="gu_name">
		</div>
		<div class="form-group">
			<label for="">Description</label>
			<textarea class="form-control form-control-sm" rows="3" id="gu_descript" ></textarea>
		</div>
		<div class="form-group">
			<button type="button" class="btn btn-sm btn-danger float-right cancel-role ml-2">Cancel</button>
			<button type="button" class="btn btn-sm btn-primary float-right submit-role">Submit</button>
		</div>
	</div>
</div>

@stop
@push('scripts')
<script type="text/javascript">
	//DEFINE VAR
	var gu_id = 0;
	$(document).ready(function($) {
		dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
			buttons: [
            ],
          ajax:{ url:"{{route('role-datatable')}}"},
                columns:[
	                {data:'gu_id', name:'gu_id',class:'text-center'},
	                {data:'gu_name', name:'gu_name'},
	                {data:'gu_descript', name:'gu_descript'},
	                {data:'gu_status', name:'gu_status',class:'text-center'},
	                {data:'action', name:'action',orderable: false, searchable: false,class:'text-center'},
                ],
                fnDrawCallback:function (oSettings) {
                    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elems.forEach(function (html) {
                        var switchery = new Switchery(html, {
                            color: '#0874e8',
                            className : 'switchery switchery-small'
                        });
                    });
                }
		})
		$(document).on('click','.switchery',function(){

			var gu_id = $(this).siblings('input').attr('gu_id');
			var gu_status = $(this).siblings('input').attr('gu_status');
			clearView();

			$.ajax({
				url: '{{route('change-status-role')}}',
				type: 'GET',
				dataType: 'html',
				data: {
					gu_status: gu_status,
					gu_id: gu_id
				},
			})
			.done(function(data) {
				data = JSON.parse(data);
				if(data.status == 'error')
				    toastr.error(data.message);
				else
				    toastr.success(data.message);
				dataTable.draw();
			})
			.fail(function(data) {
				data = JSON.parse(data.responseText);
				toastr.error(data.message);
				dataTable.draw();
			});

		});
		$('#dataTable tbody').on( 'click', 'tr', function () {

	      $("#gu_name").val(dataTable.row(this).data()['gu_name']);
	      $("#gu_descript").val(dataTable.row(this).data()['gu_descript']);
	      $(".role-tip").text("Edit Role");
	      gu_id = dataTable.row(this).data()['gu_id'];

	    });
	    $(document).on('click','.submit-role',function(){

	    	var gu_descript = $("#gu_descript").val();
	    	var gu_name = $("#gu_name").val();

	    	if(gu_name === "")
	    		toastr.error('Name is required!');
	    	else{
	    		$.ajax({
		    		url: '{{route('add-role')}}',
		    		type: 'GET',
		    		dataType: 'html',
		    		data: {
		    			gu_descript: gu_descript,
		    			gu_name: gu_name,
		    			gu_id: gu_id
		    		},
		    	})
		    	.done(function(data) {
		    		data = JSON.parse(data);
		    		if(data.status == 'error')
		    		    toastr.error(data.message);
		    		else
		    		    toastr.success(data.message);

	      				clearView();
		    			dataTable.draw();
		    	})
				.fail(function(data) {
				    data = JSON.parse(data.responseText);
				    toastr.error(data.message);
	         	});
	    	}
	    });
	    $(".cancel-role").click(function(){
	    	clearView();
	    })
	    function clearView(){
	    	$(".role-tip").text("Add Role");
			$("#gu_descript").val("");
			$("#gu_name").val("");
			gu_id = 0;
	    }
	    $(document).on('click','.role-delete',function(){

	    	if(confirm('Do you want to delete this role?')){
	    		$.ajax({
		    		url: '{{route('delete-role')}}',
		    		type: 'POST',
		    		dataType: 'html',
		    		data: {
		    			gu_id: gu_id,
		    			_token: '{{ csrf_token() }}'
		    		},
		    	})
		    	.done(function(data) {
		    		data = JSON.parse(data);
		    		if(data.status == 'error')
		    		    toastr.error(data.message);
		    		else
		    		    toastr.success(data.message);

	      				clearView();
		    			dataTable.draw();
		    	})
				.fail(function(data) {
				    data = JSON.parse(data.responseText);
				    toastr.error(data.message);
	         	});
	    	}
	    	else
	    		return;	    		
	    });
	});
</script>
@endpush
