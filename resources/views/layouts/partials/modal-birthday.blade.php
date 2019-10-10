
<div class="modal fade" id="birthday-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="text-danger" style="font-size: 20px">Happy Birthday</span> to<br>
                @foreach($data['user_info'] as $user_info)
                    @if($user_info['id'] == \Auth::user()->user_id)
                        @if(count($data['user_info']) == 1)
                            <span class="text-info" style="font-size: 20px">You</span>
                        @else
                            <span class="text-info" style="font-size: 20px">You</span> and
                        @endif
                    @endif
                @endforeach
                @foreach($data['user_info'] as $user_info)
                    @if($user_info['id'] != \Auth::user()->user_id)
                        <span class="text-capitalize text-info">{{$user_info['fullname']}}</span>({{$user_info['nickname']}}),
                    @endif
                @endforeach
                <hr>
                <img src="{{asset('images/'.$data['image_birthday'])}}" width="100%" alt="">
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-info confirm-birthday">
                    @php $count = 0; @endphp
                    @foreach($data['user_info'] as $user_info)
                        @if($user_info['id'] == \Auth::user()->user_id)
                            @php $count++; @endphp
                        @endif
                    @endforeach
                    @if($count == 0)
                        SEEN!
                    @else
                        THANK YOU!
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>
