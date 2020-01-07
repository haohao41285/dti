@extends('layouts.app')
@section('title','Edit profile')
@section('content')
<div class="">
   <!-- Page Heading -->
@section('content-title')
    Edit Profile
@endsection
   <!-- Content Row -->
   <div class="">
       <form role="form" method="post" enctype="multipart/form-data">
         @csrf
      <div class="row m-y-2">
         <div class="col-lg-4 pull-lg-8 text-center">
         {{-- <img  src="{{isset($user->user_avatar) ? env('PATH_VIEW_IMAGE').$user->user_avatar : '//placehold.it/150/'}}" class="m-x-auto img-fluid img-circle rounded-circle" loadImageFromId="avatar"/> --}}
         {{-- <h6 class="m-t-2">Upload a different photo</h6> --}}
         {{-- <label class="custom-file col-md-8"> --}}
         {{-- <span id="choose_file" class="form-control form-control-sm">Choose file</span> --}}
         {{-- <input accept="image/*" type="file" id="avatar" name="avatar" class="custom-file-input"> --}}
         <div class="previewImage">
                  <img id="previewImageAppbanner" src="{{isset($user->user_avatar) ? env('PATH_VIEW_IMAGE').$user->user_avatar : asset("images/no-image.png")}}"  />
                  <input type="file" accept="image/*" name="avatar" class="custom-file-input"  previewImageId="previewImageAppbanner" value="" style="display: none">
         </div>
         {{-- </label> --}}
         </div>
         <div class="col-lg-8 push-lg-4 personal-info">
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label">Nickname & Phone</label>
                  <div class="col-lg-6">
                     <input required="" class="form-control form-control-sm" type="text" name="user_nickname" value="{{$user->user_nickname}}" placeholder="Fullname" />
                  </div>
                  <div class="col-lg-3">
                     <input disabled="" class="form-control form-control-sm" type="text" onkeypress="return isNumberKey(event)"  value="{{$user->user_phone}}"/>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label">Email</label>
                  <div class="col-lg-9">
                     <input disabled="" class="form-control form-control-sm" type="email" value="{{$user->user_email}}" />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label">First & Last Name</label>
                  <div class="col-lg-6">
                     <input required="" class="form-control form-control-sm" type="text" value="{{$user->user_firstname}}" name="user_firstname" placeholder="First name" />
                  </div>
                  <div class="col-lg-3">
                     <input required="" class="form-control form-control-sm" type="text" value="{{$user->user_lastname}}" name="user_lastname" placeholder="Last Name" />
                  </div>
               </div>
               <div class="form-group row">
                   <label class="col-lg-3 col-form-label form-control-label" for="user_birthdate">Birthdate</label>
                   <div class="col-lg-9">
                       <input class="form-control form-control-sm" type="text" id="user_birthdate" value="{{$user->user_birthdate!==""?format_date($user->user_birthdate):""}}" name="user_birthdate" />
                   </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label">Password</label>
                  <div class="col-lg-9">
                     <input class="form-control form-control-sm" type="password" value="" name="password" />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label">New Password</label>
                  <div class="col-lg-9">
                     <input class="form-control form-control-sm" type="password" id="new_password" value="" name="new_password" />
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label">Confirm New Password</label>
                  <div class="col-lg-9">
                     <input class="form-control form-control-sm" type="password" id="confirm_password" value="" name="confirm_password" />
                      <span class="text-danger message-error"></span>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-form-label form-control-label"></label>
                  <div class="col-lg-9">
                     <input type="submit" class="btn btn-primary btn-sm" value="Save Changes" />
                     <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">Cancel</a>
                  </div>
               </div>
         </div>

         </form>
      </div>
   </div>
</div>
@endsection
@push('scripts')
<script>
   $(document).ready(function() {

       $("#user_birthdate").datepicker();

        perviewImage();

        $('#confirm_password').keyup(function () {
            let new_password = $("#new_password").val();
            let confirm_password = $("#confirm_password").val();
            let message_error = $(".message-error");

            if(new_password =! "" && confirm_password != "" && new_password === confirm_password){
                message_error.text("");
            }else
                message_error.text("Password not match!");
        })
   });
</script>

@endpush
