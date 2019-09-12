@extends('layouts.auth')

@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row" style="height: 35rem">
              <div class="col-lg-6 d-none d-lg-block bg-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                  </div>
                  <form method="POST" action="{{ route('login') }}">
                       {{ csrf_field() }}
                    <div class="form-group">
                     <input id="user_phone" type="username" class="form-control{{ $errors->has('user_phone') ? ' is-invalid' : '' }}" name="user_phone" value="{{ old('user_phone') }}" required autofocus placeholder="Enter Phone">
                    </div>
                    <div class="form-group">
                      <input id="user_password" type="password" class="form-control{{ $errors->has('user_password') ? ' is-invalid' : '' }}" name="user_password" required placeholder="Password">
                    </div>
                    {{-- <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                      </div>
                    </div> --}}
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
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                      Login
                    </button>
                    {{-- <hr> --}}
                    {{-- <a href="index.html" class="btn btn-google btn-user btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-user btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a> --}}

                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="{{ route('password.reset') }}">Forgot Password?</a>
                  </div>
                  {{-- <div class="text-center">
                    <a class="small" href="register.html">Create an Account!</a>
                  </div> --}}
                </div>
              </div>
            </div>
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

