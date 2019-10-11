@php
$navs = \App\Helpers\MenuHelper::getMenuList();
$request_url = request()->segment(1);
$request_sub_url = request()->segment(2);
$permission_list_session = Session::get('permission_list_session');

@endphp
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ asset('/') }}">

      <img style="width: 100px;" src="{{asset("images/logo169x46.png")}}" alt="logo">
  </a>
  @foreach($navs as $nav)
        @php
            $className = ($request_url == $nav["link"])?' active':'';
            $count = 0;
        @endphp

            @if(isset($nav['childrens']) && count($nav['childrens']) > 0 )
            @foreach($nav['childrens'] as $childNav)
                @if(isset($permission_list_session[$childNav['text']]) && $permission_list_session[$childNav['text']]['Read'] != 0)
                    @php
                        $count++;
                    @endphp
                @endif
            @endforeach
            @if($count > 0)
            <li class="nav-item{{ $className }}">
                <a class="nav-link{{ $className == ' active'?'':' collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseId{{ $loop->index }}" aria-expanded="true" aria-controls="collapseId{{ $loop->index }}">
                    <i class="{{ $nav['icon'] }}"></i>
                    <span>{{ $nav['text'] }}</span>
                </a>
                <div id="collapseId{{ $loop->index }}" class="collapse{{ $className ==" active"?' show':'' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar" >

                    @foreach($nav['childrens'] as $childNav)
                        @if(isset($permission_list_session[$childNav['text']]) && $permission_list_session[$childNav['text']]['Read'] != 0)
                            @php $className = Request::is($childNav["link"])?' active':'';  @endphp
                             <a class="dropdown-item{{ $className }}" href="{{ asset($childNav['link']) }}">{{ $childNav['text'] }}</a>
                        @endif
                    @endforeach
                </div>
            </li>
            @endif
            @else
            @if(isset($permission_list_session[$nav['text']]) && $permission_list_session[$nav['text']]['Read'] != 0)
                <li class="nav-item{{ $className }}">
                    <a class="nav-link" href="{{ asset($nav['link']) }}">
                        <i class="{{ $nav['icon'] }}"></i>
                        <span>{{ $nav['text'] }}</span>
                    </a>
                </li>
                @endif
            @endif
    @endforeach
    <!-- <hr class="sidebar-divider d-none d-md-block"> -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

</ul>
<!-- End of Sidebar -->
