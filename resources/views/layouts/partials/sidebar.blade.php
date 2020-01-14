@php
$request_url = request()->segment(1);
$request_sub_url = request()->segment(2);
$permission_list_session = Session::get('permission_list_session');
$menu_list_all = Session::get('menu_list_all');
$menu_list = $menu_list_all->where('parent_id',0);
$permission_list = Session::get('permission_list');


@endphp
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ asset('/') }}">

      <img style="width: 100px;" src="{{asset("images/logo169x46.png")}}" alt="logo">
  </a>

{{--    VIEW SLIDE BAR--}}
@foreach($menu_list as $parent)

    @php
        $className = ($request_url == $parent->link)?' active':'';
        $count = 0;
        $count_parent = 0;
    @endphp

    @if($menu_list_all->where('parent_id',$parent->id)->count() == 0)
        @foreach($permission_list->where('menu_id',$parent->id) as $permission)
            @if( in_array($permission->id,$permission_list_session))
                @php $count_parent++; @endphp
            @endif
        @endforeach
        @if($count_parent != 0)
                <li class="nav-item">
                    <a class="nav-link" href="{{ asset($parent->link) }}">
                        <i class="{{ $parent->icon  }}"></i>
                        <span>{{ $parent->name }}</span>
                    </a>
                </li>
        @endif
    @else
        @foreach($menu_list_all->where('parent_id',$parent->id) as $child)
            @if($permission_list->where('menu_id',$child->id)->count() != 0)
                @foreach($permission_list->where('menu_id',$child->id) as $permission)
                    @if(in_array($permission->id,$permission_list_session))
                        @php $count++; @endphp
                    @endif
                @endforeach
            @endif
        @endforeach
        @if($count> 0)
            <li class="nav-item">
                <a class="nav-link{{ $className == ' active'?'':' collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseId{{ $loop->index }}" aria-expanded="true" aria-controls="collapseId{{ $loop->index }}">
                    <i class="{{ $parent->icon }}"></i>
                    <span>{{ $parent->name }}</span>
                </a>
                <div id="collapseId{{ $loop->index }}" class="collapse{{ $className ==" active"?' show':'' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar" >

                    @foreach($menu_list_all->where('parent_id',$parent->id) as $child)
                        @php $child_count = 0; @endphp
                        @if($permission_list->where('menu_id',$child->id)->count() != 0)
                            @foreach($permission_list->where('menu_id',$child->id) as $permission)
                                @if( in_array($permission->id,$permission_list_session))
                                    @php $child_count++; @endphp
                                @endif
                            @endforeach
                            @if($child_count != 0)
                                @php $className = \Request::is($child->link)?' active':'';  @endphp
                                <a class="dropdown-item{{$className}}" href="{{ asset($child->link) }}">{{ $child->name }}</a>
                            @endif
                        @endif
                    @endforeach
                </div>
            </li>
        @endif
    @endif
@endforeach
{{--    END VIEW SLIDE BAR--}}
    <!-- <hr class="sidebar-divider d-none d-md-block"> -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

</ul>
<!-- End of Sidebar -->
