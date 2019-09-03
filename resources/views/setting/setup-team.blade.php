@extends('layouts.app')
@section('content-title')
Setup Team
@endsection
@push('styles')
<style>
	.scroll{
        display:block;
        padding:5px;
        margin-top:5px;
        width:300px;
        height:50px;
        overflow:auto;
    }
   .scroll thead th {
       background: #f8f9fc;
	   position: sticky;
    	 top: 0;
	}
	.scroll .table tbody tr:hover{
			background: #4e73df;
			color: #fff;
	}
</style>
@endpush
@section('content')
<div class="container-fluid ">
   <div class="row">
      <div class="col-md-8 row" style="height: 70vh">
         {{-- <div class=" "> --}}
               {{-- <div style=""> --}}
               	<div class="col-12 table-responsive scroll" style="height: 50%">                 
                     <table class="table table-bordered" id="" width="100%" cellspacing="0">
                        <thead>
                           <th>Team name</th>
                           <th>Leader</th>
                           <th>Team type</th>
                           <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           @for ($i = 0; $i < 10; $i++)
                           <tr>
                              <td>Team Sale Website</td>
                              <td>Khoa</td>
                              <td>Website, App</td>
                              <td><a class="btn btn-sm btn-secondary edit" href="http://localhost:8000/customer/edit"><i class="fas fa-edit"></i></a></td>
                           </tr>
                           @endfor
                        </tbody>
                     </table>               
               </div>

				<div class="col-12 table-responsive scroll" style="height: 50%">                  
                     <table class="table table-bordered" id="" width="100%" cellspacing="0">
                        <thead>
                           <th>Member</th>                           
                           </tr>
                        </thead>
                        <tbody>
                        	@for ($i = 0; $i < 20; $i++)
                        		<tr> 
                        		<td>Team Sale Website</td>
                        	    </tr>
                        	@endfor                           
                        </tbody>
                     </table>              
        		</div>
       
      </div>
      <div class="col-md-4 row" style="height: 70vh">
          <div class="col-12 table-responsive scroll" style="height: 100%">
             <table class="table table-bordered" id="" width="100%" cellspacing="0">
                <thead>
                   <th>Member</th>
                    <th></th>
                   </tr>
                </thead>
                <tbody>
                	@for ($i = 0; $i < 20; $i++)
                		<tr>
                      <td>Team Sale Website</td>       
                      <td><a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-trash"></i></a></td>                   
                   </tr>
                	@endfor
                   
                </tbody>
             </table>
          </div>
      </div>
   </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
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
        <button type="button" class="btn btn-primary btn-sm">Save changes</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection
@push('scripts')
  <script>
    $(document).ready(function(){

      $(document).on('click','.edit',function(e){
        e.preventDefault();
        $("#editModal").modal("show");
      });
    // --
    });
  </script>
@endpush