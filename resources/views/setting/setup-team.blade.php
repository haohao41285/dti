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
         thead th {
         background: #f8f9fc;
		 position: sticky;
		 top: 0;
		}
		.table>tbody>tr:hover{
			background: #4e73df;
			color: #fff;
		}

	</style>
@endpush
@section('content')
<div class="container-fluid ">
   <div class="row">
      <div class="col-md-8 row" style="height: 80vh">
         {{-- <div class=" "> --}}
               {{-- <div style=""> --}}
               	<div class="col-12 table-responsive scroll" style="height: 50%">                 
                     <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
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
                              <td><a class="btn btn-sm btn-secondary" href="http://localhost:8000/customer/edit"><i class="fas fa-edit"></i></a></td>
                           </tr>
                           @endfor
                        </tbody>
                     </table>               
               </div>

				<div class="col-12 table-responsive scroll" style="height: 50%">                  
                     <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
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
      <div class="col-md-4 table-responsive sroll " style="height: 80vh">
             <table class="table table-bordered" id="dataTableAllCustomer" width="100%" cellspacing="0">
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
@endsection