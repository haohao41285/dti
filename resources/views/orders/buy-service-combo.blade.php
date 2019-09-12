@extends('layouts.app')
@section('title')
    Buy Combo/Service
@endsection
@push('styles')
{{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"> --}}
@endpush
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <div class="card shadow mb-4">
        <div class="card-body">
          <div class="table-responsive">
          <h5>Combo/Service List</h5>
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($combo_service_list as $cs)
              <tr>
                <td><input type="checkbox" name="cs_id" cs_id="{{$cs->id}}"></td>
                <td>{{$cs->cs_name}}</td>
                <td>{{$cs->cs_description}}</td>
                <td>{{$cs->cs_type == 1?"Combo":"Service"}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow mb-4">
        <h5>Customer Information</h5>
        <div class="card-body">
          <div class="form-group row">
            <span class="col-md-4">Customer Name</span>
            <span class="com-md-8"><b>{{$customer_info->ct_fullname}}</b></span>
          </div>
          <div class="form-group row">
            <span class="col-md-4">Customer Email</span>
            <span class="com-md-8"><b>{{$customer_info->ct_email}}</b></span>
          </div>
          <div class="form-group row">
            <span class="col-md-4">Business Phone</span>
            <span class="com-md-8"><b>{{$customer_info->ct_business_phone}}</b></span>
          </div>
          <div class="form-group row">
            <span class="col-md-4">Cell Phone</span>
            <span class="com-md-8"><b>{{$customer_info->ct_cell_phone}}</b></span>
          </div>
          <div class="form-group row">
            <span class="col-md-4">Customer Address</span>
            <span class="com-md-8"><b>{{$customer_info->ct_address}}</b></span>
          </div>
          <div class="form-group row">
            <span class="col-md-4">Customer Website</span>
            <span class="com-md-8"><b>{{$customer_info->ct_website}}</b></span>
          </div>
          <div class="form-group row">
            <span class="col-md-4">Customer Note</span>
            <span class="com-md-8"><b>{{$customer_info->ct_note}}</b></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#home">Cash</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#menu1">Credit Card</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#menu2">Check</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div id="home" class="container tab-pane active"><br>
      <h3>HOME</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </div>
    <div id="menu1" class="container tab-pane fade"><br>
      <h3>Menu 1</h3>
      <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <div id="menu2" class="container tab-pane fade"><br>
      <h3>Menu 2</h3>
      <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
</script>
@endpush

