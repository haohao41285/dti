@extends('layouts.app')
@section('title')
    User Permission
@stop
@push('styles')

@endpush
@section('content')
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                <td class="text-center">
                    <input type="checkbox" class="js-switch" checked/>
                </td>
            @endforeach
        </tr>
    @endforeach
    @foreach($menu_parent as $menu)
        <tr>
            <th colspan="{{$role_list->count()+1}}">{{$menu->name}}</th>
        </tr>
        @if(count($menu->getPermission) != 0)
            @foreach($menu->getPermission as $permission)
                <tr >
                    <td class="pl-5">{{$permission->permission_name}}</td>
                    @foreach($role_list as $role)
                        <td class="text-center">
                            <input type="checkbox" class="js-switch" checked/>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @else
            @foreach($menu->getMenuChild as $menu_child)
                @foreach($menu_child->getPermission as $permission)
                    <tr>
                        <td class="pl-5">{{$permission->permission_name}}</td>
                        @foreach($role_list as $role)
                            <td class="text-center">
                                <input type="checkbox" class="js-switch" checked/>
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

    </script>
@endpush
