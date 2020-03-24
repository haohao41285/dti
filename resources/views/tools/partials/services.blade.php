<form>
  <div class="form-group col-md-12 row">
      <div class="col-md-2">
          <label for="">Cateservice</label>
          <select id="search_service_cate" name="cateservice_id" class="form-control form-control-sm">
              <option value="">-- ALL --</option>
              @foreach ($cateservices as $key =>  $cateservice)
                <option value="{{$cateservice->cateservice_id}}">{{$cateservice->cateservice_name}}</option>
              @endforeach
          </select>
      </div>
      <div class="col-md-2">
        <label for="">Status</label>
        <select id="search_service_status" name="enable_status" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            <option value="1">Enable</option>
            <option value="0">Disable</option>
        </select>
      </div>
      <div class="col-md-2">
        <label for="">Booking</label>
        <select id="search_service_booking" name="booking_online_status" class="form-control form-control-sm">
            <option value="">-- ALL --</option>
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
      </div>
      <div class="col-2 " style="position: relative;">
          <div style="position: absolute;top: 50%;" class="">
          <input type="button" class="btn btn-primary btn-sm" id="search-service" value="Search">
          <input type="button" class="btn btn-secondary btn-sm" id="formReset" value="Reset">
          </div>
      </div>
  </div>
  </form>
<table id="services_datatable" class="table table-sm table-hover" style="width:100%">
    <thead class="thead-light">
      <tr>
        <th class="text-center"></th>
        <th class="text-center" width="10">ID </th>  
        <th width="100" >Category </th>
        <th width="100">Service Name </th>
        <th class="text-center" width="30" >Price($)</th>                
        <th class="text-center" width="30">Duration(mins)</th>
        <th class="text-center" width="30">Index</th>       
        <th class="text-center" width="30">Enable</th>     
        <th class="text-center" width="30" nowrap="nowrap">Online Booking</th>
        <th width="80" >Last Update</th>
        <th class="text-center" width="80">Action</th>        
      </tr>
    </thead>
</table>