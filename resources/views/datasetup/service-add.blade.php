@extends('layouts.app')
@section('content-title')
    Create new Service
@endsection
@section('content')

<form class="sb-form" action="servicedetails.php">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="form-group form-inline">
                <label class="col-sm-4 required">Name</label>
                <div class="col-sm-8">            
                    <input type='text' value=""  class="form-control form-control-sm" required/> 
                </div>               
            </div>           
            <div class="form-group form-inline">
                <label class="col-sm-4 required">Parent Service</label>
                 <div class="col-sm-8">            
                     <select class="form-control form-control-sm">
                         <option value=""> -- None --</option>
                         <option value="1"> Service Name 1</option>
                     </select>            
                 </div>
            </div>    
            <div class="form-group form-inline">
                <label class="col-sm-4">Price</label>
                <div class="col-sm-8 input-group">
                    <div class="input-group-prepend">
                     <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                    <input name="cash" type="number"  class="form-control form-control-sm" value="">
                </div>                   
            </div>
            <div class="form-group form-inline">
                <label class="col-sm-4">Slogan</label>
                <div class="col-sm-8">            
                    <input type='text' value=""  class="form-control form-control-sm"/> 
                </div>
                  
            </div>
            <div class="form-group form-inline">
                <label class="col-sm-4">Description</label>
                <div class="col-sm-8">            
                   <textarea type='text' value=""  class="form-control form-control-sm">

                   </textarea>
                </div>
            </div>
            <div class="form-group form-inline">
                <label class="col-sm-4">Status</label>
                <div class="col-sm-8">
                   <input type="checkbox" class="js-switch" checked="checked" />
                </div>
            </div>
           
        </div>
        <div class="col-sm-12 col-md-6">
            <table class="table table-sm display" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th class="text-left">Service Detail</th>
                        <th class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=0;$i<=25;$i++){?>
                    <tr>
                        <td class="text-center"></td>
                        <td>service detail <?php print $i;?></td>
                        <td class="text-right">$25</td>
                    </tr>
                    <?php } ?>                    
                    
                </tbody>
            </table>
        </div>
    </div>    
    <div class="row form-group pt-2">       
        <label class="col-md-2">&nbsp;</label>
        <div class="col-md-6">
            <input class="btn btn-sm btn-primary" value="Submit" type="submit">   
            <input class="btn btn-sm btn-secondary" value="Cancel" type="button">   
        </div>      

    </div>
</form>    
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
    
@endsection