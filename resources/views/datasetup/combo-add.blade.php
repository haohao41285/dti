@extends('layouts.app')
@section('content-title')
    Create new Combo/Package
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
                <label class="col-sm-4">Price</label>
                <div class="col-sm-8 input-group">
                    <div class="input-group-prepend">
                     <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                    <input name="cash" type="number"  class="form-control form-control-sm" value="">
                </div>                   
            </div>
            <div class="form-group form-inline">
                <label class="col-sm-4">Combo Type</label>
                <div class="col-sm-8">            
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customRadio" name="example" value="customEx" checked="checked">
                        <label class="custom-control-label" for="customRadio">SMS</label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customRadio2" name="example" value="customEx">
                        <label class="custom-control-label" for="customRadio2">WEB</label>
                      </div> 
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
                   <div class="col-sm-8">            
                      <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" class="custom-control-input" id="customRadio" name="example" value="customEx" checked="checked">
                        <label class="custom-control-label" for="customRadio">Active</label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="customRadio2" name="example" value="customEx">
                        <label class="custom-control-label" for="customRadio2">Inactive</label>
                      </div> 
                </div>
                </div>
            </div>
            <div class="form-group form-inline">
                <h5 class="col-sm-12 margin-0">List selected services</h5>
                <div class="col-sm-12">
                <table class="table table-sm display" width="100%">
                    <thead>
                        <tr>
                            <th class="text-left">Service</th>
                            <th class="text-right">Price</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                      <tbody>
                        
                        <tr>
                            <td>Service</td>
                            <td class="text-right">$25</td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>    
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <table class="table table-sm display" id="dataTable" width="100%">
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
    </div>    
    <div class="row form-group pt-2">       
        <label class="col-md-4">&nbsp;</label>
        <div class="col-md-8">
            <input class="btn btn-sm btn-primary" value="Save changes" type="submit">   
            <input class="btn btn-sm btn-secondary" value="Cancel" type="button">   
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