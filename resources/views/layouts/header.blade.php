@if(isset($background_image) && $background_image)
    <style>
        @media screen and (min-width: 768px) {
            .main-header {
                background: url("{{ $background_image }}") no-repeat center center;
                background-size: 100% auto;
                position: static;
            }
        }
    </style>
@endif
<style>
    canvas {
        position: absolute;
        top: 0;
        left: 0;
        height: 144px;
        width: 100%;
    }
</style>
<header class="main-header" style="background-color: #000;">
    <canvas style="background-color: #1a252d" id="holder"></canvas>
    <div class="container-fluid" style="margin-top: -15px">
        <nav class="navbar site-navbar" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#blog-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{{ route('post.index') }}"
                   class="navbar-brand">{{ $author or 'Blog' }}</a>
            </div>
            <div class="collapse navbar-collapse fix-top" id="blog-navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a class="menu-item" href="{{ route('achieve') }}">归档</a></li>
                    @if(XblogConfig::getValue('github_username'))
                        <li><a class="menu-item" href="{{ route('projects') }}">项目</a></li>
                    @endif
                    @foreach($pages as $page)
                        <li><a class="menu-item"
                               href="{{ route('page.show',$page->name) }}">{{ $page->display_name }}</a></li>
                    @endforeach
                </ul>
                <ul class="nav navbar-nav navbar-right blog-navbar">
                    @if(Auth::check())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
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
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ route('user.show',auth()->user()->name) }}">个人中心</a></li>
                                @if(isAdmin(Auth::user()))
                                    <li><a href="{{ route('admin.index') }}">后台管理</a></li>
                                @endif
                                <li><a href="{{ route('user.notifications') }}">
                                        <?php
                                        $user = auth()->user();
                                        $unreadNotificationsCount = $user->unreadNotifications->count();
                                        ?>
                                        @if($unreadNotificationsCount)
                                            <span class="badge required">{{ $unreadNotificationsCount }}</span>
                                        @endif
                                        通知中心
                                    </a></li>
                                <li class="divider"></li>
                                <li><a href="{{ url('/logout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        退出登录
                                    </a>
                                </li>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </ul>
                        </li>
                    @else
                        <li><a href="{{ url('login') }}">登录</a></li>
                        <li><a href="{{ url('register') }}">注册</a></li>
                    @endif
                </ul>
                <form class="navbar-form navbar-right" role="search" method="get" action="{{ route('search') }}">
                    <input type="text" class="form-control" name="q" placeholder="搜索" required>
                </form>
            </div>
        </nav>
    </div>
    <div class="container-fluid" style="position:relative;">
        <div class="description">{{ $description or 'Stay Hungry. Stay Foolish.' }}</div>
    </div>
</header>
<script>
    $( document ).ready( function() {

        var settings = {
            width: 1920,
            height: 144,
            autoResize: false,
            autoResizeMinWidth: null,
            autoResizeMaxWidth: null,
            autoResizeMinHeight: null,
            autoResizeMaxHeight: null,
            addMouseControls: true,
            addTouchControls: true,
            hideContextMenu: true,
            starCount: 6666,
            starBgCount: 2222,
            starBgColor: { r:0, g:204, b:255 },
            starBgColorRangeMin: 20,
            starBgColorRangeMax: 80,
            starColor: { r:0, g:204, b:255 },
            starColorRangeMin: 50,
            starColorRangeMax: 100,
            starfieldBackgroundColor: { r:5, g:5, b:14 },
            starDirection: 1,
            starSpeed: 20,
            starSpeedMax: 200,
            starSpeedAnimationDuration: 2,
            starFov: 300,
            starFovMin: 200,
            starFovAnimationDuration: 2,
            starRotationPermission: true,
            starRotationDirection: 1,
            starRotationSpeed: 0.0,
            starRotationSpeedMax: 1.0,
            starRotationAnimationDuration: 2,
            starWarpLineLength: 2.0,
            starWarpTunnelDiameter: 100,
            starFollowMouseSensitivity: 0.025,
            starFollowMouseXAxis: true,
            starFollowMouseYAxis: true

        };

        //------------------------------------------------------------------------

        //standalone

        //init

        //var warpdrive = new WarpDrive( document.getElementById( 'holder' ) );
        //var warpdrive = new WarpDrive( document.getElementById( 'holder' ), settings );

        //------------------------------------------------------------------------

        //jQuery

        //init

        $('#holder').warpDrive();
        $('#holder').warpDrive(settings);

        //------------------------------------------------------------------------

    } );
</script>