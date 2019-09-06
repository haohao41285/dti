@extends('layouts.app')
@section('title')
Role List
@stop
@section('content')
<h5><b>Role List</b></h5>
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
  <thead>
    <tr>
      <th class="text-center">ID</th>
      <th>Service/Combo Name</th>
      <th class="text-left">Price</th>
      <th>Expiry Period (month)</th>
      <th>Service Name</th>
      <th>Description</th>
      <th class="text-center">Type</th>
      <th class="text-center">Status</th>
      <th class="text-center">Action</th>
    </tr>
  </thead>
</table>
@stop
@push('scripts')
<script type="text/javascript">
  //DEFINE VAR
  var gu_id = 0;
  $(document).ready(function($) {
    dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: true,
      buttons: [
            ],
          ajax:{ url:"{{route('service-datatable')}}"},
                columns:[
                  {data:'id', name:'id'},
                  {data:'cs_name', name:'cs_name'},
                  {data:'cs_price', name:'cs_price',class: 'text-right'},
                  {data:'cs_expiry_period', name:'cs_expiry_period',class: 'text-center'},
                  {data:'cs_service_id', name:'cs_service_id'},
                  {data:'cs_description', name:'cs_description'},
                  {data:'cs_type', name:'cs_type',class: 'text-center'},
                  {data:'cs_status', name:'cs_status',class:'text-center'},
                  {data:'action', name:'action',orderable: false, searchable: false,class:'text-center'},
                ],
                fnDrawCallback:function (oSettings) {
                    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                    elems.forEach(function (html) {
                        var switchery = new Switchery(html, {
                            color: '#0874e8',
                            className : 'switchery switchery-small'                
                        });
                    });
                }
    });

    $(document).on('click','.switchery',function(){

      var cs_id = $(this).siblings('input').attr('cs_id');
      var cs_status = $(this).siblings('input').attr('cs_status');
      // clearView();

      $.ajax({
        url: '{{route('change-status-cs')}}',
        type: 'GET',
        dataType: 'html',
        data: {
          gu_status: gu_status,
          gu_id: gu_id
        },
      })
      .done(function(data) {
        if(data != ""){
          data = JSON.parse(data);
          if(data.message != ""){
            alert(data.message);
          }
        }
        dataTable.draw();
      })
      .fail(function(data) {
        data = JSON.parse(data.responseText);
        alert(data.message);
        dataTable.draw();
      });
      
    });
    $('#dataTable tbody').on( 'click', 'tr', function () {

        $("#gu_name").val(dataTable.row(this).data()['gu_name']);
        $("#gu_descript").val(dataTable.row(this).data()['gu_descript']);
        $(".role-tip").text("Edit Role");
        gu_id = dataTable.row(this).data()['gu_id'];

      });
      $(document).on('click','.submit-role',function(){

        var gu_descript = $("#gu_descript").val();
        var gu_name = $("#gu_name").val();

        if(gu_descript != "" && gu_name != ""){
          $.ajax({
            url: '{{route('add-role')}}',
            type: 'GET',
            dataType: 'html',
            data: {
              gu_descript: gu_descript,
              gu_name: gu_name,
              gu_id: gu_id
            },
          })
          .done(function(data) {
            console.log(data);
            if(data == 0){
              alert('Error!');
            }else{
                clearView();
              dataTable.draw();
            }
            console.log(data);
          })
        .fail(function(xhr, ajaxOptions, thrownError) {
                  alert('Error!');
                  console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            });
        }
      });
      $(".cancel-role").click(function(){
        clearView();
      })
      function clearView(){
        $(".role-tip").text("Add Role");
      $("#gu_descript").val("");
      $("#gu_name").val("");
      gu_id = 0;
      }
  });
</script>
@endpush