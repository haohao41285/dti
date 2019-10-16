@extends('layouts.app')
@section('title','DTI - Dashboard')
@section('content-title')
    Dashboard
@endsection
@section('content')
    <a href="{{route('test-onesignal')}}"><button>send</button></a>

@endsection
@push('scripts')

@endpush
