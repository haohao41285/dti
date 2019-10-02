@extends('layouts.app')
@section('content-title')
    Places
@endsection
@section('content')

<div class="col-12 ">
    <div class="card shadow mb-4 ">
        <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-primary">Places List </h6>
        </div>
        <div class="card-body">
        <div class="table-responsive">
              <table class="table table-bordered" id="places-datatable" width="100%" cellspacing="0">
                  <thead> 
                        <tr>              
                          <th>ID</th>
                          <th>Name</th>
                          <th>Address</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>License</th>
                          <th>Created Date</th>
                          <th>Action</th>
                      </tr>
                  </thead>
              </table>
          </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Users</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-sm btn btn-primary">Save changes</button>
        <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {

    var table = $('#places-datatable').DataTable({
         // dom: "lfrtip",    
         processing: true,
         serverSide: true,
         ajax:{ url:"{{ route('getPlacesDatatable') }}" },
         columns: [

                  { data: 'place_id', name: 'place_id',class:'text-center' },
                  { data: 'place_name', name: 'place_name' },
                  { data: 'place_address', name: 'place_address'},
                  { data: 'place_email', name: 'place_email' },
                  { data: 'place_phone', name: 'place_phone',class:'text-center' },
                  { data: 'place_ip_license', name: 'place_ip_license' },
                  { data: 'created_at', name: 'created_at' ,class:'text-center'},                
                  { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
          ], 
          buttons: [

              ],      
    });

    $(document).on('click','.view',function(e){
      e.preventDefault();
      $("#view").modal("show");
    });

});

 
</script>
@endpush

