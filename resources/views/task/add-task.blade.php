@extends('layouts.app')
@section('title')
    Add Task
@endsection
@push('styles')
<style type="text/css" media="screen">
   .note-popover.popover {
        display: none;
   }
</style>
@endpush
@section('content')
	<div class="table-responsive">
	<h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">ADD NEW TASK</h4>
    <form action="{{route('save-task')}}" id="form-task" method="post" accept-charset="utf-8">
        @csrf()
        <table class="table table-striped mt-4 table-bordered" id="dataTableAllCustomer" widtd="100%" cellspacing="0">
            <tbody>
                <tr>
                    <td colspan="7"><label for="subject" class="required">SUBJECT</label>
                    </td>
                </tr>
                <tr>
                    <th colspan="7">
                        <input type="text" id="subject" required class="form-control form-control-sm" name="subject">
                    </th>
                </tr>
                <tr>
                    <td>CATEGORY</td>
                    <td>PRIORITY</td>
                    <td>TASK STATUS</td>
                    <td>DATE START</td>
                    <td>DATE END</td>
                    <td>%COMPLETE</td>
                    <td>ASSIGN TO</td>
                </tr>
                <tr>
                    <th>
                        <select name="category" class="form-control form-control-sm">
                            @foreach(getCategory() as $key => $category)
                            <option value="{{$key}}">{{$category}}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <select name="priority" class="form-control form-control-sm">
                            @foreach(getPriorityTask() as $key => $category)
                            <option {{$key==2?"selected":""}}  value="{{$key}}">{{$category}}</option>
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <select name="status" class="form-control form-control-sm">
                            @foreach(getStatusTask() as $key => $category)
                                @if($key ==1)
                                    <option value="{{$key}}">{{$category}}</option>
                                @endif
                            @endforeach
                        </select>
                    </th>
                    <th>
                        <input type="text" id="date_start" class="form-control form-control-sm" name="date_start">
                    </th>
                    <th>
                        <input type="text" id="date_end" class="form-control form-control-sm" name="date_end">
                    </th>
                    <th>
                        <input type="number" id="complete_percent" class="form-control form-control-sm" name="complete_percent">
                    </th>
                    <th>
                        <select name="assign_to" class="form-control form-control-sm text-capitalize">
                            @foreach($user_list as $key => $user)
                            <option value="{{$user->user_id}}" >{{$user->user_nickname}}({{$user->getFullname()}})</option>
                            @endforeach
                        </select>
                    </th>
                </tr>
                <tr>
                    <td colspan="7">TASK DESCRIPTION</td>
                </tr>
                <tr>
                    <td colspan="7">
                        <textarea class="fom-control form-control-sm" name="desription" id="description"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>PARENT TASK</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" id="task_parent_id" name="task_parent_id" value="{{$task_parent_id>0?$task_parent_id:""}}">
                    </td>
                    <th colspan="5" id="task_name" class="text-uppercase">{{$task_name}}</th>
                </tr>
            </tbody>
        </table>
        <div class="form-group">
            <input type="button" name="" class="btn btn-primary btn-sm submit-form" value="Submit">
            <input type="reset" name="" class="btn btn-danger btn-sm" value="Cancel">
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
	$(document).ready(function() {
		$("#date_start").datepicker({
            todayHighlight: true,
            setDate: new Date(),
        });
        $("#date_end").datepicker({
            todayHighlight: true,
            setDate: new Date(),
        });
        $('#description').summernote({
        	toolbar: [
			    // [groupName, [list of button]]
			    ['style', ['bold', 'italic', 'underline', 'clear']],
			    ['font', ['strikethrough', 'superscript', 'subscript']],
			    ['fontsize', ['fontsize']],
			    ['color', ['color']],
			    ['para', ['ul', 'ol']],
			    ['height', ['height']]
			  ]
        });
        $("#task_parent_id").keyup(function(event) {

            var task_parent_id = $(this).val();
            if(task_parent_id == "")
                $("#task_name").removeClass('text-danger').text("");
            else{
                $.ajax({
                    url: '{{route('get-task')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {task_parent_id: task_parent_id},
                })
                .done(function(data) {
                    data = JSON.parse(data);
                    if(data.status == "error"){
                        $("#task_name").addClass('text-danger').text(data.message);
                    }else{
                        $("#task_name").removeClass('text-danger').text(data.task_name);
                    }
                })
                .fail(function() {
                    $("#task_name").addClass('text-danger').text('ID Task Correctly');
                });
            }
        });
        $(".submit-form").click(function(e){
            if($("#task_name").hasClass('text-danger')){
                toastr.error("Choose Parent ID Task, Please!");
                e.preventDefault();
                return;
            }
            if($("#subject").val() == ""){
                toastr.error("Enter Subject, Please!");
                e.preventDefault();
                return;
            }
            $("#form-task").submit();
        });
	});
</script>
@endpush
