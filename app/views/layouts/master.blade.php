<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <?php
    // ##Ed Check if user is logged in
    if (Auth::check())
    {
        // The user is logged 
        $user_id = Auth::id();
	
        // Find user by id
        $user = User::find($user_id);
        $username = $user->username;
    
	if (Auth::user()->role != "user_no_profile") {
	// Get user profile
	// ##Ed Pulling one record was actually harder than it looks
	// Source: http://stackoverflow.com/questions/23925476/laravel-eloquent-get-one-row
	$profile = Profile::whereUser_id($user_id)->first();
	$profile_id = $profile->id;
	
	// Link to profile page
	$profile_url = "/profiles/" . $profile_id;
	
	// Get notifivations
	
	// Count new messages
	$message_count = Message::where('receiver_id', '=', $user_id)->where('receiver_read', '=', 0)->count();
	
	// Count new followers
	$follower_count = Friend::where('friend_id', '=', $user_id)->where('friend_notified', '=', 0)->count();
	
	// Count Replies
	$reply_count = Reply::where('post_author_id', '=', $user_id)->where('post_author_read', '=', 0)->count();
	$quote_count = Reply::where('quote_author_id', '=', $user_id)->where('quote_author_read', '=', 0)->count();
	$notifications_total = $reply_count + $quote_count;
	}
    }
    ?>
    <title>{{ $title }}</title>
	
	<!-- core CSS -->
    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/font-awesome.min.css') }}
    {{ HTML::style('css/animate.min.css') }}
    {{ HTML::style('css/prettyPhoto.css') }}
    {{ HTML::style('css/main.css') }}
    {{ HTML::style('css/responsive.css') }}
    
    <!--[if lt IE 9]>
    {{ HTML::script('js/html5shiv.js') }}
    {{ HTML::script('js/respond.min.js') }}
    <![endif]-->
    
    <!-- ##Ed Replace image links here with blade and direct link to public folder -->
    <link rel="shortcut icon" href="{{ asset('images/ico/favicon.ico') }}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('images/ico/apple-touch-icon-144-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('images/ico/apple-touch-icon-144-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href= "{{ asset('images/ico/apple-touch-icon-72-precomposed.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images/ico/apple-touch-icon-57-precomposed.png') }}">
    
    <!-- Script for radio button dynamic rank image -->
    <script>
    // ##Ed rough approach, refine it if possible
    function display_good_Result()
    {
    document.getElementById("rank").style.backgroundPosition="-50px 0";  
    }
    
    function display_eh_Result()
    {
    document.getElementById("rank").style.backgroundPosition="-100px 0";  
    }
    
    function display_bad_Result()
    {
    document.getElementById("rank").style.backgroundPosition="-150px 0";  
    }
    </script>
	  
	<script>
	  // Google tracking code
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-62256484-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
    
    <script>
    // ##Ed Custom script to toggle background colour
    // Inspired by source: http://www.htmlforums.com/html-xhtml/t-div-background-color-toggle-141341.html
    function dim_background()
    {
	var el = document.body;
	el.className = el.className ? "" : "dim";
    }
    </script>
    
    <!-- Script for Pie.js -->
    {{ HTML::script('js/chart_js/Chart.js') }}
    <meta name = "viewport" content = "initial-scale = 1, user-scalable = no">
    <style>
    canvas{}
    </style>
    
</head><!--/head-->

<body>

    <header id="header">
    <div class="navbar navbar-inverse" role="banner">
    <div class="container">
	<div class="navbar-header">
	    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	    <span class="sr-only">Toggle navigation</span>
	    <span class="icon-bar"></span>
	    <span class="icon-bar"></span>
	    <span class="icon-bar"></span>
	    </button>
	    <a class="navbar-brand" href="/home">
	    {{ HTML::image('images/logo.png', 'logo') }}
	    </a>
	</div>
    
	<div class="collapse navbar-collapse navbar-ex1-collapse">
	    <ul class="nav navbar-nav">
                <li><a href="{{ url('home') }}" title="home">Home</a></li>
		@if (Auth::check())
		@if (Auth::user()->role != "user_no_profile")
		
		@if($notifications_total > 0)
		<li><i class="fa fa-comment"></i> <a href="/profiles/notifications-feed/{{ $profile_id }}" title=" replies : {{ number_format($reply_count) }}, quoted : {{ number_format($quote_count) }}" class="notifications">{{ number_format($notifications_total) }}</a></li>
		@endif
		
		@if($follower_count > 0)
		<li><i class="fa fa-user"></i> <a href="/profiles/followers-list/{{ $profile_id }}" title=" new followers : {{ number_format($follower_count) }} " class="notifications">{{ number_format($follower_count) }}</a></li>
		@endif
		
		@if($message_count > 0)
		<li><i class="fa fa-envelope"></i> <a href="/messages-list" title=" unread messages : {{ number_format($message_count) }} " class="notifications">{{ number_format($message_count) }}</a></li>
		@endif
		
		<li><a href="{{ $profile_url }}" title="profile">{{ "@" . $username }}</a></li>
		<li class="dropdown">
		    <a href="/following-feed" title="Following: activities">@following</a>
		    <ul class="dropdown-menu">
			<li><a href="/following-reviews-feed" title="Following: reviews">Reviews</a></li>
			<li><a href="/following-posts-feed" title="Following: posts">Posts</a></li>
			<li><a href="/following-replies-feed" title="Following: replies">Replies</a></li>
		    </ul>
		</li>
        <li><a href="/add-item" title="add item">+ Add Item</a></li>
		@else
		<li><a href="/profiles/create" title="create profile">{{ "@" . $username }} : Create Profile</a></li>
		@endif
        @endif
    
		@if (Auth::check())
		<li><a href="/logout" title="log out">Log Out</a></li>
            
        @if (Auth::user()->role == "admin")  
		<li class="dropdown">
		    <a href="#" class="dropdown-toggle" data-toggle="dropdown">More <i class="fa fa-angle-down"></i></a>
		    <ul class="dropdown-menu">
			<li><a href="/items/create" title="add item">+ Direct Add Item</a></li>
            <li><a href="/boss/" title="boss">Boss</a></li>
			<li><a href="/categories/" title="all items">All Categories</a></li>
			<li><a href="/channels/" title="all channels">All Channels</a></li>
			<li><a href="/items/" title="all items">All Items</a></li>
			<li><a href="/profiles/" title="all profiles">All Profiles</a></li>
            <li><a href="/reports/" title="all reports">All Reports</a></li>
            <li><a href="/replies/" title="all replies">All Replies</a></li>
			<li><a href="/reviews/" title="all reviews">All Reviews</a></li>
			<li><a href="/posts/" title="all posts">All Posts</a></li>
			
		    </ul>
		</li>
        @endif
            
            
		@else
        <li><a href="/register" title="register">Register</a></li>
		<li><a href="/login" title="log in">Log In</a></li>
		@endif
        
	    </ul>
	    
        <!-- Search Bar was here -->
	    
	</div>
    </div>
</div>
    
<div class="navbar navbar-inverse navbar-secondary" role="banner">
        <ul class="nav navbar-nav">
		<li class="dropdown">
            <a href="/home#recent_items">Recent Items</a>
            <a href="/home#tv">TV Channels</a>
            <a href="/home#discussion_topics">Topics</a>
            <a href="/random-item" title="random item"><i class="fa fa-random"></i></a>
		</li>
        </ul>
            
	    <div class="col-md-3 pull-right"> 
	    <form class="navbar-form" action="/search-item" role="search">
	    <div class="input-group">
		<input type="text" class="form-control" placeholder="Search for Item / Creator" name="keyword" id="keyword">
		<div class="input-group-btn">
		    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
		</div>
	    </div>
	    </form>
	    </div>
</div>
    
    </header><!--/header-->
    
    <!-- message stored in session -->
    @if(Session::has('message'))
        <div class="alert alert-warning">
            <p>{{ Session::get('message') }}</p>
        </div>
    @endif
    
    @if(Session::has('message_success'))
        <div class="alert alert-success">
            <p>{{ Session::get('message_success') }}</p>
        </div>
    @endif
    
    @if(Session::has('message_danger'))
        <div class="alert alert-danger">
            <p>{{ Session::get('message_danger') }}</p>
        </div>
    @endif
    
    <!-- message passed from function -->
    @if (isset($message_passed))
        <div class="alert alert-warning">
            <p>{{ $message_passed }}</p>
        </div>
    @endif

@yield('content')

    <section id="bottom">
        <div class="container wow" data-wow-duration="1000ms" data-wow-delay="600ms">
            <div class="row">
                <div class="col-md-2 col-md-offset-1 col-sm-6">
                    <div class="widget">
                        <h3>TV / Movies</h3>
                        <ul>
                            <li><a href="/feed/TV/reviews">TV Reviews</a></li>
                            <li><a href="/feed/TV/posts">TV Discussions</a></li>
                            <li><a href="/feed/Movies/reviews">Movie Reviews</a></li>
                            <li><a href="/feed/Movies/posts">Movie Discussions</a></li>
                        </ul>
                    </div>    
                </div><!--/.col-md-2 -->

                <div class="col-md-2 col-sm-6">
                    <div class="widget">
                        <h3>Music</h3>
                        <ul>
                            <li><a href="/feed/Music/reviews">Music Reviews</a></li>
                            <li><a href="/feed/Music/posts">Music Discussions</a></li>
                        </ul>
                    </div>    
                </div><!-- /.col-md-2 -->

                <div class="col-md-2 col-sm-6">
                    <div class="widget">
                        <h3>Games</h3>
                        <ul>
                            <li><a href="/feed/Games/reviews">Games Reviews</a></li>
                            <li><a href="/feed/Games/posts">Games Discussions</a></li>
                        </ul>
                    </div>    
                </div><!-- /.col-md-2 -->
		
                <div class="col-md-2 col-sm-6">
                    <div class="widget">
                        <h3>Books</h3>
                        <ul>
                            <li><a href="/feed/Books/reviews">Books Reviews</a></li>
                            <li><a href="/feed/Books/posts">Books Discussions</a></li>
                        </ul>
                    </div>    
                </div><!-- /.col-md-2 --> 
                
                <div class="col-md-2 col-sm-6">
                    <div class="widget">
                        <h3>Gadgets</h3>
                        <ul>
                            <li><a href="/feed/Gadgets/reviews">Gadgets Reviews</a></li>
                            <li><a href="/feed/Gadgets/posts">Gadgets Discussions</a></li>
                        </ul>
                    </div>    
                </div><!-- /.col-md-2 -->
            </div>
        </div>
    </section><!-- /#bottom -->

    <footer id="footer" class="midnight-blue">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    &copy; <?php echo date("Y"); ?> QuViews All Rights Reserved.
                </div>
                <div class="col-sm-6">
                    <ul class="pull-right">
                        <li><a href="/home">Home</a></li>
                        <li><a href="/about">About QuViews</a></li>
                        &nbsp;
                        <li><a href="/about#tos"><i class="fa fa-exclamation-triangle"></i> &nbsp; Terms Of Service</a></li>
                        &nbsp;
                        <li><a href="/about#privacy">Privacy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer><!--/#footer-->
    
    {{ HTML::script('js/jquery.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}
    {{ HTML::script('js/jquery.prettyPhoto.js') }}
    {{ HTML::script('js/jquery.isotope.min.js') }}
    {{ HTML::script('js/main.js') }}
    {{ HTML::script('js/wow.min.js') }}
    
    <script>
    /*
    For showing word counter
    Source: http://stackoverflow.com/questions/2136647/character-countdown-like-on-twitter
    */
    function updateCountdown()
    {
	// 200 is the max message length
        var remaining = 200 - jQuery('.chr-count').val().length;
        jQuery('.countdown').text(remaining);
    }
    
    jQuery(document).ready(function($)
    {
	updateCountdown();
        $('.chr-count').change(updateCountdown);
        $('.chr-count').keyup(updateCountdown);
    });
    </script>
    
    <script type="text/javascript">
    $(document).ready(function() {
	$('#calendar').datepicker({
	});
    } );
    </script>
</body>
</html>