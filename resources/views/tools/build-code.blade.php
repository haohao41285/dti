@extends('layouts.app')
@section('content-title')
{{-- Places --}}
@endsection
@push('styles')

@endpush
@section('content')
<div class="col-12 row">
   <div class="col-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " >Website themes </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="themes-dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <th>ID</th>
                                        <th>Theme Code</th>
                                        <th>Image</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <h6 class="m-0 font-weight-bold text-primary " >Setting </h6>
                        </div>
                        <div class="card-body">
                            <form method="post" id="setting-form">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-2">License</label>
                                    <label id="get-license"><b></b></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Website</label>
                                    <input class="col-10 form-control-sm form-control" type="text" name="website">
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Branch</label>
                                    <input class="col-10 form-control-sm form-control" type="text" name="branch">
                                </div>
                                <div class="form-group row">
                                    <label class="col-2">Theme</label>
                                    <label id="get-code"><b></b></label>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2"></label>
                                    <input class="btn-sm btn btn-primary" type="submit" value="Update">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
</div>

@endsection
@push('scripts')
   <script>
      $(document).ready(function(){
         var themesTable = $('#themes-dataTable').DataTable({
                 processing: true,
                 serverSide: true,
                 ajax:{ url:"{{ route('getDatatableWebsiteThemes') }}",},
                 columns: [
                      { data: 'theme_id', name: 'theme_id' ,class:"id"},
                      { data: 'theme_name_temp', name: 'theme_name_temp' ,class:"code"},
                      { data: 'theme_image', name: 'theme_image' },
     
                ],       
                 buttons: [
    
                   ],
          });

      $("#themes-datatable tbody").on('click',"tr",function(){
             $('#themes-datatable tbody tr.selected').removeClass('selected');
             $(this).addClass('selected');
             themeId = $(this).find("td.id").text();
             var code = $(this).find("td.code").text();
             $("label#get-code b").text(code);
       });
      });
   </script>
@endpush