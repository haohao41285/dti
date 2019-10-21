@extends('layouts.app')
@section('title')
    Service Permission
@stop
@section('content')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5><b>Permission for <i class="text-danger"></i> role</b></h5>
            </div>
            <table class="table table-hover table-bordered" id="service-permission">
                <thead class="text-center thead-light">
                <th>Services/Permissions</th>
                @foreach($role_list as $role)
                    <th>{{$role->gu_name}}</th>
                 @endforeach
                </thead>
                <tbody>
                    @foreach($service_list as $service)
                    <tr>
                        <td>#{{$service->id}}-{{$service->cs_name}}</td>
                        @foreach($role_list as $role)
                            @php
                                $service_permission_list = $role->service_permission;
                                if($service_permission_list == "")
                                    $checked = "";
                                else{
                                    $service_permission_arr = explode(';',$service_permission_list);
                                    if(in_array($service->id,$service_permission_arr))
                                        $checked = "checked";
                                    else
                                       $checked = "";
                                }
                            @endphp
                            <td class="text-center">
                                <input type="checkbox"
                                       service_id="{{$service->id}}" role_id="{{$role->gu_id}}"
                                       {{$checked}}
                                class="js-switch"
                            /></td>
                        @endforeach
                    </tr>
                     @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div>

@stop
@push('scripts')
    <script>

        $(document).ready(function() {
            $(document).on('click','.switchery',function(){

                var service_id = $(this).siblings('input').attr('service_id');
                var role_id = $(this).siblings('input').attr('role');

                $.ajax({
                    url: '{{route('change-service-permission')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        service_id: service_id,
                        role_id: role_id,
                    },
                })
                    .done(function(data) {
                        console.log(data);
                        return;
                        data = JSON.parse(data);

                        if(data.status === 'error')
                            toastr.error(data.message);
                        else
                            toastr.success(data.message);
                        console.log(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });


            });
        });
    </script>
@endpush
