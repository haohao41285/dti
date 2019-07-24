<!DOCTYPE html>
<html lang="en">
@section('htmlhead')    
@include('layouts.partials.htmlhead')    
@show
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
