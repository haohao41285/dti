@php 
$navs = [
    ['text' => 'Dashboard', 'icon'=>'fas fa-tachometer-alt', 'link' => 'dashboard'],
    ['text' => 'Customers', 'icon'=>'fas fa-users', 'link' => 'customer','childrens' => [
        ['text' => 'New Customers', 'link'=> 'customer/customers'],
        ['text' => 'Merchants', 'link'=> 'customer/merchants'],
        ['text' => 'Create New Customer', 'link'=> 'customer/add'],
    ]],
    ['text' => 'Marketing', 'icon'=>'fas fa-lightbulb', 'link' => 'marketing','childrens' => [
        ['text' => 'Send SMS', 'link'=> 'marketing/sendsms'],
        ['text' => 'Tracking History', 'link'=> 'marketing/trackinghistory'],        
    ]],
    ['text' => 'DataSetup', 'icon'=>'fas fa-database', 'link' => 'datasetup','childrens' => [
        ['text' => 'Combo', 'link'=> 'datasetup/combos'],
        ['text' => 'Services', 'link'=> 'datasetup/services'],        
        ['text' => 'Service Details', 'link'=> 'datasetup/servicedetails'],   
        ['text' => 'Themes', 'link'=> 'datasetup/themes'],        
        ['text' => 'Licenses', 'link'=> 'datasetup/licenses'],        
    ]],
    ['text' => 'Statistic', 'icon'=>'fas fa-chart-bar', 'link' => 'statistic','childrens' => [
        ['text' => 'Seller', 'link'=> 'statistic/seller'],
        ['text' => 'POS', 'link'=> 'statistic/pos'],        
        ['text' => 'Website', 'link'=> 'statistic/website'],        
    ]],
     ['text' => 'IT Tools', 'icon'=>'fas fa-toolbox', 'link' => 'tools','childrens' => [
        ['text' => 'Clone Website', 'link'=> 'tools/clonewebsite'],
        ['text' => 'Update Website', 'link'=> 'tools/updatewebsite'],
    ]],
    ['text' => 'Users', 'icon'=>'fas fa-user-cog', 'link' => 'user','childrens' => [
        ['text' => 'Users', 'link'=> 'user/list'],
        ['text' => 'Roles', 'link'=> 'user/roles'],        
        ['text' => 'Roles Permissions', 'link'=> 'user/rolepermission'],        
        ['text' => 'Agent Licenses', 'link'=> 'user/agentlicense'],        
    ]],    
    ['text' => 'Settings', 'icon'=>'fas fa-cog', 'link' => 'settings'],
];    
$request_url = request()->segment(1);
$request_sub_url = request()->segment(2);

@endphp
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ asset('/') }}">
      <img src="{{asset("images/logo169x46.png")}}" alt="logo">     
  </a>
  @foreach($navs as $nav)
        @php $className = ($request_url == $nav["link"])?' active':'';  @endphp 
        @if(isset($nav['childrens']) && count($nav['childrens']) > 0)
        <li class="nav-item{{ $className }}">
            <a class="nav-link{{ $className == ' active'?'':' collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseId{{ $loop->index }}" aria-expanded="true" aria-controls="collapseId{{ $loop->index }}">
                <i class="{{ $nav['icon'] }}"></i>
                <span>{{ $nav['text'] }}</span>
            </a>
            <div id="collapseId{{ $loop->index }}" class="collapse{{ $className ==" active"?' show':'' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                @foreach($nav['childrens'] as $childNav)
                    @php $className = Request::is($childNav["link"])?' active':'';  @endphp 
                     <a class="dropdown-item{{ $className }}" href="{{ asset($childNav['link']) }}">{{ $childNav['text'] }}</a>
                @endforeach
            </div>
          </li>
        @else         
        <li class="nav-item{{ $className }}">
            <a class="nav-link" href="{{ asset($nav['link']) }}">
                <i class="{{ $nav['icon'] }}"></i>
                <span>{{ $nav['text'] }}</span>
            </a>
        </li>
         @endif
    @endforeach

</ul>
<!-- End of Sidebar -->