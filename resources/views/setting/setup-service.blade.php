@extends('layouts.app')
@section('title')
Combo/Service List
@stop
@section('content')
<h5><b>Combo/Service List</b></h5>
<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
  <thead>
    <tr>
      <th class="text-center">ID</th>
      <th>Name</th>
      <th class="text-center">Type</th>
      <th class="text-left">Price</th>
      <th>Expiry(month)</th>
      <th>Service Name</th>
      <th>Description</th>
      <th>Assign To</th>
      <th class="text-center">Status</th>
      <th class="text-center">Action</th>
    </tr>
  </thead>
</table>
{{-- MODAL FOR ADD/EDIT COMBO,SERVICE --}}
<div class="modal fade" id="add-edit-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body" id="body-add-edit">
         
      </div>
    </div>
  </div>
</div>
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
                 {   
                     text: '<i class="fas fa-plus"></i>Add New',
                     className: "btn-sm add-new-cs",
                 }
             ],
          ajax:{ url:"{{route('service-datatable')}}"},
                columns:[
                  {data:'id', name:'id'},
                  {data:'cs_name', name:'cs_name'},
                  {data:'cs_type', name:'cs_type',class: 'text-center'},
                  {data:'cs_price', name:'cs_price',class: 'text-right'},
                  {data:'cs_expiry_period', name:'cs_expiry_period',class: 'text-center'},
                  {data:'cs_service_id', name:'cs_service_id'},
                  {data:'cs_description', name:'cs_description'},
                  {data:'cs_assign_to', name:'cs_assign_to'},
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

      $.ajax({
        url: '{{route('change-status-cs')}}',
        type: 'GET',
        dataType: 'html',
        data: {
          cs_status: cs_status,
          cs_id: cs_id
        },
      })
      .done(function(data) {
        data = JSON.parse(data);
        if(data.message == "error"){
          toastr.error(data.message);
        }else{
          toastr.success(data.message);
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
    });
    function clearView(){
      $("#body-add-edit").html("");
      $('#add-edit-modal').modal('hide');
    }
    $(document).on('click','.edit-cs',function(){

      var cs_id = $(this).attr('cs_id');
      var cs_type = $(this).attr('cs_type');
      var cs_name = $(this).attr('cs_name');
      var cs_price = $(this).attr('cs_price');
      var cs_description = $(this).attr('cs_description');

      $.ajax({
        url: '{{route('get-service-combo')}}',
        type: 'GET',
        dataType: 'html',
        data: {
          cs_id: cs_id,
          cs_type: cs_type
        },
      })
      .done(function(data) {
        data = JSON.parse(data);
        if(data.status == 'error')
          toastr.error(data.message);
        else{
          var service_arr = data.service_arr;
          var serivece_list_all = data.service_list_all;
          var content_body_html = "";
          var service_list_html = "";
          var user_html = "";
          var selected = "";

          $.each(data.user, function(index, val) {
            // if(cs_assign_to == val.user_id) var selected = "selected";
            user_html += `<option `+selected+` value="`+val.user_id+`">`+val.user_nickname+`</option>`;
          });

          if(cs_type == 1){
            $.each(serivece_list_all, function(index, val)
            {
              var id = ''+val['id'];
              let checked = "";
              if(service_arr.includes(id))
                checked = "checked";
              service_list_html += `<div class="checkbox">
                    <label><input type="checkbox" `+checked+` class="service_id"  style="height: 20px;width: 20px" value="`+val['id']+`">`+val['cs_name']+`</label>
                </div>`;
            });
            content_body_html = `<div class="form-group">
              <h6 for="cs_name" ><b>Combo Name</b></h6>
              <input type="text" name="cs_name" cs_id="`+cs_id+`" cs_type="`+cs_type+`"  class="form-control form-control-sm  cs_name" value="`+cs_name+`" placeholder="">
            </div>
            <div class="form-group">
              <h6><b>Price</b></h6>
              <input type="text" name="cs_price"  class="form-control form-control-sm cs_price" value="`+cs_price+`" placeholder="">
            </div>
            <div class="form-group">
              <h6><b>Assign To</b></h6>
              <select name="" id="assign_to" class="form-control form-control-sm">
                <option value="">Choose User</option>
                `+user_html+`
              </select>
            </div>
            <div class="form-group">
              <h6><b>Description</b></h6>
              <textarea name="cs_description" rows="3" class="form-control form-control-sm cs_description">`+cs_description+`</textarea>
            </div>
            <h6><b>Service List</b></h6>
            `+service_list_html+`
            <div class="form-group row float-right">
              <button type="button" class="btn btn-danger cancel-add-edit">Cancel</button>
              <button type="button" class="btn btn-primary ml-2 submit-add-edit">Submit</button>
            </div>
            `;
          }else
          content_body_html = `<div class="form-group">
              <h6 for="cs_name"><b>Service Name</b></h6>
              <input type="text" name="cs_name" cs_id="`+cs_id+`" cs_type="`+cs_type+`"  class="form-control form-control-sm cs_name" value="`+cs_name+`" placeholder="">
            </div>
            <div class="form-group">
              <h6><b>Price</b></h6>
              <input type="text" name="cs_price"  class="form-control form-control-sm cs_price" value="`+cs_price+`" placeholder="">
            </div>
            <div class="form-group">
              <h6><b>Assign To</b></h6>
              <select name="" id="assign_to" class="form-control form-control-sm">
                <option value="">Choose User</option>
                `+user_html+`
              </select>
            </div>
            <div class="form-group">
              <h6><b>Description</b></h6>
              <textarea name="cs_description" rows="3" class="form-control form-control-sm cs_description">`+cs_description+`</textarea>
            </div>
            <h6><b>Menu List</b></h6>
            <div style="max-height:20em;overflow-y: auto;" class="scroll">
              `+data.menu_html+`
            </div>
            <div class="form-group row float-right">
              <button type="button" class="btn btn-danger cancel-add-edit">Cancel</button>
              <button type="button" class="btn btn-primary ml-2 submit-add-edit">Submit</button>
            </div>
            `;
            $("#body-add-edit").html(content_body_html);
            $('#add-edit-modal').modal('show');
        }
      })
      .fail(function() {
        console.log("error");
      });
    });
    $(document).on('click','.submit-add-edit',function(){

      var cs_name = $(".cs_name").val();
      var cs_id = $(".cs_name").attr('cs_id');
      var cs_type = $(".cs_name").attr('cs_type');
      var cs_price = $(".cs_price").val();
      var cs_description = $(".cs_description").val();
      var service_id_arr = [];

      $('.service_id:checked').each(function() {
        service_id_arr.push($(this).val());
      });
      $.ajax({
        url: '{{route('save-service-combo')}}',
        type: 'GET',
        dataType: 'html',
        data: {
          cs_id: cs_id,
          cs_price: cs_price,
          cs_name: cs_name,
          cs_type: cs_type,
          cs_description: cs_description,
          service_id_arr: service_id_arr
        },
      })
      .done(function(data) {

        data = JSON.parse(data);
        var message = '';

        if(data.status == 'error'){
          if($.type(data.message) == 'string')
            toastr.error(data.message);
          else{
            $.each(data.message, function(index, val) {
                message += val+'\n';
              });
            if(message != "")
              toastr.error(message);
          }
        }
        else{
          toastr.success(data.message);
          clearView();
          dataTable.draw();
        }
      })
      .fail(function(data) {
        toastr.error("Error!");
        dataTable.draw();
      });
    });
    $(document).on("click",".cancel-add-edit",function(){
      clearView();
    });
    $(document).on("click","input[type=checkbox]",function(){

      var menu_id = $(this).attr('id');
      var parent_id = $(this).attr('parent_id');
      var c = this.checked;

      if(parent_id == 0)
        $("."+menu_id).prop('checked',c);
      else
        $("#"+parent_id).prop('checked',c);
    });
    $(document).on("click",'.add-new-cs',function(){

      var cs_type = 1;
      getCs(cs_type);

    });
    function getCs(cs_type){
      var cs_type = 1;
      var cs_id = 0;
      $.ajax({
        url: '{{route('get-cs')}}',
        type: 'GET',
        dataType: 'html',
        data: {cs_type: cs_type},
      })
      .done(function(data) {
        data = JSON.parse(data);
        if(data.status == 'error')
          toastr.error(data.message);
        else{
          var cs_list = data;
          var content_body_html = "";
          var service_list_html = "";

          if(cs_type == 1){
            $.each(cs_list, function(index, val)
            {
              service_list_html += `<div class="checkbox">
                    <label><input type="checkbox" class="service_id"  style="height: 20px;width: 20px" value="`+val['id']+`">`+val['cs_name']+`</label>
                </div>`;
            });
            content_body_html = `
            <div class="form-group">
            <h6><b>Type</b></h6>
            <select name="" class="form-control-sm form-control cs_type">
              <option value="1">Combo</option>
              <option value="2">Service</option>
            </select>
            <div class="form-group">
              <h6><b>Name</b></h6>
              <input type="text" name="cs_name" cs_id="`+cs_id+`" cs_type="`+cs_type+`"  class="form-control form-control-sm cs_name" value="" placeholder="">
            </div>
            <div class="form-group">
              <h6><b>Price</b></h6>
              <input type="text" name="cs_price"  class="form-control form-control-sm cs_price" value="" placeholder="">
            </div>
            <div class="form-group">
              <h6><b>Assign To</b></h6>
              <select name="" class="form-control form-control-sm">
                <option value="">Nguyen Thieu</option>
              </select>
            </div>
            <div class="form-group">
              <h6><b>Description</b></h6>
              <textarea name="cs_description" rows="3" class="form-control form-control-sm cs_description"></textarea>
            </div>
            <h6><b> List</b></h6>
            <div style="max-height: 20em;overflow-y: auto" id="cs-box">
              `+service_list_html+`
            </div>
            <div class="form-group row float-right">
              <button type="button" class="btn btn-danger cancel-add-edit">Cancel</button>
              <button type="button" class="btn btn-primary ml-2 submit-add-edit">Submit</button>
            </div>
            `;
          }

          $("#body-add-edit").html(content_body_html);
          $('#add-edit-modal').modal('show');
        }
      })
      .fail(function() {
        console.log("error");
      });
    }
    $(document).on('change','.cs_type',function(){

      var cs_type = $('.cs_type :selected').val();
      var cs_id = 0;
      $.ajax({
        url: '{{route('get-cs')}}',
        type: 'GET',
        dataType: 'html',
        data: {cs_type: cs_type},
      })
      .done(function(data) {
        data = JSON.parse(data);
        if(data.status == 'error')
          toastr.error(data.message);
        else{
          var cs_list = data;
          var content_body_html = "";
          var service_list_html = "";

          if(cs_type == 1){
            $.each(cs_list, function(index, val)
            {
              service_list_html += `<div class="checkbox">
                    <label><input type="checkbox" class="service_id"  style="height: 20px;width: 20px" value="`+val['id']+`">`+val['cs_name']+`</label>
                </div>`;
            });

          }else{
              service_list_html = data.menu_html;
          }
          $("#cs-box").html(service_list_html);
        }
      })
      .fail(function() {
        console.log("error");
      });
    })
  });
</script>
@endpush