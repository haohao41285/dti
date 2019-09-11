@extends('layouts.app')
@section('content-title')
    New Order
@endsection
@section('content')
<div class="">
    <form action="">
    <div class="form-group col-md-12 row">
        <div class="col-md-4">
            <label class="required">Customer phone</label>
            <div class=" input-group" >
              <input type="text" class="input-sm form-control form-control-sm"  name="customer_phone" />
            </div>
        </div>
        <div class="col-2 " style="position: relative;">
            <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="reset" value="Reset">
            </div>
        </div>  
    </div>
    <hr>
	<div class="col-12 ">
         <label class="required">Services</label>
            <table class="table table-bordered" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th class="text-left">Service</th>
                        <th class="text-left">Service Detail</th>
                        <th class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=0;$i<=25;$i++){?>
                    <tr>
                        <td class="text-center"></td>
                        <td>Service</td>
                        <td>service detail <?php print $i;?></td>
                        <td class="text-right">$25</td>
                    </tr>
                    <?php } ?>                    
                    
                </tbody>
            </table>
        </div>
    </form>	
</div>
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
         // console.log(this);
        $(this).toggleClass('selected');
    } );
});
</script>
@endpush