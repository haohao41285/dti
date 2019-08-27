@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-header page-title text-center">
                    <img src="{{ asset("images/logo274x29.png")}}" alt="logo"/>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ url('/password/email') }}">
                       {{ csrf_field() }}
                       @if($errors->has('errorLogin'))                       
                        <div class="alert alert-danger">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                          {{$errors->first('errorLogin')}}
                        </div>
                       @endif   
                        <div class="form-group row">
                            <label for="user_phone" class="col-sm-4 col-form-label text-md-right">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input required="" type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>                               
                            </div>
                        </div>           
                        
                        <div class="form-group row mb-4">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>

                                <a class="btn btn-link" href="{{ route('login') }}">
                                    {{ __('Login') }}
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


