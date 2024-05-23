            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- Sidebar user panel (optional) -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="{{ Auth::user()->thumbnail ? : assetv(__c('default.user.avatar')) }}" class="img-circle img-avatar" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ Auth::user()->name }}</p>
                        <!-- Status -->
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>

                <!-- search form (Optional) -->
                {{-- <form action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="@lang('label.search')...">
                        <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
                    </div>
                </form> --}}
                <!-- /.search form -->

                <!-- Sidebar Menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    @foreach (__c('menu_groups') as $group)
                    <li class="header">{{ __($group['group_name']) }}</li>
                        @foreach ($group['childs'] as $menu)
                            @if (!isset($menu['permission']) || hasPermission($menu['permission']))
                            @if ( isset($menu['childs']))
                                <li class="treeview">
                                    <a href="#"><i class="{{ $menu['icon'] }}"></i> <span>{{ __($menu['name']) }}</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        @foreach ($menu['childs'] as $child)
                                            @if (!isset($child['permission']) || hasPermission($child['permission']))
                                            <li><a href="{{ route($child['route']) }}">{{ __($child['name']) }}</a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li><a href="{{ route($menu['route']) }}"><i class="{{ $menu['icon'] }}"></i> <span>{{ __($menu['name']) }}</span></a></li>
                            @endif
                            @endif
                        @endforeach
                    @endforeach
                    <li class="header">@lang('menu.account')</li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out text-red"></i> 
                            <span>{{ __('menu.logout') }}</span>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </a>
                    </li>
                </ul>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->