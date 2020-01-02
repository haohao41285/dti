<div class="modal fade" id="social-edit-modal">
  <div class="modal-dialog modal-lg" style="width: 100%">
    <div class="modal-content">
    
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Social Network</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div class='x_panel x_panel_form'>
          <div class="x_content">
              <form action="" id="social_form" class="form-horizontal form-label-left" enctype="multipart/form-data">    
                @csrf
              <div class="row col-md-12 social_list">
                
             </div>
             <div class="col-md-6 col-sm-5 col-xs-12 float-right">
               <button class="btn btn-sm btn-primary float-right ml-2 social-save" type="button" >Save</button>
               <button class="btn btn-sm btn-danger float-right social-cancel" type="button">Cancel</button>
             </div>
              </form>
          </div>        
      </div>
      </div>
    </div>
  </div>
</div>



      