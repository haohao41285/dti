@extends('layouts.app')
@section('content-title')
  SETUP SERVICE
@stop
@push('styles')
    <style>
        .required:after {
            content:"*";
            color:red;
        }
    </style>
@endpush
@section('content')
<div class="col-12">
<h5><b>Combo/Service List</b></h5>
<table class="table table-sm table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
  <thead>
    <tr class="thead-light">
      <th class="text-center">ID</th>
      <th>Name</th>
      <th class="text-center">Type</th>
      <th class="text-left">Price</th>
      <th>Expiry</th>
      <th>Work Term</th>
      <th>Service Name</th>
      <th>Description</th>
      <th>Assign To</th>
      <th class="text-center">Status</th>
      <th class="text-center">Action</th>
    </tr>
  </thead>
</table>
</div>
{{-- MODAL FOR ADD/EDIT COMBO,SERVICE --}}
<div class="modal fade" id="add-edit-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
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
  var cs_assign_id = [];

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
                  {data:'id', name:'id',class:'text-center'},
                  {data:'cs_name', name:'cs_name'},
                  {data:'cs_combo_service_type', name:'cs_combo_service_type',class: 'text-center'},
                  {data:'cs_price', name:'cs_price',class: 'text-right'},
                  {data:'cs_expiry_period', name:'cs_expiry_period',class: 'text-center'},
                  {data:'cs_work_term', name:'cs_work_term',class: 'text-center'},
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

    function clearView(){
      cs_assign_to = 0;
      $("#body-add-edit").html("");
      $('#add-edit-modal').modal('hide');
    }
    $(document).on('click','.edit-cs',function(){

      var cs_id = $(this).attr('cs_id');
      var cs_type = $(this).attr('cs_type');
      var cs_name = $(this).attr('cs_name');
      var cs_price = $(this).attr('cs_price');
      var cs_description = $(this).attr('cs_description');
      var cs_form_type = $(this).attr('cs_form_type');
      var cs_combo_service_type = $(this).attr('cs_combo_service_type');
      var cs_expiry_period = $(this).attr('cs_expiry_period');
      var cs_type_time = $(this).attr('cs_type_time');
      // cs_assign_id = $(this).attr('cs_assign_id').split(';');
      cs_assign_id = $(this).attr('cs_assign_id');
      var cs_type_time_term = $(this).attr('cs_type_time_term');
      var cs_work_term = $(this).attr('cs_work_term');

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
          var assign_html = "";
          var selected = "";
          var service_form = "";
          var service_type_htm = "";

          //GET ASSIGN LIST
          $.each(data.teams, function(index, val) {
              if(cs_assign_id === val.id) var selected = "selected";
            assign_html += `<option `+selected+` value="`+val.id+`">`+val.team_name+`</option>`;
          });

          //TIME TYPE ARRAY
          let time_type_html = "";
          let time_type_arr = <?php echo json_encode(getTimeType()); ?>;
          $.each(time_type_arr, function(index, val) {
            let checked_time = "";
            if(index == cs_type_time) checked_time = "selected";
            time_type_html += `<option `+checked_time+`  value="`+index+`">`+val+`</option>`;
          });
           //TIME TYPE TERM ARRAY
          let time_type_term_html = "";
          $.each(time_type_arr, function(index, val) {
            let checked_time = "";
            if(index == cs_type_time_term) checked_time = "selected";
            time_type_term_html += `<option `+checked_time+`  value="`+index+`">`+val+`</option>`;
          });
          
          //GET FORM SERVICE LIST
          $.each(data.service_form,function (ind,value) {
            if(cs_form_type == parseInt(ind)+1) var selected_form = 'selected';
            else var selected_form = '';

            service_form += `<option `+selected_form+` value="`+ind+`">`+value+`</option>`;
          });
          //GET COMBO SERVICE TYPE LIST
            $.each(data.combo_service_type_list,function (ind,value) {
                if(cs_combo_service_type == parseInt(ind)+1) var selected_type = 'selected';
                else var selected_type = "";

                service_type_htm += `<option `+selected_type+` value="`+value['id']+`">`+value['name']+`</option>`;
            });
            // console.log(data.combo_service_type_list);

          if(cs_type == 1){
            $.each(serivece_list_all, function(index, val)
            {
              var id = ''+val['id'];
              let checked = "";

              if(service_arr.includes(id))
                checked = "checked";
              service_list_html += `<div class="checkbox">
                    <label><input type="checkbox" `+checked+` name="cs_service_id[]" class="service_id"  style="height: 20px;width: 20px" value="`+val['id']+`">`+val['cs_name']+`</label>
                </div>`;

            });
            content_body_html = `
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                          <h6 for="cs_name" ><b class="required">Combo Name</b></h6>
                            <input type="hidden" name="cs_type" value="1" >
                            <input type="hidden" name="cs_id" value="`+cs_id+`">
                          <input type="text" name="cs_name" cs_id="`+cs_id+`" cs_type="`+cs_type+`"  class="form-control form-control-sm  cs_name" value="`+cs_name+`" placeholder="">
                        </div>
                        <div class="form-group">
                          <h6><b class="required">Price</b></h6>
                          <input type="text" onkeypress="return isNumberKey(event)" name="cs_price"  class="form-control form-control-sm cs_price" value="`+cs_price+`" placeholder="">
                        </div>
                        <div class="form-group">
                        <h6><b>Combo Service type</b></h6>
                        <select name="cs_combo_service_type" id="cs_combo_service_type" class="form-control form-control-sm">
                          <option value="">Choose Service Type</option>
                            `+service_type_htm+`
                         </select>
                       </div>
                       <div class="form-group">
                      <h6><b>Description</b></h6>
                      <textarea name="cs_description" rows="3" class="form-control form-control-sm cs_description">`+cs_description+`</textarea>
                    </div>
                    </div>
                    <div class="col-md-6">
                        <h6><b class="required">Service List</b></h6>
                        `+service_list_html+`
                        <div class="form-group row float-right">
                          <button type="button" class="btn btn-danger btn-sm cancel-add-edit">Cancel</button>
                          <button type="button" class="btn btn-primary btn-sm ml-2 submit-add-edit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
            `;
          }else
          content_body_html = `
            <form>
                <div class="col-md-12 row">
                    <div class="col-md-6">
                        <div class="form-group">
                          <h6 for="cs_name"><b class="required">Service Name</b></h6>
                           <input type="hidden" name="cs_type" value="2" >
                          <input type="hidden" name="cs_id" value="`+cs_id+`">
                          <input type="text" name="cs_name" cs_id="`+cs_id+`" cs_type="`+cs_type+`"  class="form-control form-control-sm cs_name" value="`+cs_name+`" placeholder="">
                        </div>
                        <div class="form-group">
                          <h6><b class="required">Price</b></h6>
                          <input type="text" onkeypress="return isNumberKey(event)" name="cs_price"  class="form-control form-control-sm cs_price" value="`+cs_price+`" placeholder="">
                        </div>
                        <div class="form-group">
                          <h6><b class="required">Expire Period</b></h6>
                          <div class="input-group">
                            <input type="text" onkeypress="return isNumberKey(event)" name="cs_expiry_period"  class="form-control form-control-sm" value="`+cs_expiry_period+`" placeholder="">
                            <div class="input-group-append">
                              <select name="cs_type_time" class="form-control form-control-sm">
                              `+time_type_html+`
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                          <h6><b class="required">Work Term</b></h6>
                          <div class="input-group">
                            <input type="text" onkeypress="return isNumberKey(event)" name="cs_work_term"  class="form-control form-control-sm" value="`+cs_work_term+`" placeholder="">
                            <div class="input-group-append">
                              <select name="cs_type_time_term" class="form-control form-control-sm">
                               `+time_type_term_html+`
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                            <h6><b>Combo Service type</b></h6>
                            <select name="cs_combo_service_type" id="cs_combo_service_type" class="form-control form-control-sm">
                                `+service_type_htm+`
                             </select>
                       </div>
                        <div class="form-group">
                          <h6><b>Service Form</b></h6>
                          <select name="cs_form_type" id="cs_form_type" class="form-control form-control-sm">
                            `+service_form+`
                          </select>
                        </div>
                        <div class="form-group">
                          <h6><b>Assign To Team</b></h6>
                          <select name="cs_assign_to" id="assign_to" class="form-control form-control-sm">
                            `+assign_html+`
                          </select>
                        </div>
                        <div class="form-group">
                          <h6><b>Description</b></h6>
                          <textarea name="cs_description" rows="3" class="form-control form-control-sm cs_description">`+cs_description+`</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                    <h6><b>Menu List</b></h6>
                    <ul class="nav nav-tabs app-website-box" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#home">Website</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#menu1">iNailSo App</a>
                      </li>
                    </ul>
                    <div class="tab-content" style="max-height: 33em;overflow-y: auto" id="cs-box">
                      <div id="home" class="container tab-pane active"><br>
                        `+data.menu_website_html+`
                      </div>
                      <div id="menu1" class="container tab-pane fade"><br>
                        `+data.menu_app_html+`
                      </div>
                    </div>

                    <div class="form-group row float-right">
                      <button type="button" class="btn btn-danger btn-sm cancel-add-edit">Cancel</button>
                      <button type="button" class="btn btn-primary btn-sm ml-2 submit-add-edit">Submit</button>
                    </div>
                </div>
                 </div>
            </form>
            `;
            $("#body-add-edit").html(content_body_html);
            $('#add-edit-modal').modal('show');
        }
      })
      .fail(function() {
        toastr.error("Error!");
      });
    });
    $(document).on('click','.submit-add-edit',function(){
        var formData = new FormData($(this).parents('form')[0]);
        formData.append('_token','{{csrf_token()}}');

      $.ajax({
        url: '{{route('save-service-combo')}}',
        type: 'POST',
        dataType: 'html',
        data: formData,
          cache: false,
          contentType: false,
          processData: false,
      })
      .done(function(data) {
        data = JSON.parse(data);

        if(data.status == 'error'){
          if($.type(data.message) == 'string')
            toastr.error(data.message);
          else{
            $.each(data.message, function(index, val) {
                toastr.error(val);
              });
          }
        }
        else{
          toastr.success(data.message);
          clearView();
          dataTable.ajax.reload( null, false );        }
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

      var cs_type = 2;
      getCs(cs_type);

    });
    function getCs(cs_type){
      var cs_type = 2;
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
          var cs_list = data.cs_list;
          var content_body_html = "";
          var service_list_html = "";
          var user_html = "";
          var combo_service_html = "";

          $.each(data.teams, function(index, val) {
            if(cs_assign_id == val.id) var selected = "selected";
            user_html += `<option `+selected+` value="`+val.id+`">`+val.team_name+`</option>`;
          });
            $.each(data.cs_combo_service_type, function(index, val) {
                // if(cs_assign_id == val.user_id) var selected = "selected";
                combo_service_html += `<option  value="`+val.id+`">`+val.name+`</option>`;
            });

          if(cs_type == 2){
            $.each(cs_list, function(index, val)
            {
              service_list_html += `<div class="checkbox">
                    <label><input type="checkbox" name="cs_service_id[]" class="service_id"  style="height: 20px;width: 20px" value="`+val['id']+`">`+val['cs_name']+`</label>
                </div>`;
            });
            content_body_html = `
             <form>
          <div class="row">
            <div class="col-md-6">
                <input type="hidden" value="`+cs_id+`" name="cs_id">
                <div class="form-group">
                    <h6><b class="required">Type</b></h6>
                    <select name="cs_type" required class="form-control-sm form-control cs_type">
                      <option value="1">Combo</option>
                      <option selected value="2">Service</option>
                    </select>
                </div>
                <div class="form-group">
                    <h6><b class="required">Combo Service Kind Of</b></h6>
                    <select name="cs_combo_service_type" required class="form-control-sm form-control">
                      `+combo_service_html+`
                    </select>
                </div>
                <div class="form-group">
                  <h6><b class="required">Name</b></h6>
                  <input type="text" name="cs_name" required cs_id="`+cs_id+`" cs_type="`+cs_type+`"  class="form-control form-control-sm cs_name" value="" placeholder="">
                </div>
                <div class="form-group service-content">
                  <h6><b class="required">Price</b></h6>
                  <input type="text" onkeypress="return isNumberKey(event)" name="cs_price" required class="form-control form-control-sm cs_price" value="" placeholder="">
                </div>
                <div class="form-group service-content-son">
                    <h6><b class="required">Expire Period</b></h6>

                    <div class="input-group">
                      <input type="text" onkeypress="return isNumberKey(event)" name="cs_expiry_period" required class="form-control form-control-sm" value="" placeholder="">
                      <div class="input-group-append">
                         <select name="cs_type_time" class="form-control form-control-sm">
                          @foreach(getTimeType() as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                </div>
                <div class="form-group service-content-son">
                    <h6><b class="required">Work Term</b></h6>

                    <div class="input-group">
                      <input type="text" onkeypress="return isNumberKey(event)" name="cs_work_term" required class="form-control form-control-sm" value="" placeholder="">
                      <div class="input-group-append">
                         <select name="cs_type_time_term" class="form-control form-control-sm">
                          @foreach(getTimeType() as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                </div>
               
               <div class="form-group service-content-son" >
                <h6><b>Service Form</b></h6>
                <select name="cs_form_type" id="cs_form_type" class="form-control form-control-sm">
                  @foreach(getFormService() as $key => $form)
            <option value="{{$key}}">{{$form}}</option>
                  @endforeach
            </select>
          </div>
          <div class="form-group service-content-son">
            <h6><b class="required">Assign To Team</b></h6>
            <select name="cs_assign_to" id="assign_to" class="form-control form-control-sm">
              `+user_html+`
            </select>
          </div>
            <div class="form-group">
          <h6><b>Description</b></h6>
          <textarea name="cs_description" rows="3" class="form-control form-control-sm cs_description"></textarea>
        </div>
        </div>
        <div class="col-md-6">
            <h6><b> List</b></h6>
            <ul class="nav nav-tabs app-website-box" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#home">Website</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#menu1">iNailSo App</a>
              </li>
            </ul>
          <div class="tab-content" style="max-height: 33em;overflow-y: auto" id="cs-box">
            <div id="home" class="container tab-pane active"><br>
              `+data.menu_website_html+`
            </div>
            <div id="menu1" class="container tab-pane fade"><br>
              `+data.menu_app_html+`
            </div>
          </div>
           
                <div class="form-group row float-right">
                  <button type="button" class="btn btn-danger btn-sm cancel-add-edit">Cancel</button>
                  <button type="button" class="btn btn-primary btn-sm ml-2 submit-add-edit">Submit</button>
                </div>
            </div>
            <div>
            </form>
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
      // $(".cs_name").attr('cs_type',cs_type);
      $.ajax({
        url: '{{route('get-cs')}}',
        type: 'GET',
        dataType: 'html',
        data: {cs_type: cs_type},
      })
      .done(function(data) {
        data = JSON.parse(data);
        // console.log(data);
        if(data.status == 'error')
          toastr.error(data.message);
        else{
          var cs_list = data.cs_list;
          var content_body_html = "";
          var service_list_html = "";
          var user_html = "";
          console.log(data.teams);

            $.each(data.teams, function(index, val) {
                let selected = '';
                // if(cs_assign_id == val.user_id) selected = "selected";
                user_html += `<option `+selected+` value="`+val.id+`">`+val.team_name+`</option>`;
            });

          if(cs_type == 1){//COMBO
            $(".app-website-box").css('display', 'none');

            $.each(cs_list, function(index, val)
            {
              service_list_html += `<div class="checkbox">
                    <label><input type="checkbox" name="cs_service_id[]" class="service_id"  style="height: 20px;width: 20px" value="`+val['id']+`"> `+val['cs_name']+`</label>
                </div>`;
            });
            $(".service-content-son").html('');

          }else{//SERVICE
              $(".app-website-box").css('display', '');

              $(".service-content").after(`

              <div class="form-group service-content-son">
                    <h6><b class="required">Expire Period(month)</b></h6>
                  <div class="input-group">
                      <input type="text" onkeypress="return isNumberKey(event)" name="cs_expiry_period" required class="form-control form-control-sm" value="" placeholder="">
                      <div class="input-group-append">
                         <select name="cs_type_time" class="form-control form-control-sm">
                          @foreach(getTimeType() as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
              </div>
              <div class="form-group service-content-son">
                    <h6><b class="required">Work Term</b></h6>

                    <div class="input-group">
                      <input type="text" onkeypress="return isNumberKey(event)" name="cs_work_term" required class="form-control form-control-sm" value="" placeholder="">
                      <div class="input-group-append">
                         <select name="cs_type_time_term" class="form-control form-control-sm">
                          @foreach(getTimeType() as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                </div>
               <div class="form-group service-content-son" >
                <h6><b>Service Form</b></h6>
                <select name="cs_form_type" id="cs_form_type" class="form-control form-control-sm">
                  @foreach(getFormService() as $key => $form)
                  <option value="{{$key}}">{{$form}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group service-content-son">
                <h6><b class="required">Assign To Team</b></h6>
                <select name="cs_assign_to" id="assign_to" class="form-control form-control-sm">
                  `+user_html+`
                </select>
              </div>

              `);
              service_list_html = `
              <div id="home" class="container tab-pane active"><br>
                `+data.menu_website_html+`
              </div>
              <div id="menu1" class="container tab-pane fade"><br>
                `+data.menu_app_html+`
              </div>
              `;


          }
          $("#cs-box").html(service_list_html);
        }
      })
      .fail(function() {
        console.log("error");
      });
    });
    $(document).on('click','.type-app',function(){

      $(".type-app").removeClass('btn-primary').removeClass('btn-default').addClass('btn-default');
      $(this).addClass('btn-primary').removeClass('btn-default').removeClass('btn-default');

      let type_app = $(this).attr('type-app');
      cs_app_website_type = type_app;
      $("#type-app-website").val(type_app);

      $.ajax({
        url: '{{route('get_menu_app')}}',
        type: 'GET',
        dataType: 'html',
        data: {type_app: type_app },
      })
      .done(function(data) {
        data = JSON.parse(data);
        $("#cs-box").html(data.menu_html);
        console.log(data.menu_html);
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    });
    $(document).on('click','.delete-cs',function(){

      let cs_id = $(this).attr('cs_id');

      if(confirm('Do you want to delete this combo/service?')){
        $.ajax({
          url: '{{ route('delete_service') }}',
          type: 'POST',
          dataType: 'html',
          data: {
            cs_id: cs_id,
            _token: '{{ csrf_token() }}'
          },
        })
        .done(function(data) {
          data = JSON.parse(data);
          if(data.status === 'error')
            toastr.error(data.message);
          else{
            toastr.success(data.message);
            dataTable.ajax.reload(null,false);
          }
        })
        .fail(function() {
          toastr.error('Failed!');
        });

      }else
        return false;
    });
  });
</script>
@endpush
