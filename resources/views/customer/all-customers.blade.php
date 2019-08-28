@extends('layouts.app')
@section('content-title')
    Customers Management
@endsection
@section('content')
<div class="table-responsive">
    <div class="form-group col-md-12 row">
        <div class="col-md-2">
            <label for="">Created date</label>
            <input type="text" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="">City</label>
            <input type="text" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label for="">State</label>
            <select name="" id="" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($state as $element)                    
                    <option value="{{$element}}">{{$element}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="">Status</label>
            <select name="" id="" class="form-control form-control-sm">
                <option value="">-- ALL --</option>
                @foreach ($status as $element)                    
                    <option value="{{$element}}">{{$element}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-2 " style="position: relative;">
            <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="reset" value="Reset">
            </div>
        </div>      
    </div>
    <hr>
    <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
        <thead>                
                <th>Full Name</th>
                <th>Nail Shop</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
                <th>Date Expired</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        {{-- <tbody> --}}
           {{--  <tr>                
                <td>Jendy</td>
                <td>Laguna Spa & Nails Salon</td>
                <td class="text-center">19494582430</td>
                <td>dsds@gmail.com</td>
                <td class="text-center"><span class="text-gray-500">Expired</span></td>                
                <td class="text-center"><span class="text-gray-500">06-02-2019</span></td>
                <td class="text-center">06-02-2018</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-secondary" href="{{ route("editCustomer") }}"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
     
           <tr>                
                <td>Jendy</td>
                <td>Laguna Spa & Nails Salon</td>
                <td class="text-center">19494582430</td>
                <td>dsds@gmail.com</td>
                <td class="text-center">Trial</td>                
                <td class="text-center">06-12-2019</td>
                <td class="text-center">06-02-2018</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-secondary" href="{{ route("editCustomer") }}"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
         <tr>                
                <td>Jendy</td>
                <td>Laguna Spa & Nails Salon</td>
                <td class="text-center">19494582430</td>
                <td>dsds@gmail.com</td>
                <td class="text-center">Purchasing</td>                
                <td class="text-center">06-12-2019</td>
                <td class="text-center">06-02-2018</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-secondary" href="{{ route("editCustomer") }}"><i class="fas fa-edit"></i></a>
                </td>
            </tr> --}}
           {{-- </tbody>     --}}
    </table>
</div>

<!-- Modal view-->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div style="max-width: 90%" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
    var table = $('#dataTableAllCustomer').DataTable({
         // dom: "lBfrtip",
             buttons: [
                 {   
                     extend: 'csv', 
                     text: '<i class="fas fa-download"></i> Import',
                     className: "btn-sm"
                 },
                 {   
                     extend: 'csv', 
                     text: '<i class="fas fa-upload"></i> Export',
                     className: "btn-sm"
                 }
             ],  
             processing: true,
             serverSide: true,
         ajax:{ url:"{{ route('customersDatatable') }}",
         data: function (d) {

              } 
          },
         columns: [

                  { data: 'customer_fullname', name: 'customer_fullname' },
                  { data: 'customer_fullname', name: 'customer_fullname' },
                  { data: 'customer_phone', name: 'customer_phone' ,class:'text-center'},
                  { data: 'customer_email', name: 'customer_email' },
                  { data: 'customer_status', name: 'customer_status' ,class:'text-center'},
                  { data: 'customer_status', name: 'customer_status' ,class:'text-center'},
                  { data: 'customer_status', name: 'customer_status' ,class:'text-center'},                 
                  { data: 'action' , name:'action' ,orderable: false, searcheble: false ,class:'text-center'}
          ],       
    });

    $("#reset").on('click',function(e){
        e.preventDefault();
        table.ajax.reload(null, false);
    });

    $(document).on('click','.view',function(e){
        e.preventDefault();
        var id = $(this).attr('data');
        $("#viewModal").modal('show');
    })
});
</script>
@endpush

