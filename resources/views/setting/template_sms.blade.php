@extends('layouts.app')
@section('content-title')
  SETUP TEMPLATE SMS
@endsection
@push('styles')
@endpush
@section('content')
<div class="modal fade" id="short-link-modal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="text" name="link" id="link" class="form-control form-control-sm col-md-10" placeholder="Enter Your Link">
            <button class="col-md-2 bg-primary text-white text-center" id="shorten">Shorten</button>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
        </div>
      </div>

    </div>
  </div>
<div class="col-12">
<div class="row">
  <div class="col-md-7">
    <div class="card shadow mb-4">
    <div class="card-header py-2">
      <h6 class="m-0 font-weight-bold text-primary">Template List</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <!-- <th>Id</th> -->
              <th>Template Title</th>
              <th>SMS Content Template</th>
              <th style="width: 80px">Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  </div>
  <div class="col-md-5">
    <div class="card shadow mb-4">
    <div class="card-header py-2">
      <h6 class="m-0 font-weight-bold text-primary">SMS Template</h6>
    </div>
    <div class="card-body">
      <form action="" id="sms_template_form">
      <div class="form-group row"  >
            <label class="col-lg-3 col-form-label form-control-label">Template title</label>
            <div class="col-lg-9">
               <input class="late form-control" name="template_title" id="template_title" type="text">
            </div>

        </div>
        <div class="form-group row"  >
          <label class="col-lg-3 col-form-label form-control-label">SMS Content Template</label>
            <div class="col-lg-9">
                <textarea class="form-control" name="sms_content" id="textMessage" rows="4" cols="50"></textarea>
                <span class="note"><span id="length">0</span>/160 characters</span>
            </div>
        </div>
        <div class="form-group row" >
          <label class="col-lg-3 col-form-label form-control-label">Params</label>
          <div class="col-lg-9">
            <button type="button" id="phone" class="btn btn-sm btn-primary mt-1">Phone</button>
            <button type="button" id="name" class="btn btn-sm btn-primary mt-1">Name</button>
            <button type="button" id="birthday" class="btn btn-sm btn-primary mt-1">Birthday</button>
            <button type="button" id="code" class="btn btn-sm btn-primary mt-1">Code</button>
            <button type="button" id="time1" class="btn btn-sm btn-primary mt-1">Time1</button>
            <button type="button" id="time2" class="btn btn-sm btn-primary mt-1">Time2</button>
            {{-- <button type="button" id="short-link" class="btn btn-sm btn-primary">Short Link</button> --}}
          </div>
        </div>
        <div class="form-group row" >
        </div>

         <div class="form-group row">
            <label class="col-lg-3 col-form-label form-control-label"></label>
            <div class="col-lg-9">
               {{-- <a href="" class="btn btn-sm btn-danger">Cancel</a> --}}
               <input type="button" value="Cancel" id="reset" class="btn btn-danger btn-sm" name="">
               <input type="button" class="btn btn-sm btn-primary save-change" value=" Save " />
            </div>
         </div>
      </form>
    </div>
  </div>
</div>

</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
  $(document).ready(function(){
    var template_id = 0;
    var name = "{name}";
    var phone = "{phone}";
    var birthday = "{birthday}";
    var code = "{code}";
    var time1 = "{time1}";
    var time2 = "{time2}";
    $(document).on("click","#name",function(){
      $('#textMessage').val(function(i, text) {
          return text + name;
      }).focus();
    });
    $(document).on("click","#phone",function(){
      $('#textMessage').val(function(i, text) {
          return text + phone;
      }).focus();
    });
    $(document).on("click","#birthday",function(){
      $('#textMessage').val(function(i, text) {
          return text + birthday;
      }).focus();
    });
    $(document).on("click","#code",function(){
      $('#textMessage').val(function(i, text) {
          return text + code;
      }).focus();
    });
    $(document).on("click","#time1",function(){
      $('#textMessage').val(function(i, text) {
          return text + time1;
      }).focus();
    });
    $(document).on("click","#time2",function(){
      $('#textMessage').val(function(i, text) {
          return text + time2;
      }).focus();
    });

    $("#textMessage").on("keyup",function(){
        convertSmsContentTemplate();
   });

   function convertSmsContentTemplate(){
        var MaxLength = 160;
        var length = $("#textMessage").val().length;
        $("#length").text(length);

        if(length > MaxLength){
            $("#length").text(160);
           var str = $("#textMessage").val();
           var s_str = str.substring(0,MaxLength);
           $("#textMessage").val(s_str);
        }
    }
    $("#short-link").click(function(){
      $("#short-link-modal").modal('show');
    });

    table = $("#dataTable").DataTable({
        processing:true,
        serverSide:true,
        buttons: [
        ],
        ajax:{
          url:" {{ route('sms-template-datatable') }}",
        },
        columns:[
         /* {data:'id',name: 'id', class: 'text-center'},*/
          {data:'template_title',name:'template_title'},
          {data:'sms_content_template',name:'sms_content_template'},
          {data:'action',name:'action',orderable: false, searcheble: false, class: 'text-center'},
        ]
      });

      $(document).on('click','.delete-template',function(e){
          e.preventDefault();
          clearView();
          if(confirm("Are you sure delete this template?")){
          $.ajax({
            url:"{{route('delete-template')}}",
            method:'post',
            data:{
              _token:'{{csrf_token()}}',
              template_id: template_id
            },
            success:function(data){
              if(data.status == 'success'){
                toastr.success(data.message);
                template_id = 0;
                clearView();
              }else{
                toastr.error(data.message);
              }
            },
            error:function(){
              toastr.error('Deleting Error!');
            },
          });
          } else  e.preventDefault();
        });
    function clearView(){
      $("#textMessage").val("");
      $("#template_title").val("");
      table.draw();
    }
    $("#shorten").click(function(event) {
      var link = $("#link").val();

      $.ajax({
        url: "",
        method: 'GET',
        data: {link: link},
        success:function(data){
            alert(data);
        },
        error:function(){
            toastr.error('Error short link','Error !!');
        },
      });

    });
    $("table>tbody").on('click','tr',function(){

      $('#template_title').val(table.row(this).data()['template_title']);
      $('#textMessage').val(table.row(this).data()['sms_content_template']);
      template_id = table.row(this).data()['id'];

    });
    $(document).on('click','#reset',function(){
      clearView();
      template_id = 0;

    });
    $(".save-change").on('click',function(){

      var template_title = $("#template_title").val();
      var sms_content_template = $("#textMessage").val();

      // if(template_title == "" || sms_content_template == ""){
      //   toastr.error('Enter Title, Content Template');
      //   e.preventDefault();
      // }
      $.ajax({
        url: '{{route('save-template-sms')}}',
        type: 'POST',
        dataType: 'html',
        data: {
          template_id: template_id,
          template_title: template_title,
          sms_content_template: sms_content_template,
          _token: '{{csrf_token()}}'
        },
      })
      .done(function(data) {
        data = JSON.parse(data);
        var message = "";
        if(data.status == 'success'){
          toastr.success(data.message);
          template_id = 0;
          clearView();
        }else{
          if($.type(data.message) == 'string'){
            toastr.error(data.message);
          }else{
             $.each(data.message, function(index, val) {
              message += val+'\n';
            });
            toastr.error(message);
          }
        }
      })
      .fail(function() {
        toastr.error('Saving Error!');
      });


    })

  });
</script>
@endpush
