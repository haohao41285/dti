@if(session('success'))
    <script type="text/javascript">
        toastr.success('{{ session('success') }}');
    </script>
@endif
@if(session('error'))
    <script type="text/javascript">
        toastr.error('{{ session('error') }}');
    </script>
@endif
@if($errors->any())
    <script type="text/javascript">
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    </script>
@endif