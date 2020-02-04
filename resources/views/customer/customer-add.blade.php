@extends('layouts.app')
@section('content-title')
    Create new Customer
@endsection
@push('styles')
    <style type="text/css" media="screen">
        .select2-container .select2-selection--single{
            height:34px !important;
        }
        .select2-container--default .select2-selection--single{
                 border: 1px solid #ccc !important; 
             border-radius: 0px !important; 
        }
        .select2-container {
            width: 100%!important;
        }
</style>
@endpush
@section('content')
    <div class="table-responsive mt-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-info" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">NEW CUSTOMER</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-info" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">NEW BUSINESS</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <form class="sb-form pt-3" action="{{route('save-my-customer')}}" method="post">
                    @csrf()
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Cell Phone</label>
                            <div class="col-sm-8">
                                <input required class="form-control form-control-sm maskphone cell_phone" placeholder="" value="{{old('ct_cell_phone')}}" name="ct_cell_phone"  type="text" id="ct_cell_phone" onkeypress="return isNumberKey(event)" data-inputmask="'mask' : '(999) 999-9999'" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">First Name</label>
                            <div class="col-sm-8">
                                <input type='text' required value="{{old('ct_firstname')}}" name="ct_firstname"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Last Name</label>
                            <div class="col-sm-8">
                                <input type='text' required value="{{old('ct_lastname')}}" name="ct_lastname"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Business Name</label>
                            <div class="col-sm-8">
                                <input type='text' required value="{{old('ct_salon_name')}}" name="ct_salon_name" class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Business Phone</label>
                            <div class="col-sm-8">
                                <input class="form-control form-control-sm maskphone" placeholder="" value="{{old('ct_business_phone')}}" name="ct_business_phone"  type="text" id="ct_business_phone" onkeypress="return isNumberKey(event)" data-inputmask="'mask' : '(999) 999-9999'" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Email</label>
                            <div class="col-sm-8">
                                <input type='email' name="ct_email" value="{{old('ct_email')}}"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4 required" >Address</label>
                            <div class="col-md-8">
                                <input type='text' name="ct_address" value="{{old('ct_address')}}"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4">Website</label>
                            <div class="col-md-8">
                                <input type='text' name="ct_website" value="{{old('ct_website')}}"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4">Note</label>
                            <div class="col-md-8">
                                <textarea name="ct_note" class="form-control form-control-sm" rows="3" value="{{old('ct_note')}}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4">&nbsp;</label>
                            <div class="col-md-8">
                                <input class="btn btn-sm btn-danger reset-btn" value="Reset" type="button">
                                <input class="btn btn-sm btn-primary" value="Submit" type="submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <form class="sb-form pt-3" action="{{route('save_my_business')}}" method="post">
                    @csrf()
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4">Customer Name</label>
                            <div class="col-sm-8">
                                <select name="customer_id" class="form-control form-control-sm text-capitalize select2" id="customer-list">
                                    @foreach($customer_list as $customer)
                                        <option class="text-capitalize" value="{{$customer->id}}">{{$customer->getFullname()." - ".$customer->ct_cell_phone}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Business Name</label>
                            <div class="col-sm-8">
                                <input type='text' required value="{{old('business_name')}}" name="business_name" class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4 required">Business Phone</label>
                            <div class="col-sm-8">
                                <input class="form-control form-control-sm maskphone" placeholder="" value="{{old('business_phone')}}" name="business_phone" id="business_phone" type="text" onkeypress="return isNumberKey(event)" data-inputmask="'mask' : '(999) 999-9999'" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-sm-4">Email</label>
                            <div class="col-sm-8">
                                <input type='email' name="email" value="{{old('email')}}"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4" >Address</label>
                            <div class="col-md-8">
                                <input type='text' name="address" value="{{old('address')}}"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4">Website</label>
                            <div class="col-md-8">
                                <input type='text' name="website" value="{{old('website')}}"  class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-7 form-inline">
                            <label class="col-md-4">&nbsp;</label>
                            <div class="col-md-8">
                                <input class="btn btn-sm btn-danger reset-btn" value="Reset" type="button">
                                <input class="btn btn-sm btn-primary" value="Submit" type="submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

 $(document).ready(function() {

    $(".reset-btn").click(function(){
         $(this).parents('form')[0].reset();
     });

    $(document).on("keypress","#business_phone,#ct_cell_phone,#ct_business_phone",function() {
       let number_phone = $(this).val();

       if(number_phone.length >9)
        return false;
    });

    $('.select2').select2();

    $('#customer-list').select2().on('select2:open', function(e){
        $('.select2-search__field').attr('placeholder', 'Search...');
    })
});
</script>
@endpush
