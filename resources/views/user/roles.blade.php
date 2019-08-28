@extends('layouts.app')
@section('title')
Role List
@stop
@section('content')
<div class="row">
	<div class="col-md-6">
		<h5>Role List</h5>
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
	<div class="col-md-5 offset-md-1" style="padding-top: 30px">
		<div class="form-group">
			<label for="">Role Name</label>
			<input type="text" class="form-control form-control-sm" name="" id="gu_name">
		</div>
		<div class="form-group">
			<label for="">Role Description</label>
			<textarea class="form-control form-control-sm" rows="3" id="gu_descript" ></textarea>
		</div>
		<div class="form-group">
			<button type="button" class="btn btn-sm btn-danger float-right">Cancel</button>
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
            columnDefs: [
                {
                    "targets": 0, 
                    "className": "text-center"
                },
	            {
	                "targets": 3,
	                "className": "text-center",
	            },
            ],
          ajax:{ url:"{{route('role-datatable')}}"},
                columns:[
	                {data:'gu_id', name:'gu_id'},
	                {data:'gu_name', name:'gu_name'},
	                {data:'gu_descript', name:'gu_descript'},
	                {data:'gu_status', name:'gu_status'},
	                {data:'action', name:'action',orderable: false, searchable: false},
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

			$.ajax({
				url: '{{route('change-status-role')}}',
				type: 'GET',
				dataType: 'html',
				data: {
					gu_status: gu_status,
					gu_id: gu_id
				},
			})
			.done(function() {
				dataTable.draw();
			})
			.fail(function() {
				console.log("error");
				alert('Error!');
			});
			
		});
		$('#dataTable tbody').on( 'click', 'tr', function () {

	      $("#gu_name").val(dataTable.row(this).data()['gu_name']);
	      $("#gu_descript").text(dataTable.row(this).data()['gu_descript']);
	      gu_id = dataTable.row(this).data()['gu_id'];

	    });
	    $(document).on('click','.submit-role',function(){

	    	var gu_descript = $("#gu_descript").text();
	    	var gu_name = $("#gu_name").val();
	    	alert(gu_id);

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
	    		console.log(data);
	    		if(data == 0){
	    			alert('Error!');
	    		}else{
	    			$("#gu_descript").text("");
	    			$("#gu_name").val("");
	    			gu_id = 0;
	    			dataTable.draw();
	    		}
	    		console.log(data);
	    	})
			.fail(function(xhr, ajaxOptions, thrownError) {
            alert('Error  !');
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
         });
	    	
	    })
	});
</script>
@endpush