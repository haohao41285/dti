@extends('layouts.app')
@section('title')
    Reviews Report
@endsection
@section('style')
<style type="text/css" media="screen">
    .today-review{
        position: absolute;
        top: 0;
        right: 0;
    }
</style>
@endsection
@section('content')
@php
    $current_month = today()->format('m');
    $current_year = today()->format('Y');
    $count = 1;
    $month = $current_month;
    $year = $current_year;
    $change = 0;
@endphp
    <div class="table-responsive">
        <h4 class="border border-info border-top-0 border-right-0 border-left-0 text-info">REVIEWS REPORT</h4>
        <form id="customer_form">
            <div class="form-group col-md-12 row">
                <div class="col-md-6">
                    <label for="">Choose Time <span style="color: black">(Month)</span></label>
                    <div class="row">
                        @while($count < 13)
                        @php
                            if($change == 1){ $year = $current_year-1; }
                        @endphp
                            <button class="btn btn-sm float-right  ml-1 time {{ $month==$current_month?"btn-primary border-danger":"border-primary" }}" style="width: 36px;height: 36px" id="month-{{ $month }}" year="{{ $year }}" type="button" value="{{ $month }}">{{ $month }}</button>
                            @php
                                $month--; $count++;
                                
                                if($month == 0){ $month = 12; $change = 1;}
                            @endphp
                        @endwhile
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">User</label>
                    <select id="user_id" name="team_id" class="form-control form-control-sm">
                            <option value="">--All--</option>
                        @foreach ($user_list as $key =>  $user)
                            <option value="{{$user->user_id}}">{{$user->getFullname()."(".$user->user_nickname.")"}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2 " style="position: relative;">
                    <div style="position: absolute;top: 50%;" class="">
                        <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
                        <input type="button" class="btn btn-secondary btn-sm" id="reset-btn" value="Reset">
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-sm table-boreder table-hover" id="dataTableReviews" width="100%" cellspacing="0">
            <thead>
                <tr class="thead-light">
                    <th>ID</th>
                    <th>User</th>
                    <th>Total Reviews</th>
                    <th>Successfully Total</th>
                    <th>Failed Total</th>
                    <th>%Complete</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            var current_month = '{{ $current_month }}';
            var current_year = '{{ $current_year }}';
            var team_id = $("#team_id").val();

            var table = $('#dataTableReviews').DataTable({
                // dom: "lBfrtip",
                // order:[[6,"desc"]],
                processing: true,
                serverSide: true,
                buttons: [
                   /* {
                        text: '<i class="fas fa-upload"></i> Export',
                        className: "btn-sm",
                        action: function ( e, dt, node, config ) {
                            document.location.href = ""+"/"+team_id
                        }
                    }*/
                ],
                ajax:{ url:"{{ route('report.reviews.datatable') }}",
                    data: function (d) {
                        d.current_month = current_month;
                        d.current_year = current_year;
                        d.user_id = $("#user_id :selected").val();
                    }
                },
                columns: [

                    { data: 'id', name: 'id',class:'text-center' },
                    { data: 'user', name: 'user' },
                    { data: 'total_reviews', name: 'total_reviews',class:'text-right'},
                    { data: 'successfully_total', name: 'successfully_total' ,class:'text-right'},
                    { data: 'failed_total', name: 'failed_total',class:'text-right' },
                    { data: 'percent_complete', name: 'percent_complete',class:'text-right' },
                ],
            });

            $("#search-button").click(function(){
                table.draw();
            });
        
            $("#reset-btn").on('click',function(e){
                $(this).parents('form')[0].reset();
                $(".time").removeClass('btn-primary');
                current_month = '{{ $current_month }}';
                current_year = '{{ $current_year }}';
                $('#month-'+current_month).removeClass('btn-primary').addClass('btn-primary');
                table.ajax.reload(null, false);
            });
            $(".time").click(function(){
                $(".time").removeClass('btn-primary');
                $(this).removeClass('btn-primary').addClass('btn-primary');

                current_month = $(this).val();
                current_year = $(this).attr('year');
            });
            
            function format ( d ) {
            // `d` is the original data object for the row
            return `
            <table class="border border-info table-striped table table-border bg-white">
                <tr class="bg-info text-white">
                    <th scope="col" class="text-center">Today</th>
                    <th scope="col" class="text-right">Successfully Total</th>
                    <th class="text-right">Failed Total</th>
                </tr>
            `;
            }
            $('#dataTableReviews tbody').on('click','.user', function () {

                var user_id = $(this).attr('id');
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }else{
                    $.ajax({
                        url: '{{route('report.reviews.review_today')}}',
                        type: 'GET',
                        dataType: 'html',
                        data: {
                            user_id: user_id,
                        },
                    })
                        .done(function(data) {
                            data = JSON.parse(data);
                            console.log(data);
                            // return;
                            var subtask_html = "";

                            subtask_html += `
                            <tr>
                                <td class="text-center">`+data.today+`</td>
                                <td class="text-right text-danger"><b>`+data.successfully_total+`</b></td>
                                <td class="text-right text-danger"><b>`+data.failed_total+`</b></td>
                            </tr> `;

                            row.child(format(row.data()) +subtask_html+"</table>" ).show();
                            tr.addClass('shown');
                        })
                        .fail(function() {
                            toastr.error('Get Reviews Today Failed!');
                        });
                }
            } );
        });
    </script>
@endpush
