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
