@php
	$backgroundUrl = "https://source.unsplash.com/oWTW-jNGl9I/600x800";
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
