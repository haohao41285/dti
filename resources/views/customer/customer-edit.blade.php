@extends('layouts.app')
@section('content-title')
    Edit Customer #123
@endsection
@section('content')
<nav class="nav nav-tabs md-tabs" role="tablist">
    <a href="#tab1" class="nav-item nav-link active" data-toggle="tab">
        Account Info
    </a>
    <a href="#tab3" class="nav-item nav-link" data-toggle="tab">
        Order History
    </a>
</nav>
<div id="myTabContent" class="tab-content pt-4">
   <div class="tab-pane show fade in active" id="tab1">
        @include('customer.tab-customerinfo')         
   </div>   
   <div class="tab-pane fade" id="tab3">
       @include('customer.tab-orderhistory')
   </div>
</div>
<script type="text/javascript" src="public/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>   
<script type="text/javascript">
$(document).ready(function(){
    if ($("input.timepicker")[0]) {
        $('input.timepicker').datetimepicker({            
           format: 'hh:mm A'
        });
    }
    $('.working-day input').on( "change", function(e){
        var $day = $(e.target).attr("rel");
        $(".day_"+$day).css('visibility', $(e.target).val() == 1?'visible':'hidden');        
    });
});
</script> 
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
   
});
</script>
@endpush
