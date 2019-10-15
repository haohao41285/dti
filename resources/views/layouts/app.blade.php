<html lang="en">
@section('htmlhead')
  @include('layouts.partials.htmlhead')
@show
<body id="app">
{{--modal birthday--}}
@if(Cookie::get('birthday') != 'confirm')
@if(!empty($data['user_info']))
    @include('layouts.partials.modal-birthday')
@endif
@endif
{{--end modalbirthday--}}
{{--check confirm view event holiday if exist--}}
@if(Cookie::get('event') != 'confirm')
@if(!empty($data['event_info']))
    @include('layouts.partials.modal-event')
@endif
@endif
{{--end check--}}
<div id="wrapper">
    @include('layouts.partials.sidebar')
    <!-- Content Wrapper. Contains page content -->
        <div id="content-wrapper" class="d-flex flex-column">
        <!-- content -->
        <div id="content">
            @include('layouts.partials.header')
            <!-- Begin Page Content -->
            <div class="container-fluid">
              <!-- Page Heading -->
              <h1 class="h3 mb-4 text-gray-800">@yield('content-title')</h1>
              @yield('content')
            </div>
            <!-- /.container-fluid -->

        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->
    @include('layouts.partials.footer')
</div><!-- ./wrapper -->
@section('scripts')
    @include('layouts.partials.scripts')
@show

<script>
    $(document).ready(function () {
        $("#birthday-modal").modal('show');
    });
</script>
<script>
    @if(Cookie::get('birthday') != 'confirm')
    @if(!empty($data['user_info']))
    $(document).ready(function () {

        $("#birthday-modal").modal('show');
        $(".confirm-birthday").click(function () {
            $.ajax({
                url: '{{route('confirm-birthday')}}',
                type: 'GET',
                dataType: 'html',
            })
                .done(function(data) {
                    if(data != ""){
                        toastr.error(data);
                    }else{
                        $("#birthday-modal").modal('hide');
                    }
                    console.log(data);
                })
                .fail(function() {
                    toastr.error('Confirm Failed!');
                });
        });
    });
    @endif
    @endif
</script>
<script>
    @if(Cookie::get('event') != 'confirm')
    @if(!empty($data['event_info']))
        $(document).ready(function () {
            $("#event-modal").modal('show');
            $(".confirm-event").click(function () {
                $.ajax({
                    url: '{{route('confirm-event')}}',
                    type: 'GET',
                    dataType: 'html',
                })
                    .done(function(data) {
                        if(data != ""){
                            toastr.error(data);
                        }else{
                            $("#event-modal").modal('hide');
                        }
                        console.log(data);
                    })
                    .fail(function() {
                        toastr.error('Confirm Failed!');
                    });
            });
        });
    @endif
    @endif
</script>
<script>
    $(document).ready(function(){
        $(".search-customer").click(function(){

            var customer_phone = $("#customer_phone_search").val();

            if(customer_phone != ""){
                $.ajax({
                    url: '{{route('search-customer')}}',
                    type: 'GET',
                    dataType: 'html',
                    data: {customer_phone: customer_phone},
                })
                    .done(function(data) {

                        data = JSON.parse(data);

                        if(data.status == 'error'){

                            toastr.error(data.message);
                        }else{
                            window.location.href = "{{route('customer-detail')}}"+"/"+data.id;
                        }
                    })
                    .fail(function() {
                        toastr.error('Search Customer Failed!');
                    });
            }
        });
    });
</script>
</body>
</html>
