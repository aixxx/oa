<nav class="top-toolbar navbar navbar-desktop flex-nowrap">
    <ul class="navbar-nav nav-right">
        <li>
            <H5 style="margin-right: 10px;margin-top: 30px;">当前登录账户：{{ \Auth::guard('admin')->user()->name}}</H5>
        </li>
        <li>
            <br/>
            <button style="margin-right: 10px;" class="btn btn-primary btn-sm"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                        class="icon dripicons-lock-open"></i> 退出登录
            </button>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" style="display: none;">

            </form>
        </li>
    </ul>
</nav>