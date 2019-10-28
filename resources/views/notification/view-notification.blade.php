@extends('layouts.app')
@section('content')
    <h4 class="border border-info border-top-0 mb-3 border-right-0 border-left-0 text-info">VIEW NOTIFICATION</h4>

    <div class="table-responsive">
        <span class="mt-3">Created By:</span>
        <b><span class="text-capitalize">{{$notification_info->getCreatedBy->user_lastname.$notification_info->getCreatedBy->user_firstname}}</span>({{$notification_info->getCreatedBy->user_nickname.")"}}</b><br>
        <span class="mt-3">Created at:</span>
        <b>{{format_datetime($notification_info->created_at)}}</b><br>
        <span class="mt-3">Receiver:</span>
        <b><span class="text-capitalize">{{$notification_info->getReceive->user_lastname.$notification_info->getReceive->user_firstname}}</span>({{$notification_info->getReceive->user_nickname.")"}}</b><br>
        <span class="mt-3">Content:</span>
        <div class="ml-5">{!!$notification_info->content!!}</div>

    </div>

@endsection
@push('scripts')
@endpush

