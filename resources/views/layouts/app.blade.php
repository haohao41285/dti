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
    var OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "d11fe280-bd65-4897-90b8-22835655a341", /*Đây là app ID của các bạn họ */
            autoRegister: true,
            requiresUserPrivacyConsent: false,
            /* Phần này là hiện cái nút thông báo góc phải ko thích có hể xóa*/
            // notifyButton: {
            //     enable: true, /* Required to use the Subscription Bell */
            //     size: 'medium', /* One of 'small', 'medium', or 'large' */
            //     theme: 'default', /* One of 'default' (red-white) or 'inverse" (white-red) */
            //     prenotify: true, /* Show an icon with 1 unread message for first-time site visitors */
            //     showCredit: false, /* Hide the OneSignal logo */
            //     text: {
            //         'tip.state.unsubscribed': 'Bấm vào đây để nhận thông báo từ hệ thống',
            //         'tip.state.subscribed': "Bạn đã đăng ký nhận thông báo",
            //         'tip.state.blocked': "Bạn đã khoá thông báo",
            //         'message.prenotify': 'Bấm vào đây để nhận thông báo từ hệ thống',
            //         'message.action.subscribed': "Cảm ơn bạn đã xác nhận!",
            //         'message.action.resubscribed': "Bạn đã đăng ký nhận thông báo",
            //         'message.action.unsubscribed': "Bạn sẽ không nhận được thông báo nữa",
            //         'dialog.main.title': 'Quản lý thông báo trang web',
            //         'dialog.main.button.subscribe': 'ĐĂNG KÝ',
            //         'dialog.main.button.unsubscribe': 'HUỶ ĐĂNG KÝ',
            //         'dialog.blocked.title': 'Mở khoá thông báo',
            //         'dialog.blocked.message': "Làm theo các hướng dẫn này để cho phép thông báo:"
            //     }
            // }
        });
        /* Đoạn phí dưới này là gửi thông tincủa user lên cho OneSignal */
        OneSignal.sendTags({
            user_id: '{{\Illuminate\Support\Facades\Auth::user()->user_id}}',  /* Gửi id của user */
            role: '{{\Illuminate\Support\Facades\Auth::user()->user_group_id}}',  /* Gửi role của user */
            {{--sub_role: '{{Auth::user()->sub_role}}', /* Gửi sub_role của user cái này mình viết quyền trưởng phòng */--}}
            name: '{{\Illuminate\Support\Facades\Auth::user()->user_nickname}}', /* Gửi user của user */
        });
        // OneSignal.getTags(function(tags) {
        //     console.log(tags);
        // });
        // OneSignal.on('notificationDisplay', function(event) {
        //     console.warn('OneSignal notification displayed:', event);
        // });

    });
    // OneSignal.isPushNotificationsEnabled(function(isEnabled) {
    //     if (isEnabled) {
    //         // user has subscribed
    //         OneSignal.getUserId( function(userId) {
    //             console.log('player_id of the subscribed user is : ' + userId);
    //             // Make a POST call to your server with the user ID
    //         });
    //     }
    // });
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
