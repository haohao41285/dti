@extends('layouts.app')
@section('content-title')
    SERVICE PERMISSIONS
@stop
@section('content')
<div style="height:300px">
    <table class="table table-sm table-hover table-bordered" id="service-permission">
        <thead>
            <tr  class="sticky-top bg-primary text-white" style="z-index: 1000">
                <th>Services/Permissions</th>
                @foreach($team_list as $team)
                    <th class="text-center">{{$team->team_type_name}}</th>
                 @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($service_type_list as $service_type)
            <tr>
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
                        {{-- <input type="checkbox"
                               service_id="{{$service->id}}" team_id="{{$team->id}}"
                               {{$checked}}
                        class="js-switch"/> --}}

                        <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" service_id="{{$service->id}}" team_id="{{$team->id}}"
                               {{$checked}} class="js-switch" id="switch_{{$service->id}}_{{$team->id}}">
                          <label class="custom-control-label" for="switch_{{$service->id}}_{{$team->id}}"></label>
                        </div>
                </td>
                @endforeach
            </tr>
             @endforeach
        @endforeach
        </tbody>
    </table>
</div>

@stop
@push('scripts')
    <script>

        $(document).ready(function() {
            $(document).on('click','.custom-control-input',function(){

                var service_id = $(this).attr('service_id');
                var team_id = $(this).attr('team_id');

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
