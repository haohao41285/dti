@extends('layouts.app')
@section('title')
    List User
@endsection
@section('content')
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
	<thead>
		<tr>
			<th>ID</th>
			<th>Full Name</th>
			<th>NickName</th>
            <th>Birthday</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Group</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
	</thead>
</table>
@stop
@push('scripts')
<script type="text/javascript">
	$(document).ready(function($) {
		dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
            order: [[0,'desc']],
			buttons: [
              {
                  text: '<i class="fas fa-plus"></i> Add User',
                  className: 'btn btn-sm btn-primary',
                  action: function ( e, dt, node, config ) {
                     document.location.href = "{{ route('user-add') }}";
                  }
              },
              { text : '<i class="fas fa-download"></i> Export',
                className: 'btn btn-sm btn-primary',
                  action:function () {
                      document.location.href = "{{ route('user-export') }}";
                  }
              }
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
	            {
	                "targets": 6,
	                "className": "text-center",
	            },
	            {
	                "targets": 7,
	                "className": "text-center",
	            }
            ],
          ajax:{ url:"{{route('user-datatable')}}"},
                columns:[
                {data:'user_id', name:'user_id'},
                {data:'user_fullname', name:'user_fullname',orderable: false, searchable: false},
                {data:'user_nickname', name:'user_nickname'},
                {data:'user_birthdate', name:'user_birthdate'},
                {data:'user_phone', name:'user_phone'},
                {data:'user_email', name:'user_email'},
                {data:'gu_name', name:'gu_name',orderable: false, searchable: false},
                {data:'user_status', name:'user_status'},
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
	});
	$(document).on('click','.switchery',function(){

		var user_status = $(this).siblings('input').attr('user_status');
		var user_id = $(this).siblings('input').attr('user_id');

		$.ajax({
			url: '{{route('change-user-status')}}',
			type: 'GET',
			dataType: 'html',
			data: {
				user_status: user_status,
				user_id: user_id
			},
		})
		.done(function(data) {
		    data = JSON.parse(data);
		    if(data.status == 'error')
		        toastr.error(data.message);
		    else{
                dataTable.draw();
                toastr.success(data.message);
            }
		})
		.fail(function() {
		    toastr.error('Change Failed!');
		});
	});
    $(document).on('click','.delete-user',function () {

        let user_id = $(this).attr('user_id');

        if(confirm('Do you want delete this user from database?')){
            $.ajax({
                url: '{{route('user-delete')}}',
                type: 'POST',
                dataType: 'html',
                data: {
                    user_id: user_id,
                    _token: '{{csrf_token()}}'
                },
            })
                .done(function(data) {
                    data = JSON.parse(data);
                    if(data.status == 'error'){
                        toastr.error(data.message);
                    }else{
                        toastr.success(data.message);
                        dataTable.ajax.reload(null, false);
                    }
                })
                .fail(function() {
                    toastr.error('Delete Failed!');
                });
        }
    });
</script>
@endpush
