@extends('layouts.master')

@section('content')
    
    <section id="recent-works">
            <div class="container">
            <div class="row">
                <div class="features">
                    <div class="col-md-3 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
			
                        <div class="user_image">
                        <a href="/profiles/{{ $profile->id }}">{{ HTML::image($image_path, 'user image') }}</a>
                        </div>
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        
			@if($followers_count > 0) <!-- ** only show if there are posts -->
			<div id="feed_line">
                        @foreach($followers as $followers_user)
                        
                        <div id="feed_section">
                          <div id="feed_img">
                          <!-- user profile pic -->
			  <a href="/profiles/{{ $followers_user->profile_id }}">{{ HTML::image("images/profiles/small/" . $followers_user->profile_image, 'user image') }}</a>
                          </div>
                          
                          <div id="feed_body">
			  <!-- Check do we follow user? -->
			  <?php
			  // ##Ed performance hog, query in loop, improve
			  $friendship_following_them = App::make('FriendController')->check_friendship(Auth::id(), $followers_user->user_id);
			  ?>
			  
			@if((Auth::check()) && ($followers_user->user_id != Auth::id()))
			
			@if($friendship_following_them == 0)
			<p class="pull-right">
                        <a href="/add_friend/{{ $followers_user->user_id  }}" title="Follow User" class="btn btn-follow">
                        <span> &nbsp; Follow</span>
                        </a>
			</p>
			@elseif($friendship_following_them > 0)
			<p class="pull-right">
                        <a href="/remove_friend/{{ $followers_user->user_id }}" title="Unfollow User" class="btn btn-unfollow">
                        <span>Following</span>
                        </a>
			</p>
			@endif
			
			@endif
				
                          <p>
                          <b><a href="/profiles/{{ $followers_user->profile_id }}">{{ $followers_user->username }}</a></b>
                          </p>
			  
			  <!-- If the is seen by profile owner, and was not already marked as seen, mark as seen -->
			  <!-- ##Ed Query in loop, if possible improve -->
			  @if(Auth::check())
			  @if((Auth::id() == $profile->user_id) && ($followers_user->friend_notified != 1))
			  <?php
			  // Update friends table
			  $this_friend = Friend::find($followers_user->friend_id);
			  $this_friend->friend_notified = 1;
			  $this_friend->save();
			  ?>
			  @endif
			  @endif
			  
                          </div>
                        </div> <!--/. feed section -->
                        
                        @endforeach
                        </div> <!--/. feed line -->
                        
			{{ $followers->links() }}
			
			@endif <!--/. ** -->
			
                        @if($followers_count > 0)
			<!-- Do Nothing -->
                        @else
                        <p class="light_gray_font"><b>Sorry, no followers yet</b></p>
                        @endif
			
                        
                        <!-- ------------------------------------ -->
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow margin_top_20" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow center">
                        <!-- right -->
                        
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/ .container-->
    </section>
@stop
