@extends('layouts.app')
@section('content-title')
    Move Customers
@endsection
@section('styles')

@endsection
@section('content')
    <div class="">
            <div class="form-group col-md-12">
                <div class="form-inline">
                    <label for="team_type_id"><b>Choose Team</b></label>
                    <select name="team_type_id" id="team_type_id" class="form-control form-control-sm col-md-2">
                        @foreach($team_type_list as $team_type)
                            <option value="{{$team_type->id}}">{{$team_type->team_type_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div class="form-inline">
                    <label for="user_1"><b>From Staff</b></label>
                    <select name="user_1" id="user_1" class="form-control form-control-sm col-md-4">
                        <option value=""></option>
                    </select>
                        <button class="btn btn-sm btn-primary position-absolute move-customer" style="right:10px"><i class="fas fa-exchange-alt"></i>Move</button>

                </div>
                <hr>
                <table class="table table-striped table-hover" id="customer_user_1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Business</th>
                            <th>Contact Name</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="col-md-6">
                <div class="form-inline">
                    <label for="user_2"><b>To Staff</b></label>
                    <select name="user_2" id="user_2" class="form-control form-control-sm col-md-4">
                        <option value=""></option>
                    </select>
                </div>
                <hr>
                <table class="table table-striped table-hover" id="customer_user_2" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Business</th>
                            <th>Contact Name</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            beginSetup();
            var user_1 = 0;
            var user_2 = 0;
            var customer_array = [];

            function beginSetup(){
                var team_type_id = $("#team_type_id :selected").val();
                $.ajax({
                    url: '{{route('get-user-team')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {
                        team_type_id:team_type_id
                    },
                })
                .done(function(data) {
                    data = JSON.parse(data);

                    if(data.status == 'success'){
                        var user_list_html = "";
                        $.each(data.user_list,function (ind,val) {
                            user_list_html += `
                                <option value="`+val['user_id']+`">`+val['user_nickname']+`(`+val['user_firstname']+val['user_lastname']+`)</option>
                            `;
                            if(ind === 0){
                                user_1 = val['user_id'];
                                user_2 = val['user_id'];
                                table_user_1.draw();
                                table_user_2.draw();
                            }
                        });
                        $("#user_1").html(user_list_html);
                        $("#user_2").html(user_list_html);
                    }
                    else
                        toastr.error(data.message);
                })
                .fail(function() {
                    console.log("error");
                });
            }
            $("#team_type_id").change(function(){
                beginSetup();
            });
            var table_user_1 = $('#customer_user_1').DataTable({
                // dom: "lBfrtip",
                buttons: [
                ],
                processing: true,
                serverSide: true,
                ajax:{ url:"{{ route('get_customer_1') }}",
                    data: function (d) {
                        d.user_1 = user_1;
                    }
                },
                columns: [

                    { data: 'id', name: 'id',class:'text-center' },
                    { data: 'ct_salon_name', name: 'ct_salon_name' },
                    { data: 'ct_fullname', name: 'ct_fullname'},
                ],
            });
            var table_user_2 = $('#customer_user_2').DataTable({
                // dom: "lBfrtip",
                buttons: [
                ],
                processing: true,
                serverSide: true,
                ajax:{ url:"{{ route('get_customer_2') }}",
                    data: function (d) {
                        d.user_2 = user_2;
                    }
                },
                columns: [

                    { data: 'id', name: 'id',class:'text-center' },
                    { data: 'ct_salon_name', name: 'ct_salon_name' },
                    { data: 'ct_fullname', name: 'ct_fullname'},
                ],
            });
            $("#user_1").change(function () {
                user_1 = $("#user_1 :selected").val();
                table_user_1.draw();
            });
            $("#user_2").change(function () {
                user_2 = $("#user_2 :selected").val();
                table_user_2.draw();
            });
            $(".move-customer").click(function(e){

                if(customer_array.length === 0){
                    toastr.error('Selec Customer!');
                    return;
                }
                if(user_1 === user_2){
                    toastr.error('Failed! Choose Other Staff!');
                    return;
                }
                $.ajax({
                    url: '{{route('move-customer-all')}}',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        user_1: user_1,
                        user_2: user_2,
                        customer_array:customer_array,
                        _token:'{{csrf_token()}}'
                    },
                })
                    .done(function(data) {
                        console.log(data);
                        data = JSON.parse(data);
                        if(data.status == 'success'){
                            toastr.success(data.message);
                        }
                        else
                            toastr.error(data.message);

                        table_user_1.draw();
                        table_user_2.draw();
                        customer_array = [];
                    })
                    .fail(function() {
                        console.log("error");
                    });

            });
            $('#customer_user_1 tbody').on( 'click', 'tr', function () {

                $(this).toggleClass('selected');
                var customer_id = table_user_1.rows(this).data()[0]['id'];

                if($.inArray(customer_id,customer_array) !== -1){
                    customer_array = $.grep(customer_array, function(value) {
                        return value != customer_id;
                    });
                }else{
                    customer_array.push(customer_id);
;                }
            } );
        });
    </script>

@endpush

