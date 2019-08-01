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

                                    </g>
                                </g>
                            </svg>
                        </div>
                        <span class="brand-text">OA</span>
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
                <li class="sidebar-header"><span>系统管理</span></li>

                <li class="nav-dropdown active"><a class="has-arrow active" href="#" aria-expanded="false"><i
                                class="icon dripicons-ticket"></i><span>权限-电脑端</span></a>
                    <ul class="nav-sub active" aria-expanded="true">
                        <li class="active"><a href="/admin/users" class="active"><span>管理员</span></a>
                        </li>

                        <li><a href="/admin/roles"><span>角色管理</span></a></li>
                    </ul>
                </li>

                <li class="nav-dropdown active"><a class="has-arrow active" href="#" aria-expanded="false"><i
                                class="icon dripicons-scale"></i><span>权限-手机端</span></a>
                    <ul class="nav-sub active" aria-expanded="true">
                        <li class="active"><a href="/admin/vueaction" class="active"><span>页面路由</span></a>
                        </li>
                        <li class="active"><a href="/admin/routes" class="active"><span>接口路由</span></a>
                        </li>
                        <li class="active"><a href="/admin/apiroles" class="active"><span>角色管理</span></a>
                        </li>
                        <li class="active"><a href="/admin/user" class="active"><span>用户管理</span></a>
                        </li>
                    </ul>
                </li>
                <li><a href="/admin/abilities" aria-expanded="false"><i
                                class="icon dripicons-menu"></i><span>基础数据管理</span></a>
                </li>
                <li><a href="/admin/resetpassword" aria-expanded="false"><i
                                class="icon dripicons-gear"></i><span>修改密码</span></a>
                </li>
            </ul>
        </nav>
    </div>
</aside>