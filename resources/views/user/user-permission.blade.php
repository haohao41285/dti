@extends('layouts.app')
@section('title')
    User Permission
@stop
@push('styles')
@endpush
@section('content')
<div  style="height:300px">
    <table class="table tabel-sm table-hover table-bordered" id="dataTable" >
        <thead>
            <tr class="sticky-top bg-primary text-white" style="z-index: 1000">
                <th style="width: 20%">Permission</th>
                @foreach($role_list as $role)
                    <th class="text-center">{{$role->gu_name}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="">
        @foreach($permission_other as $permission)
            <tr>
                <td>{{$permission->permission_name}}</td>
                @foreach($role_list as $role)
                    @php
                        $check = checkPermission($role,$permission);
                    @endphp
                    <td class="text-center">
                        {{-- <input type="checkbox" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check==""?0:1}}" {{$check}} class="js-switch"/> --}}
                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="switch_{{$role->gu_id}}_{{$permission->id}}" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check==""?0:1}}" {{$check}} class="js-switch">
                          <label class="custom-control-label" for="switch_{{$role->gu_id}}_{{$permission->id}}"></label>
                        </div>
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
                                {{-- <input type="checkbox" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check==""?0:1}}" class="js-switch" {{$check}}/> --}}
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" class="custom-control-input" id="switch_{{$role->gu_id}}_{{$permission->id}}"  role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check==""?0:1}}" class="js-switch" {{$check}}>
                                  <label class="custom-control-label" for="switch_{{$role->gu_id}}_{{$permission->id}}"></label>
                                </div>
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
                                    {{-- <input type="checkbox" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check1==""?0:1}}" class="js-switch" {{$check1}}/> --}}
                                    <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="switch_{{$role->gu_id}}_{{$permission->id}}" role_id="{{$role->gu_id}}" permission_id="{{$permission->id}}" check="{{$check1==""?0:1}}" class="js-switch" {{$check1}}>
                                      <label class="custom-control-label" for="switch_{{$role->gu_id}}_{{$permission->id}}"></label>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>
</div>
@stop
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".permission_parent").click(function(){
                let id = $(this).attr('id');
                $(".child_"+id).toggleClass('d-none');
                $("#parent_"+id).toggleClass(' fa-minus-circle  fa-plus-circle').toggleClass('text-danger text-primary');
            });
            $(document).on('click','.custom-control-input',function(){

                var check = $(this).attr('check');
                var permission_id = $(this).attr('permission_id');
                var role_id = $(this).attr('role_id');

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
