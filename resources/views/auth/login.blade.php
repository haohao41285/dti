@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-header page-title text-center">
                    <img src="{{ asset("images/logo274x29.png")}}" alt="logo"/>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                       {{ csrf_field() }}
                       @if($errors->has('errorLogin'))                       
                        <div class="alert alert-danger">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                          {{$errors->first('errorLogin')}}
                        </div>
                       @endif   
                        <div class="form-group row">
                            <label for="user_phone" class="col-sm-4 col-form-label text-md-right">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="user_phone" type="username" class="form-control{{ $errors->has('user_phone') ? ' is-invalid' : '' }}" name="user_phone" value="{{ old('user_phone') }}" required autofocus>

                                @if ($errors->has('user_phone'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('user_phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="user_password" type="password" class="form-control{{ $errors->has('user_password') ? ' is-invalid' : '' }}" name="user_password" required>

                                @if ($errors->has('user_password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('user_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>            
                        @if (env('GOOGLE_RECAPTCHA'))
                        <div class="form-group row mb-4">
                            <div class="col-md-6 offset-md-4">
                            <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}"></div>
                            @if ($errors->has('g-recaptcha-response'))
                                <span class="invalid-feedback" style="display: block;">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        @endif
                        <div class="form-group row mb-4">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                <a class="btn btn-link" href="{{ route('password.reset') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@if (env('GOOGLE_RECAPTCHA'))
@push('scripts')
    <!-- Google reCaptcha -->
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js"  async defer></script>   
        <!-- End Google reCaptcha -->
@endpush
@endif