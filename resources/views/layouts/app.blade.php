<!DOCTYPE html>
<html lang="en">
@section('htmlhead') 
  @include('layouts.partials.htmlhead')
@show
<body id="app">
<div id="wrapper">    
    @include('layouts.partials.sidebar')
    <!-- Content Wrapper. Contains page content -->
        <div id="content-wrapper" class="d-flex flex-column">
        <!-- content -->
        <div id="content">
            @include('layouts.partials.header')
            <!-- Begin Page Content -->
            <div class="container-fluid">
              <!-- Page Heading -->
              <h1 class="h3 mb-4 text-gray-800">@yield('content-title')</h1>
              @yield('content')
            </div>
            <!-- /.container-fluid -->                
            
        </div><!-- /.content -->
    </div><!-- /.content-wrapper -->    
    @include('layouts.partials.footer')
</div><!-- ./wrapper -->
@section('scripts')
    @include('layouts.partials.scripts')
@show
</body>
</html>
