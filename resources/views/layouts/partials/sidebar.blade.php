@php 
$navs = \App\Helpers\MenuHelper::getMenuList();    
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