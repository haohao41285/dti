@extends('layouts.app')
@section('title')
    User Permission
@stop
@push('styles')

@endpush
@section('content')
<table class="table table-sm  table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th style="width: 20%">Permission</th>
            @foreach($role_list as $role)
                <th class="text-center">{{$role->gu_name}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach($permission_other as $permission)
        <tr>
            <td>{{$permission->permission_name}}</td>
            @foreach($role_list as $role)
                @php
                    $check = checkPermission($role,$permission);
                @endphp
                <td class="text-center">
                    <input type="checkbox" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check==""?0:1}}" {{$check}} class="js-switch"/>
                </td>
            @endforeach
        </tr>
    @endforeach
    @foreach($menu_parent as $menu)
        <tr id="{{$menu->id}}" class="permission_parent ">
            <th colspan="{{$role_list->count()+1}}"><i id="parent_{{$menu->id}}" class="fas fa-plus-circle text-primary"></i>{{$menu->name}}</th>
        </tr>
        @if($permission_list->where('menu_id',$menu->id)->count() != 0)
            @foreach($permission_list->where('menu_id',$menu->id) as $permission)
                <tr class="child_{{$menu->id}} d-none">
                    <td class="pl-5">{{$permission->permission_name}}</td>
                    @foreach($role_list as $role)
                        @php
                            $check = checkPermission($role,$permission);
                        @endphp
                        <td class="text-center">
                            <input type="checkbox" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check==""?0:1}}" class="js-switch" {{$check}}/>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @else
            @foreach($menu_list_all->where('parent_id',$menu->id) as $menu_child)

                @foreach($permission_list->where('menu_id',$menu_child->id) as $permission)

                    <tr class="child_{{$menu->id}} d-none">
                        <td class="pl-5">{{$permission->permission_name}}</td>
                        @foreach($role_list as $role)
                            @php
                                $check1 = checkPermission($role,$permission);
                            @endphp
                            <td class="text-center">
                                <input type="checkbox" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check1==""?0:1}}" class="js-switch" {{$check1}}/>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
@stop
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".permission_parent").click(function(){
                let id = $(this).attr('id');
                $(".child_"+id).toggleClass('d-none');
                $("#parent_"+id).toggleClass(' fa-minus-circle  fa-plus-circle').toggleClass('text-danger text-primary');
            });
            $(document).on('click','.switchery',function(){

                var check = $(this).siblings('input').attr('check');
                var permission_id = $(this).siblings('input').attr('permission_id');
                var role_id = $(this).siblings('input').attr('role_id');

                $.ajax({
                    url: '{{route('change-permission-role')}}',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        check: check,
                        permission_id: permission_id,
                        role_id: role_id,
                        _token: '{{csrf_token()}}'
                    },
                })
                    .done(function(data) {
                        console.log(data);
                        // return;
                        data = JSON.parse(data);
                        if(data.status == 'error')
                            toastr.error(data.message);
                        else{
                            toastr.success(data.message);
                        }
                    })
                    .fail(function() {
                        toastr.error('Change Failed!');
                    });
            });
        });
    </script>
@endpush
