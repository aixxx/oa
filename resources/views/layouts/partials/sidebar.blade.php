<!-- MENU SIDEBAR WRAPPER -->

<aside class="sidebar sidebar-left">
    <div class="sidebar-content">
        <div class="aside-toolbar">
            <ul class="site-logo">
                <li>
                    <!-- START LOGO -->
                    <a href="/">
                        <div class="logo">
                            <svg width="70px" height="70px" viewBox="0 0 70 70" version="1.1"
                                 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="logo" fill="#54C3F1" fill-rule="nonzero">
                                        艾克科技
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <span class="brand-text">{!! $website_name !!}</span>
                    </a>
                    <!-- END LOGO -->
                </li>
            </ul>
            <ul class="header-controls">
                <li class="nav-item menu-trigger">
                    <button type="button" class="btn btn-link btn-menu" data-toggle-state="mini-sidebar"
                            data-key="leftSideBar">
                        <i class="la la-dot-circle-o"></i>
                    </button>
                </li>
            </ul>
        </div>
        <nav class="main-menu">

            <ul class="nav metismenu" id="menu">
                <li class="active">
                    <a href="{{  route('workflow.entry.index')  }}" aria-expanded="false"><i class="icon dripicons-home"></i><span>首页</span></a>
                </li>
                {!! $menu !!}
            </ul>
        </nav>
    </div>
</aside>