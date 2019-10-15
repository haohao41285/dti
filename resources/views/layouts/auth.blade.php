@php
	$image = \App\Models\MainLoginBackground::all()->random()->image;
	$backgroundUrl = env('PATH_VIEW_IMAGE').$image;
@endphp
<!DOCTYPE html>
<html lang="en">
@section('htmlhead')
@include('layouts.partials.htmlhead')
@show
<style>
    .bg-image{
        background: url({{$backgroundUrl}});
        background-position: center;
        background-size: cover;
    }
</style>
<body class="bg-gradient-primary">
    <div  id="wrapper" class="container">
        @yield('content')
    </div>

</div><!-- ./wrapper -->
@section('scripts')
@include('layouts.partials.scripts')
@show
</body>
</html>
