<form class="sb-form">
<div class="row form-group">
    <div class="col-md form-inline">
        <label class="col-md-4" for="imageUpload3">Logo</label>
        <div class="col-md-8 logo-upload-container">
            <div class="catalog-image-upload">
                <div class="catalog-image-edit">
                    <input type='file' id="imageUpload3" data-target="#catalogImagePreview3" accept=".png, .jpg, .jpeg" />
                    <label for="imageUploadLogo"></label>
                </div>
                <div class="catalog-image-preview">
                    <img id="catalogImagePreview3" src="" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-md form-inline">
        <label class="col-md-4" for="imageUploadFavicon">Favicon</label>
        <div class="col-md-8 logo-upload-container">
            <div class="catalog-image-upload">
                <div class="catalog-image-edit">
                    <input type='file' id="imageUploadFavicon" data-target="#catalogImagePreview3" accept=".png, .jpg, .jpeg" />
                    <label for="imageUpload3"></label>
                </div>
                <div class="catalog-image-preview">
                    <img id="catalogImagePreview3" src="" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md form-inline">
        <label class="col-md-4">Business name</label>
        <div class="col-md-8">
            <input type='text' name="name" value=""  class="form-control form-control-sm" />    
        </div>
    </div>
    <div class="col-md form-inline">
        <label class="col-sm-4">Address</label>
        <div class="col-md-8">
            <input type='text' name="address" value=""  class="form-control form-control-sm" />    
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md form-inline">
        <label class="col-sm-4">Tax code</label>
       <div class="col-md-8">
           <input type='text' name="tax_code" value=""  class="form-control form-control-sm" />    
        </div>
    </div>
    <div class="col-md form-inline">
        <label class="col-sm-4">Email</label>
        <div class="col-md-8">
            <input type='email' name="email" value=""  class="form-control form-control-sm" /> 
        </div>
    </div>
</div>
<div class="row form-group">
    <div class="col-md form-inline">
        <label class="col-sm-4">Business phone</label>
        <div class="col-sm-8 input-group input-group-sm">
             <div class="input-group-prepend">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     1
                </button> 
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">1</a>
                    <a class="dropdown-item" href="#">84</a>
                    <a class="dropdown-item" href="#">61</a>                        
                    <input type="hidden" name="country_code" id="country_code" value="1">
                 </div> 
            </div>   
             <input class="form-control form-control-sm maskphone" placeholder="" value="" name="user_phone" type="text" data-inputmask="'mask' : '(999) 999-9999'" >                    
        </div>
    </div>
    <div class="col-md form-inline">
        <label class="col-sm-4">Website</label>
        <div class="col-sm-8">
            <input type='text' name="website" value=""  class="form-control form-control-sm" /> 
        </div>
    </div>
</div>
<div class="row form-group">
    <div class="col-md form-inline">
        <label class="col-sm-4">Price floor</label>
       <div class="col-sm-8 input-group">
            <div class="input-group-prepend">
             <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
            </div>
            <input name="cash" type="number"  class="form-control form-control-sm" value="" required>
        </div>
    </div>
    <div class="col-md form-inline">
        <label class="col-sm-4">Interest</label>
        <div class="col-sm-8 input-group">
            <div class="input-group-prepend">
             <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
            </div>  
           <input name="cash" type="number" class="form-control form-control-sm" value="" required>
        </div>
    </div>
</div>
<div class="row form-group">
    <div class="col-md form-inline">
        <label class="col-sm-4">Datetime option</label>
       <div class="col-sm-8">
           <input type='text' name="tax_code" value=""  class="form-control form-control-sm" />    
        </div>
    </div>
    <div class="col-md form-inline">
        <label class="col-sm-4">Last long address</label>
        <div class="col-sm-8">
            <input type='text' name="tax_code" value=""  class="form-control form-control-sm" />    
        </div>
    </div>
</div>

<div class="row form-group col-md-12">
    <label class="col-sm-2" style="margin-top:10px;">Working Day</label>
    <div class="col-sm-10 workingtime">                    
        <?php $weekday = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']; ?>
        <?php foreach( $weekday as $day): ?>
          <div class="col-day">  
            <label><?php print ucfirst($day) ?></label>
            <div class="btn-group btn-group-toggle working-day" data-toggle="buttons">
                <label class="btn btn-sm btn-day active">
                    <input name="work_<?php print $day ?>" value="1" type="radio" checked="checked" rel="<?php print $day ?>"> Open
                </label>
                <label class="btn btn-sm btn-day">
                  <input name="work_<?php print $day ?>" value="0" type="radio" rel="<?php print $day ?>"> Close
                </label>
            </div>
        </div>
        <?php endforeach; ?>
    </div>   
</div>   
<div class="row form-group col-md-12">
    <label class="col-sm-2 col-md-2">Time Start</label>
    <div class="col-sm-10 workingtime">
       <?php foreach( $weekday as $day): ?>
        <div class="col-day input-group day_<?php print $day ?>">
            <input type='text' name="time_start_<?php print $day ?>" value="08:00 AM"  class="form-control form-control-sm timepicker"/>
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-clock"></i></span>
             </div>   
        </div>
        <?php endforeach; ?>      
    </div>   
</div>   
<div class="row form-group col-md-12">
    <label class="col-sm-2">Time End</label>
    <div class="col-sm-10 workingtime">
       <?php foreach( $weekday as $day): ?>
        <div class="col-day input-group day_<?php print $day ?>">
            <input type='text' name="time_end_<?php print $day ?>" value="08:00 AM"  class="form-control form-control-sm timepicker"/>
             <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-clock"></i></span>
             </div>
        </div>
        <?php endforeach; ?>      
    </div>   
    
</div>
<div class="row form-group col-md-12">
    <label class="col-sm-2">Description</label>
    <div class="col-sm-10" >
        <textarea rows="4" class="form-control" placeholder="description" name="description"></textarea>
    </div>
</div>
<div class="row form-group col-md-12">
    <label class="col-sm-2">&nbsp;</label>
    <div class="col-sm-10" >
         <input class="btn btn-sm btn-primary" value="Save changes" type="button">
    </div>
</div>    

</form>    