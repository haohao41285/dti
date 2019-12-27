@extends('layouts.app')
@section('title')
    Service Permission
@stop
@section('content')
    <div class="container-fluid">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5><b>Permission for <i class="text-danger"></i> team</b></h5>
            </div>
            <table class="table table-sm table-hover table-bordered" id="service-permission">
                <thead class="text-center thead-light">
                <th>Services/Permissions</th>
                @foreach($team_list as $team)
                    <th>{{$team->team_type_name}}</th>
                 @endforeach
                </thead>
                <tbody>
                @foreach($service_type_list as $service_type)
                    <tr class="thead-light">
                        <td colspan="{{$team_list->count()+1}}"><b>{{$service_type->name}}</b></td>
                    </tr>
                    {{-- @foreach($service_type->getComboService as $service) --}}
                    @foreach($combo_service_list->where('cs_combo_service_type',$service_type->id) as $service)
                    <tr>
                        <td>#{{$service->id}}-{{$service->cs_name}}</td>
                        @foreach($team_list as $team)
                            @php
                                $service_permission_list = $team->service_permission;
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
                                       service_id="{{$service->id}}" team_id="{{$team->id}}"
                                       {{$checked}}
                                class="js-switch"
                            /></td>
                        @endforeach
                    </tr>
                     @endforeach
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
                var team_id = $(this).siblings('input').attr('team_id');

                $.ajax({
                    url: '{{route('change-service-permission')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        service_id: service_id,
                        team_id: team_id,
                    },
                })
                    .done(function(data) {
                        data = JSON.parse(data);
                        // console.log(data);
                        // return;
                        if(data.status == 'error')
                            toastr.error(data.message);
                        else
                            toastr.success(data.message);
                    })
                    .fail(function() {
                        toastr.error('Change Failed!');
                    });


            });
        });
    </script>
@endpush
