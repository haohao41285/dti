<div class="modal fade" id="add-cate-modal">
  <div class="modal-dialog modal-lg" style="width: 100%">
    <div class="modal-content">
    
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add CateService</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div class='x_panel x_panel_form'>
          <div class="x_content">
              <form method="post" action="" id="cateservice_form" class="form-horizontal form-label-left" enctype="multipart/form-data">    
                @csrf 
                <input type="hidden" name="cateservice_id" value="" />                 
                  <div class="row">
                     <label class="control-label col-md-2 col-sm-2 col-xs-12">Name</label>
                     <div class="col-md-9 col-sm-9 col-xs-12">
                       <input type='text' class="form-control form-control-sm" id="cateservice_name" value="" required name="cateservice_name" />
                     </div>
                   </div>
                   <div class="row">
                     <label class="control-label col-md-2 col-sm-2 col-xs-12">Index</label>
                     <div class="col-md-9 col-sm-9 col-xs-12">
                         <input type='text' onkeypress="return isNumberKey(event)" class="form-control form-control-sm" id="cateservice_index" value="" name="cateservice_index" />
                     </div>
                   </div>   
                   <div id="collapseonlinebooking" class="onlinebooking">
                      <div class="row" style="padding-bottom:10px;">
                       <label class="control-label col-md-2 col-sm-2 col-xs-12">Image</label>
                       <div class="col-md-9 col-sm-9 col-xs-12" style="overflow: hidden;">
                          <div class="catalog-image-upload">
                                 <div class="catalog-image-edit">
                                    <input type="hidden" name="cateservice_image_old" value="">
                                     <input type='file' class="cateservice_image" name="cateservice_image" data-target="#catalogImagePreview1" accept=".png, .jpg, .jpeg" />
                                     <label for="cateservice_image"></label>
                                 </div>
                                 <div class="catalog-image-preview">
                                     <img id="catalogImagePreview1" style='' src ="" height ="100%" /> 
                                 </div>

                          </div>
                       </div>
                     </div>  

                      <div class="row" style="padding-bottom:10px;">
                       <label class="control-label col-md-2 col-sm-2 col-xs-12">Icon Image</label>
                       <div class="col-md-9 col-sm-9 col-xs-12" style="overflow: hidden;">
                          <div class="catalog-image-upload">
                                 <div class="catalog-image-edit">
                                    <input type="hidden" name="cateservice_icon_image_old" value="">
                                     <input type='file' class="cateservice_image" name="cateservice_icon_image" data-target="#catalogImagePreview2" accept=".png, .jpg, .jpeg" />
                                     <label for="cateservice_image"></label>
                                 </div>
                                 <div class="catalog-image-preview">
                                     <img id="catalogImagePreview2" style='display:' src ="" height ="100%" /> 
                                            
                                 </div>

                          </div>
                       </div>
                     </div>   

                       <div class="row" style="padding-bottom:10px;">
                       <label class="control-label col-md-2 col-sm-2 col-xs-12">Description</label>
                       <div class="col-md-9 col-sm-9 col-xs-12">
                         <textarea id="message"  class="form-control summernote" name="cateservice_description" data-parsley-trigger="keyup" data-parsley-minlength="20" data-parsley-maxlength="100" data-parsley-minlength-message="Come on! You need to enter at least a 20 caracters long comment.."
                                data-parsley-validation-threshold="10"></textarea>
                       </div>
                     </div>   
                   </div>   

                   <div class="row">
                      <label class="control-label col-md-2 col-sm-2 col-xs-12">&nbsp;</label>
                      <div class="col-sm-6 col-md-6  form-group">
                         <button id="submit" class="btn btn-sm btn-primary" >SUBMIT</button>
                         <button class="btn btn-sm btn-danger cancel-add" type="button">CANCEL</button>
                      </div>            
                  </div>  
              </form>
          </div>        
      </div>
      </div>
    </div>
  </div>
</div>



      