@extends('layouts.app')
@section('content-title')
    Generate Licenses
@endsection
@section('content')

<form class="sb-form" action="servicedetails.php">
    <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-sm-4 required">User Sale</label>
            <div class="col-sm-8">            
                  <select class="form-control form-control-sm">
                         <option value=""> -- None --</option>
                         <option value="1"> Service Name 1</option>
                  </select>   
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-md-7 form-inline">
            <label class="col-sm-4">&nbsp;</label>
            <div class="col-sm-8">            
                <input class="btn btn-sm btn-primary" value="Generate" type="submit">   
            <input class="btn btn-sm btn-secondary" value="Cancel" type="button">   
            </div>
        </div>    
    </div> 
    
</form>  
    
@endsection

@push('scripts')
  
<script type="text/javascript">
 $(document).ready(function() {
    var table = $('#dataTable').DataTable({
        buttons: [],        
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
    });
     $('#dataTable tbody').on( 'click', 'tr', function () {
         console.log(this);
        $(this).toggleClass('selected');
    } );
});
</script>
@endpush
