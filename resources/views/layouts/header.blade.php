@if(isset($header_bg_image) && $header_bg_image)
    <style>
        .main-header {
            background: url("{{ $header_bg_image }}") no-repeat center center;
            background-size: cover;
        }
        canvas {
            z-index: -1;
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
@endif
<header class="bg-placeholder">
    <canvas id="c"></canvas>
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
<script>
    var w = c.width = window.innerWidth,
        h = c.height = window.innerHeight,
        ctx = c.getContext( '2d' ),

        opts = {

            len: 20,
            count: 50,
            baseTime: 10,
            addedTime: 10,
            dieChance: .05,
            spawnChance: 1,
            sparkChance: .1,
            sparkDist: 10,
            sparkSize: 2,

            color: 'hsl(hue,100%,light%)',
            baseLight: 50,
            addedLight: 10, // [50-10,50+10]
            shadowToTimePropMult: 6,
            baseLightInputMultiplier: .01,
            addedLightInputMultiplier: .02,

            cx: w / 2,
            cy: h / 2,
            repaintAlpha: .04,
            hueChange: .1
        },

        tick = 0,
        lines = [],
        dieX = w / 2 / opts.len,
        dieY = h / 2 / opts.len,

        baseRad = Math.PI * 2 / 6;

    ctx.fillStyle = 'black';
    ctx.fillRect( 0, 0, w, h );

    function loop() {

        window.requestAnimationFrame( loop );

        ++tick;

        ctx.globalCompositeOperation = 'source-over';
        ctx.shadowBlur = 0;
        ctx.fillStyle = 'rgba(0,0,0,alp)'.replace( 'alp', opts.repaintAlpha );
        ctx.fillRect( 0, 0, w, h );
        ctx.globalCompositeOperation = 'lighter';

        if( lines.length < opts.count && Math.random() < opts.spawnChance )
            lines.push( new Line );

        lines.map( function( line ){ line.step(); } );
    }
    function Line(){

        this.reset();
    }
    Line.prototype.reset = function(){

        this.x = 0;
        this.y = 0;
        this.addedX = 0;
        this.addedY = 0;

        this.rad = 0;

        this.lightInputMultiplier = opts.baseLightInputMultiplier + opts.addedLightInputMultiplier * Math.random();

        this.color = opts.color.replace( 'hue', tick * opts.hueChange );
        this.cumulativeTime = 0;

        this.beginPhase();
    }
    Line.prototype.beginPhase = function(){

        this.x += this.addedX;
        this.y += this.addedY;

        this.time = 0;
        this.targetTime = ( opts.baseTime + opts.addedTime * Math.random() ) |0;

        this.rad += baseRad * ( Math.random() < .5 ? 1 : -1 );
        this.addedX = Math.cos( this.rad );
        this.addedY = Math.sin( this.rad );

        if( Math.random() < opts.dieChance || this.x > dieX || this.x < -dieX || this.y > dieY || this.y < -dieY )
            this.reset();
    }
    Line.prototype.step = function(){

        ++this.time;
        ++this.cumulativeTime;

        if( this.time >= this.targetTime )
            this.beginPhase();

        var prop = this.time / this.targetTime,
            wave = Math.sin( prop * Math.PI / 2  ),
            x = this.addedX * wave,
            y = this.addedY * wave;

        ctx.shadowBlur = prop * opts.shadowToTimePropMult;
        ctx.fillStyle = ctx.shadowColor = this.color.replace( 'light', opts.baseLight + opts.addedLight * Math.sin( this.cumulativeTime * this.lightInputMultiplier ) );
        ctx.fillRect( opts.cx + ( this.x + x ) * opts.len, opts.cy + ( this.y + y ) * opts.len, 2, 2 );

        if( Math.random() < opts.sparkChance )
            ctx.fillRect( opts.cx + ( this.x + x ) * opts.len + Math.random() * opts.sparkDist * ( Math.random() < .5 ? 1 : -1 ) - opts.sparkSize / 2, opts.cy + ( this.y + y ) * opts.len + Math.random() * opts.sparkDist * ( Math.random() < .5 ? 1 : -1 ) - opts.sparkSize / 2, opts.sparkSize, opts.sparkSize )
    }
    loop();

    window.addEventListener( 'resize', function(){

        w = c.width = window.innerWidth;
        h = c.height = window.innerHeight;
        ctx.fillStyle = 'black';
        ctx.fillRect( 0, 0, w, h );

        opts.cx = w / 2;
        opts.cy = h / 2;

        dieX = w / 2 / opts.len;
        dieY = h / 2 / opts.len;
    });
</script>