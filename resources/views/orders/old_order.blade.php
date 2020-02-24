@extends('layouts.app')
@section('content-title')
    NEW ORDER
@endsection
@push('styles')
<style>
    .form-group {
        margin-bottom: .5rem;
    }
    .card-header{
        padding: 0.5rem 0.75rem;
    }
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
    <div class="">
    <form action="{{route('post-add-order')}}" method="post">
        @csrf()

    <div class="col-md-12 form-group row">
        <label class="col-md-2">Order By</label>
        <div class="col-md-4">
            <select name="created_by" id="created_by" class=" form-control form-control-sm">
                @foreach($user_list as $user)
                    <option {{ \Auth::user()->user_id==$user->user_id?"selected":"" }} value="{{ $user->user_id }}">{{ $user->user_lastname." ".$user->user_firstname." (".$user->user_nickname." )" }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-12 form-group row">
        <label class="col-md-2">Business</label>
        <div class="col-md-4">
            <select name="place_id" id="place_id" class="place_id form-control form-control-sm">
                @foreach($place_list as $place)
                    <option  value="{{ $place->place_id }}">{{ $place->place_name." - ".$place->place_phone }}</option>
                @endforeach
            </select>
        </div>
    </div>
     <div class="col-md-12 form-group row">
        <label class="col-md-2">Date Order</label>
        <div class="col-md-4">
            <input type="text" id="date_order" name="date_order" class="form-control form-control-sm">
        </div>
    </div>
    <hr>
	<div class="col-12 row">
        <div class="col-md-2">
            <label class="required">Services</label>
        </div>
        <div class="col-md-10">
            <div id="accordion">
                <div class="card">
                    <div class="card-header">
                        <a class="card-link" data-toggle="collapse" href="#t">
                            <div class="text-uppercase text-info">Type 1</div>
                        </a>
                    </div>
                    <div id="t" class="collapse " data-parent="#accordion">
                        <div class="card-body row">
                                <label class="col-md-6"><input style="width:20px;height: 20px" type="checkbox" max_discount="" class="combo_service" cs_price="" name="cs_id[]"  value="">Service 1</label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    
    <div class="form-group col-md-12">
        <div class="col-md-6 float-right">
        <button type="submit" class="btn btn-primary">Submit</button>

        </div>
    </div>
    </form>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).ready(function() {

    $("#date_order").datepicker({
        todayHighlight: true,
        setDate: new Date(),
    });
    $('.place_id').select2();
});
</script>
@endpush
