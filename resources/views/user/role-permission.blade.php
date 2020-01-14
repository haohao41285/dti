@extends('layouts.app')
@section('title')
User Permission
@stop
@section('content')
<div class="container-fluid">

  <div class="card shadow mb-4">
    <div class="card-header py-3">
    	<h5><b>Permission for <i class="text-danger">{{$role_name}}</i> role</b></h5>
    </div>
      <table class="table table-sm table-hover table-bordered">
          <thead>
                <th>Permissions</th>
            @foreach($permission_arr as $permission)
                <th class="text-center">{{$permission}}</th>
            @endforeach
          </thead>
            @foreach($menu_list as $key => $menu)
                @if(!isset($menu['childrens']))
          	    <tr>
                    <td>{{$menu['text']}}</td>
                @foreach($permission_arr as $permission)
                    <td class="text-center">
                        <input type="checkbox"
                               permission_status="{{$role_permission_arr[$menu['text']][$permission]??0}}"
                               {{(!isset($role_permission_arr[$menu['text']][$permission])|| $role_permission_arr[$menu['text']][$permission]==0)?"":"checked"}}
                               menu="{{$menu['text']}}"
                               permission_name="{{$permission}}"
                               class="js-switch"
                        />
                    </td>
                @endforeach
                </tr>
                @else
                <tr>
                	<td colspan="5" onclick="Collapse('{{$key}}',this)"><i class="fa fa-plus-square " aria-hidden="true"></i>{{$menu['text']}}</td>
                </tr>
	                @foreach($menu['childrens'] as $key_chil => $value_chil)
	                <tr class="{{$key}}" style="display: none">
	                	    <td style="padding-left: 30px">{{$value_chil['text']}}</td>
                	    @foreach($permission_arr as $permission)
		                    <td class="text-center">
                                <input type="checkbox"
                                       permission_status="{{$role_permission_arr[$value_chil['text']][$permission]??0}}"
                                       {{(!isset($role_permission_arr[$value_chil['text']][$permission])||$role_permission_arr[$value_chil['text']][$permission]==0)?"":"checked"}}
                                       menu="{{$value_chil['text']}}"
                                       permission_name="{{$permission}}"
                                       class="js-switch"
                                />
                            </td>
		                @endforeach
		            </tr>
	                @endforeach
                @endif
            @endforeach
          </tbody>
      </table>
    </div>
  </div>

</div>

@stop
@push('scripts')
<script>
	function Collapse(class_tr,that){
		var status = $(that).children('i');
		status.toggleClass('fa-plus-square fa-minus-square');
		if(status.hasClass('fa-plus-square')){
		    $("."+class_tr).css('display', 'none');
		}else
			$("."+class_tr).css('display', '');
	}

	$(document).ready(function() {
		$(document).on('click','.switchery',function(){

			var permission_status = $(this).siblings('input').attr('permission_status');
			var permission_name = $(this).siblings('input').attr('permission_name');
			var menu = $(this).siblings('input').attr('menu');

			$.ajax({
				url: '{{route('change-permission')}}',
				type: 'GET',
				dataType: 'html',
				data: {

					permission_status: permission_status,
					permission_name: permission_name,
					menu: menu,
					role_id: {{$role_id}}
				},
			})
			.done(function(data) {
				if(data == 1)
					toastr.success('Change Permission Success!');
				else
					toastr.error('Change Permission Error!');
				console.log(data);
			})
			.fail(function() {
				console.log("error");
			});


		});
	});
</script>
@endpush
