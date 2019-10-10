<html lang="en">
@section('htmlhead')
  @include('layouts.partials.htmlhead')
@show
<body id="app">
{{--check confirm view event holiday if exist--}}

@if(Cookie::get('event') != 'confirm')
@if(!empty($data['event_info']))
    <div class="modal fade" id="event-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="text-danger" style="font-size: 30px">3 ngày</span> nữa sẽ đến
                   <span class="text-danger" style="font-size: 30px">{{$data['event_info']['name']}}</span>
                    <hr>
                    <img src="{{asset($data['event_info']['image'])}}" width="100%" alt="">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-info confirm-event">OK, KHÔNG HIỂN THỊ LẠI</button>
                </div>
            </div>
        </div>
    </div>
    </div>
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
    @if(cookie('event'))
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
</body>
</html>
