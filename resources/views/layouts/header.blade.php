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
        width: 700px;
    }
</style>
<header class="main-header" style="background-color: #000;">
    <canvas id="c"></canvas>
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