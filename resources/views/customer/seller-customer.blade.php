@extends('layouts.app')
@section('content-title')
  Seller's Customer
@endsection
@push('style')
  <style>
  </style>
@endpush
@section('content')
<div class="table-responsive">
    <form>
    <div class="form-group col-md-12 row">
        <div class="col-md-4">
            <label for="seller_id">Seller</label>
            <select name="" id="seller_id" class="form-control form-control-sm">
              @foreach($sellers as $seller)
                <option team_id="{{ $seller->user_team }}" value="{{ $seller->user_id }}">{{ $seller->user_lastname." ".$seller->user_firstname."( ".$seller->user_nickname." )" }}</option>
              @endforeach
            </select>
        </div>
        <div class="col-2 " style="position: relative;">
            <div style="position: absolute;top: 50%;" class="">
            <input type="button" class="btn btn-primary btn-sm" id="search-button" value="Search">
            <input type="button" class="btn btn-secondary btn-sm" id="formReset" value="Reset">
            </div>
        </div>
    </div>
    </form>
    <hr>
  <table class="table table-sm table-hover" id="dataTableAllCustomer" width="100%" cellspacing="0">
    <thead>
        <tr class="sticky-top bg-primary text-white"  style="z-index: 9">
          <th>ID</th>
          <th>Business</th>
          <th>Contact Name</th>
          <th>Business Phone</th>
          <th>Cell Phone</th>
          <th>Status</th>
        </tr>
    </thead>
</table>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {
    $("#created_at").datepicker({});
    var table = $('#dataTableAllCustomer').DataTable({
      // dom: "lifrtp ",
      // order:[[7,'desc']],
      responsive:false,
      serverSide: true,
      processing: true,
      buttons: [
       ],
       ajax:{ url:"{{ route('seller_customer_datatable') }}",
        type: 'POST',
       data: function (d) {
          d.seller_id = $("#seller_id").val();
          d.team_id = $("#seller_id :selected").attr('team_id');
          d._token = '{{ csrf_token() }}';
            }
        },
        columnDefs: [ {'targets': 0, 'searchable': false} ],
       columns: [

                { data: 'id', name: 'id',class:'text-center w-10' },
                { data: 'business', name: 'business' },
                { data: 'contact_name', name: 'contact_name'},
                { data: 'business_phone', name: 'business_phone' ,class:'text-center'},
                { data: 'cell_phone', name: 'cell_phone',class:'text-center' },
                { data: 'status', name: 'status',class:'text-center' }
        ],
    });
   
     $("#formReset").click(function () {
         $(this).parents('form')[0].reset();
         table.draw();
     });

    $("#search-button").click(function(){
      table.draw();
    });
   
     $('#dataTableAllCustomer tbody').on('click', '.details-control', function () {

         var customer_template_id = $(this).attr('id');
         $(this).toggleClass('fa-plus-circle fa-minus-circle');
         var tr = $(this).closest('tr');
         var row = table.row( tr );
         var team_id = $("#seller_id :selected").attr('team_id');

         if ( row.child.isShown() ) {
             // This row is already open - close it
             row.child.hide();
             tr.removeClass('shown');
         }else{
             $.ajax({
                 url: '{{route('get-place-customer')}}',
                 type: 'GET',
                 dataType: 'html',
                 data: {
                     customer_template_id: customer_template_id,
                     team_id: team_id
                 },
             })
                 .done(function(data) {
                     data = JSON.parse(data);
                     console.log(data);
                     var subtask_html = "";
                     $.each(data, function(index,val){

                         if(val.get_user.length  != 0) var user_manage = val.get_user.user_nickname;
                         else var user_manage = "";

                         subtask_html += `
                                <tr>
                                    <td>`+val.get_place.place_name+`</td>
                                    <td>`+val.get_place.place_phone+`</td>
                                    <td>`+val.get_place.place_ip_license+`</td>
                                    <td>`+user_manage+`</td>
                                    <td class="text-center">
                                         <a class="btn btn-sm btn-secondary move-place"
                                            user_id="`+val.get_user.user_id+`"
                                            place_name="`+val.get_place.place_name+`"
                                            place_id="`+val.get_place.place_id+`"
                                            customer_id="`+val.customer_id+`" href="javascript:void(0)" title="Move Place To User">
                                            <i class="fas fa-exchange-alt"></i>
                                         </a>
                                    </td>
                                </tr> `;
                     });
                     row.child(format(row.data()) +subtask_html+"</table>" ).show();
                     tr.addClass('shown');
                 })
                 .fail(function() {
                     toastr.error('Get SubTask Failed!');
                 });
         }
     } );
     function format ( d ) {
         // `d` is the original data object for the row
         return `<table class="border border-info table-striped table table-border bg-white">
            <tr class="bg-info text-white">
                <th scope="col">Name</th>
                <th scope="col">Phone</th>
                <th>Liences</th>
                <th>User Manager</th>
                <th class="text-center">Action</th>
            </tr>`;
     }
     $(document).on('click',".move-place",function(){
         var place_name = $(this).attr('place_name');
         var place_id = $(this).attr('place_id');
         var customer_id = $(this).attr('customer_id');
         var user_id = $(this).attr('user_id');
         $("#place_id_hidden").val(place_id);
         $("#customer_id_hidden").val(customer_id);
         $("#current_user").val(user_id);
         $("#place_name").val(place_name);
         $("#move-place-modal").modal('show');

         //GET USER'S TEAM
         var team_id = $("#team_id :selected").val();
         $.ajax({
             url: '{{route('get_user_form_team')}}',
             type: 'GET',
             dataType: 'html',
             data: {
                 team_id: team_id,
                 user_id: user_id
             },
         })
             .done(function(data) {

                 data = JSON.parse(data);
                 console.log(data);
                 if(data.status == 'error')
                     toastr.error(data.message);
                 else{
                     option_html = '';
                     $.each(data.user_list,function(ind,val){
                         option_html += `<option value="`+val.user_id+`">`+val.user_nickname+`(`+val.user_firstname+val.user_lastname+`)</option>`;
                     });
                     $("#user_id").html(option_html);
                 }
             })
             .fail(function() {
                 console.log("error");
             });
     });
     $(".move-place-submit").click(function(){
         var formData = new FormData($(this).parents('form')[0]);
         formData.append('_token','{{csrf_token()}}');
         formData.append('team_id',$("#team_id :selected").val());

         $.ajax({
             url: '{{route('move_place')}}',
             type: 'POST',
             dataType: 'html',
             processData: false,
             contentType: false,
             data: formData,
         })
             .done(function(data) {
                 data = JSON.parse(data);
                 if(data.status == 'error')
                     toastr.error(data.message);
                 else{
                     toastr.success(data.message);
                     cleanModalPlace();
                 }
             })
             .fail(function() {
                 console.log("error");
             });
     });
     function cleanModalPlace(){
         $("#form-place")[0].reset();
         $("#move-place-modal").modal('hide');
         table.ajax.reload(null, false);
     }
     $(".cancel-move").click(function () {
         cleanModalPlace();
     });
     $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
     $(document).on("keypress","#business_phone,#ct_cell_phone,#ct_business_phone",function() {
       let number_phone = $(this).val();

       if(number_phone.length >9)
        return false;
     });
});
</script>
@endpush

