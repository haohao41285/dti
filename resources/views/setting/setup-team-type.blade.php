@extends('layouts.app')
@section('title')
Team Type List
@stop
@section('content')
<div class="row">
	<div class="col-md-6">
		<h5><b>Team Type List</b></h5>
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th class="text-center">ID</th>
					<th>Team Type Name</th>
					<th>Description</th>
					<th>Status</th>
					<th>Created At</th>
					<th class="text-center" style="width:100px">Action</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="col-md-5 offset-md-1" style="padding-top: 0px">
		<h5><b class="tt-tip">Add Team Type</b></h5>
		<div class="form-group">
			<label for="">Name</label>
			<input type="text" class="form-control form-control-sm" name="" id="team_type_name">
		</div>
		<div class="form-group">
			<label for="">Description</label>
			<textarea class="form-control form-control-sm" rows="3" id="team_type_description" ></textarea>
		</div>
		<div class="form-group">
			<button type="button" class="btn btn-sm btn-danger float-right cancel-tt ml-2">Cancel</button>
			<button type="button" class="btn btn-sm btn-primary float-right submit-tt">Submit</button>
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
          ajax:{ url:"{{route('team-type-datatable')}}"},
                columns:[
	                {data:'id', name:'id',class:'text-center'},
	                {data:'team_type_name', name:'team_type_name'},
	                {data:'team_type_description', name:'team_type_description'},
	                {data:'team_type_status', name:'team_type_status',class: 'text-center'},
	                {data:'created_at', name:'created_at',class: 'text-center'},
	                {data:'action', name:'action',orderable: false, searchable: false,class: 'text-center'},
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

			var id = $(this).siblings('input').attr('id');
			var team_type_status = $(this).siblings('input').attr('team_type_status');
			clearView();

			$.ajax({
				url: '{{route('change-status-team-type')}}',
				type: 'GET',
				dataType: 'html',
				data: {
					team_type_status: team_type_status,
					id: id
				},
			})
			.done(function(data) {
				if(data != ""){
					data = JSON.parse(data);
					if(data.message == "error"){
						toasrt.error(data.message);
					}else
					toastr.success(data.message);
				}
				dataTable.draw();
			})
			.fail(function(data) {
				data = JSON.parse(data.responseText);
				alert(data.message);
				dataTable.draw();
			});
			
		});
		$('#dataTable tbody').on( 'click', 'tr', function () {

	      $("#team_type_name").val(dataTable.row(this).data()['team_type_name']);
	      $("#team_type_description").val(dataTable.row(this).data()['team_type_description']);
	      $(".tt-tip").text("Edit Team Type");
	      id = dataTable.row(this).data()['id'];

	    });
	    $(document).on('click','.submit-tt',function(){

	    	var team_type_description = $("#team_type_description").val();
	    	var team_type_name = $("#team_type_name").val();

	    	if(team_type_description != "" && team_type_name != ""){
	    		$.ajax({
		    		url: '{{route('add-team-type')}}',
		    		type: 'GET',
		    		dataType: 'html',
		    		data: {
		    			team_type_description: team_type_description,
		    			team_type_name: team_type_name,
		    			id: id
		    		},
		    	})
		    	.done(function(data) {
		    	    data = JSON.parse(data);
		    		if(data.status == 'error'){
		    			toastr.error(data.message);
		    		}else{
	      				clearView();
		    			dataTable.draw();
		    		}
		    		console.log(data);
		    	})
				.fail(function(xhr, ajaxOptions, thrownError) {
	                toastr.error('Error!');
	                // console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	         	});
	    	}
	    });
	    $(".cancel-tt").click(function(){
	    	clearView();
	    })
	    function clearView(){
	    	$(".tt-tip").text("Add Team Type");
			$("#team_type_description").val("");
			$("#team_type_name").val("");
		    id = 0;
	    }
	});
</script>
@endpush