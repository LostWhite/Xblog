@if(isset($header_bg_image) && $header_bg_image)
    <style>
        .main-header {
            background: url("{{ $header_bg_image }}") no-repeat center center;
            background-size: cover;
        }
    </style>
@endif
<header class="main-header bg-placeholder">
    <div class="container-fluid" style="margin-top: -15px">
        <nav class="navbar navbar-dark navbar-expand-lg">
            <a href="{{ route('post.index') }}" id="blog-navbar-brand" class="navbar-brand">{{ $author or 'Blog' }}</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse"
                    data-target="#blog-navbar-collapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="blog-navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('achieve') }}">归档</a></li>
                    @if(XblogConfig::getValue('github_username'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('projects') }}">项目</a></li>
                    @endif
                    @foreach($pages as $page)
                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('page.show',$page->name) }}">{{ $page->display_name }}</a>
                        </li>
                    @endforeach
                </ul>
                <ul class="nav navbar-nav ml-auto justify-content-end">
                    <form class="form-inline" role="search" method="get" action="{{ route('search') }}">
                        <input type="text" class="form-control" name="q" placeholder="搜索" required>
                    </form>
                    @if(Auth::check())
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink"
                               data-toggle="dropdown">
                                <?php
                                $user = auth()->user();
                                $unreadNotificationsCount = $user->unreadNotifications->count();
                                ?>
                                @if($unreadNotificationsCount)
                                    <span class="badge required">{{ $unreadNotificationsCount }}</span>
                                @endif
                                {{ $user->name }}
                                <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="{{ route('user.show',auth()->user()->name) }}">个人中心</a>
                                @if(isAdmin(Auth::user()))
                                    <a class="dropdown-item" href="{{ route('admin.index') }}">后台管理</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('user.notifications') }}">
                                    <?php
                                    $user = auth()->user();
                                    $unreadNotificationsCount = $user->unreadNotifications->count();
                                    ?>
                                    @if($unreadNotificationsCount)
                                        <span class="badge required">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                    通知中心
                                </a>
                                <a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    退出登录
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ url('login') }}">登录</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('register') }}">注册</a></li>
                    @endif

                </ul>
            </div>
        </nav>
    </div>
    <div class="container-fluid">
        <div class="description">{{ $description or 'Stay Hungry. Stay Foolish.' }}</div>
    </div>
</header>