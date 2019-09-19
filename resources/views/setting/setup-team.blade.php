@extends('layouts.app')
@section('title')
Setup Team
@endsection
@push('styles')
<style>
	.scroll{
        display:block;
        padding:5px;
        margin-top:5px;
        width:300px;
        height:50px;
        overflow:auto;
    }
   .scroll thead th {
       background: #f8f9fc;
	   position: sticky;
    	 top: 0;
	}
	.scroll .table tbody tr:hover{
			background: #4e73df;
			color: #fff;
	}
</style>
@endpush
@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card shadow mb-4 ">
      <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary">Team List <button type="button" class="float-right btn btn-primary btn-sm add-team">Add Team</button></h6>
        
      </div>
      <div class="card-body">
          <table class="table table-bordered table-hover" id="team-table" width="100%" height="50%" cellspacing="0">
            <thead>
               <th>ID</th>
               <th>Team name</th>
               <th>Leader</th>
               <th>Team type</th>
               <th>Action</th>
            </thead>
         </table>   
      </div>
    </div>

    <div class="card shadow mb-4 ">
      <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary">User List</h6>
      </div>
      <div class="card-body"  >
          <table class="table table-bordered table-hover"  id="user-table" width="100%"  cellspacing="0">
            <thead>
               <th>ID</th>
               <th>Member</th>
               <th>Team</th>
            </thead>
          </table>   
      </div>
    </div>

  </div>
  <div class="col-md-4" >
    <div class="card shadow mb-4 ">
      <div class="card-header py-2">
        <h6 class="m-0 font-weight-bold text-primary">Members of <b class="team-name-list"></b> Team</h6>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-hover" id="member-table" width="100%" cellspacing="0">
            <thead>
               <th>Member</th>
                <th>Action</th>
               </tr>
            </thead>
         </table>
      </div>
    </div>
  </div>

</div>

<div class="row">
  
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="title-team"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="team-content-detail">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm save-change">Save changes</button>
        <button type="button" class="btn btn-danger btn-sm cancle-change" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
  <script>
    $(document).ready(function(){

      var team_id = 0;
      //GET TEAM LIST
      var table = $('#team-table').DataTable({
         // dom: "lBfrtip",
         searching: false,
         paging: false,
         info: false,
         scrollY: "200px",
         buttons: [
             ],  
         processing: true,
         serverSide: true,
         ajax:{ url:"{{ route('get-team-list') }}",
         data: function (d) {
              } 
          },
         columns: [
                  { data: 'id', name: 'id' },
                  { data: 'team_name', name: 'team_name' },
                  { data: 'team_leader', name: 'team_leader',class: 'text-capitalize' },
                  { data: 'team_type_name', name: 'team_type_name'},    
                  { data: 'action' , name:'action' ,class:'text-center'}
          ],       
    });
      //GET MEMBER LIST
      var memberTable = $('#member-table').DataTable({
         // dom: "lBfrtip",
         searching: false,
         paging: false,
         info: false,
         scrollY: true,
         buttons: [
         ],  
         processing: true,
         serverSide: true,
         ajax:{ url:"{{ route('get-member-list') }}",
         data: function (d) {
            d.team_id = team_id
              } 
          },
         columns: [
            { data: 'user_fullname', name: 'user_fullname',class: 'text-capitalize' },
            { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
          ],       
        });
      //GET USER LIST
      var userTable = $('#user-table').DataTable({
         // dom: "lBfrtip",
         searching: false,
         paging: false,
         scrollY: "300px",
         info: false,
         buttons: [
         ],  
         processing: true,
         serverSide: true,
         ajax:{ url:"{{ route('get-user-list') }}",
         data: function (d) {
              } 
          },
         columns: [
            { data: 'user_id', name: 'user_id'},
            { data: 'user_fullname', name: 'user_fullname', class: 'text-capitalize' },
            { data: 'team_name', name: 'team_name' }
          ],       
        });

      $(document).on('click','.edit-team',function(e){
        e.preventDefault();

        var team_id = $(this).attr('team_id');
        var team_type = $(this).attr('team_type');
        var leader_id = $(this).attr('leader_id');
        var team_name = $(this).attr('team_name');

        $.ajax({
          url: '{{route('edit-team')}}',
          type: 'GET',
          dataType: 'html',
          data: {team_id: team_id},
        })
        .done(function(data) {

          data = JSON.parse(data);
          var team_leader_html = "";
          var team_type_html = "";

          $.each(data.user_list, function(index, val) {
            if(leader_id == val['user_id'])  var selected = "selected";
            else var selected = "";
            team_leader_html += '<option '+selected+' value="'+val['user_id']+'" class="text-capitalize">'+val['user_firstname']+" "+val['user_lastname']+'</option>';
          });

          $.each(data.team_type_list, function(index, val) {
            if(team_type == val['id'])  var selected = "selected";
            else var selected = "";
            team_type_html += '<option '+selected+' value="'+val['id']+'">'+val['team_type_name']+'</option>';
          });
          $("#title-team").html('Edit <b>'+team_name+'</b> Team');
          $("#team-content-detail").html(`
              <div class="form-group row">
                <label class="col-md-4" for="team_name">Team Name</label>
                <input type="text" class="col-md-8 form-control" team_id="`+team_id+`" name="team_name" id="team_name" value="`+team_name+`" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="team_name">Team Leader</label>
                <select name="" class="col-md-8 form-control" id="team_leader">
                  `+team_leader_html+`
                </select>
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="team_type">Team Type</label>
                <select name="" class="col-md-8 form-control" id="team_type_id">
                  `+team_type_html+`
                </select>
              </div>
          `);
        
        $("#editModal").modal("show");
        })
        .fail(function() {
          toastr.error("Error. Check Again!");
        });
      });
      $('.cancle-change').click(function(){
         $("#team-content-detail").html('');
      });
      $(".save-change").click(function(){

        var team_id = $("#team_name").attr('team_id');
        var team_name = $("#team_name").val();
        var team_leader = $("#team_leader :selected").val();
        var team_type_id = $("#team_type_id :selected").val();

        $.ajax({
          url: '{{route('save-team')}}',
          type: 'GET',
          dataType: 'html',
          data: {
            team_id: team_id,
            team_name: team_name,
            team_leader: team_leader,
            team_type_id: team_type_id
          },
        })
        .done(function(data) {
          data = JSON.parse(data);
          if(data.status == 'error')
            toastr.error(data.message);
          else{
            $("#editModal").modal('hide');
            table.draw();
            memberTable.draw();
            toastr.success(data.message);
          }
        })
        .fail(function() {
          toastr.error("Error. Check again!");
        });
      });
      $(document).on('click','.delete-team',function(){

        var team_id = $(this).attr('team_id');
        if(confirm("This team may has user. Do you want delete?")){
            $.ajax({
            url: '{{route('delete-team')}}',
            type: 'GET',
            dataType: 'html',
            data: {team_id:team_id},
          })
          .done(function(data) {
            data = JSON.parse(data);
            if(data.status == 'error')
              toastr.error(data.message);
            else{
              table.draw();
              toastr.success(data.message);
            }
          })
          .fail(function() {
            toastr.error("Error. Check again!");
          });
        }else{

        }
      });
      $("#team-table tbody").on('click','tr',function(){
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
        team_id = table.row(this).data()['id'];
        var team_name = table.row(this).data()['team_name'];
        memberTable.draw();
        $(".team-name-list").html(team_name);
      });

      $(document).on('click','.remove-member',function(){

        var user_id = $(this).attr('user_id');

        $.ajax({
          url: '{{route('remove-member-from-team')}}',
          type: 'GET',
          dataType: 'html',
          data: {user_id: user_id},
        })
        .done(function(data) {
          data = JSON.parse(data);
          if(data.status == 'error')
            toastr.error(data.message);
          else{
            memberTable.draw();
            userTable.draw();
            toastr.success(data.message);
          }
        })
        .fail(function() {
          toastr.error("Error. Check again!");
        });
      });
      $("#user-table tbody").on('click','tr',function(){

        if(team_id == 0)
          toastr.error('Choose Team want to add, first!');
        else{

          var user_id = userTable.row(this).data()['user_id'];

          $.ajax({
            url: '{{route('add-member-to-team')}}',
            type: 'GET',
            dataType: 'html',
            data: {
              user_id: user_id,
              team_id: team_id
            },
          })
          .done(function(data) {
            data = JSON.parse(data);
            if(data.status == 'error')
              toastr.error(data.message);
            else{
              toastr.success(data.message);
              userTable.draw();
              memberTable.draw();
            }
          })
          .fail(function() {
            toastr.error("Error. Check again!");
          });
        }
      });
      $(document).on('click','.add-team',function(e){

        $.ajax({
          url: '{{route('edit-team')}}',
          type: 'GET',
          dataType: 'html',
          data: {team_id: team_id},
        })
        .done(function(data) {

          data = JSON.parse(data);
          var team_leader_html = "";
          var team_type_html = "";

          $.each(data.user_list, function(index, val) {
            team_leader_html += '<option value="'+val['user_id']+'" class="text-capitalize">'+val['user_firstname']+" "+val['user_lastname']+'</option>';
          });

          $.each(data.team_type_list, function(index, val) {
            team_type_html += '<option value="'+val['id']+'">'+val['team_type_name']+'</option>';
          });
          $("#title-team").text("Add New Team");
          $("#team-content-detail").html(`
              <div class="form-group row">
                <label class="col-md-4" for="team_name">Team Name</label>
                <input type="text" class="col-md-8 form-control" team_id="0" name="team_name" id="team_name" value="" placeholder="">
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="team_name">Team Leader</label>
                <select name="" class="col-md-8 form-control" id="team_leader">
                  `+team_leader_html+`
                </select>
              </div>
              <div class="form-group row">
                <label class="col-md-4" for="team_type">Team Type</label>
                <select name="" class="col-md-8 form-control" id="team_type_id">
                  `+team_type_html+`
                </select>
              </div>
          `);
        
        $("#editModal").modal("show");
        })
        .fail(function() {
          toastr.error('Error! Check again!');
        });
      });
    });
  </script>
@endpush
