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
			
			@if((Auth::check()) && ($profile->user_id != Auth::id()))
			
			@if($friendship_following_them == 0)
			<p>
                        <a href="/add_friend/{{ $profile->user_id  }}" title="Follow User" class="btn btn-follow">
                        <span> &nbsp; Follow</span>
                        </a>
			</p>
			@elseif($friendship_following_them > 0)
			<p>
                        <a href="/remove_friend/{{ $profile->user_id  }}" title="Unfollow User" class="btn btn-unfollow">
                        <span>Following</span>
                        </a>
			</p>
			@endif
			
			@endif
			
			<br />
			
			<div id="profile_fl_holder">
			
			<div id="profile_fl" class="border_right">
			  <p class="light_gray_font"><b>Following</b></p>
			  <a title="{{ number_format($following_count) }}" href='/profiles/following-list/{{ $profile->id }}'>{{ number_format($following_count) }}</a>
			</div>
			
			<div id="profile_fl">
			  <p class="light_gray_font"><b>Followers</b></p>
			  <a title="{{ number_format($follower_count) }}" href='/profiles/followers-list/{{ $profile->id }}'>{{ number_format($follower_count) }}</a>
			</div>
			
			</div>
			
                        <div id="bottom_line">
                        </div>
			
			@if($friendship_following_me > 0)
			<p class="light_gray_font small">
			FOLLOWS YOU
			</p>
			@endif
			
			@if((Auth::check()) && ($profile->user_id != Auth::id()))
			<p>
			<a title="Direct Messages" href="/messages/create/{{ $profile->user_id }}">
			<i class="fa fa-envelope-o fa-2x"></i>
		    &nbsp; Send
			</a>
			</p>
			
                        <div id="bottom_line">
                        </div>
			@endif
			
			
			@if((Auth::check()) && ($profile->user_id == Auth::id()))
			<div id="profile_fl_holder">
			
			<div id="profile_fl" class="">
			<p>
			<a title="My Messages" href="/messages-list">
			<i class="fa fa-envelope"></i>
		    &nbsp; <small>Read</small>
			</a>
			</p>
			</div>
			
			<div id="profile_fl">
			<p class="pull-right">
			<a title="My Conversation History" href="/profiles/conversation-history-feed/{{ $profile->id }}">
			<i class="fa fa-comment"></i>
		    &nbsp; <small>Read</small>
			</a>
			</p>
			</div>
			
			</div>
				
            <div id="bottom_line">
            </div>
			@endif
			
			<p><b class="light_gray_font">About:</b> {{ $profile->about }}</p>
			<!-- <p><b class="light_gray_font">Country:</b> {{ $profile->country }}</p> -->
			@if(Auth::check())
			@if (($user->id == Auth::id()) OR (Auth::user()->role == "admin"))
			<p><a href="/profiles/{{ $profile->id }}/edit">Edit Profile</a></p>
			@endif
			@endif
			
                        <div id="bottom_line">
                        </div>
			
			@if($favorite_item)
			<p><i class="fa fa-star"></i> &nbsp;<b class="light_gray_font"> Favorite</b></p>
			<p>{{ $favorite_item->name }}</p>
                        <div class="item_image">
                        <a href="/items/{{ $favorite_item->id }}">{{ HTML::image("images/items/" . $favorite_item->image, 'item image') }}</a>
                        </div>
			
			@if($profile->user_id == Auth::id())
                        <p>
                        <a href="{{ URL::to('unfavorite-item') }}" title="Undo Favorite">
			<i class="fa fa-undo"></i>
			&nbsp;
                        Remove
                        </a>
                        </p>
			@endif
			@endif
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        
                        <h2 class="margin_top_40">
						Recent Quick Reviews
						
						@if((Auth::check()) && (Auth::user()->role == "admin"))
                        <small class="pull-right">
						<a title="Change Role" class="font-small light_gray_font" href="/boss/change-user-role/{{ $profile->user_id }}"> &nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-exclamation-circle"></i> &nbsp; Change User Role</a>
                        </small>
						@endif
						
                        @if((Auth::check()) && ($profile->user_id != Auth::id()))
                        <small class="pull-right">
						<a title="Report Item" class="font-small light_gray_font" href="/reports/create/{{ $profile->id }}/profile"><i class="fa fa-exclamation-triangle"></i> &nbsp; Report User</a>
                        </small>
                        @endif
						</h2>
                        
			@if($review_count > 0) <!-- ** only show if there are reviews -->
			<div id="feed_line">
                        @foreach($reviews as $review)
                        <?php
			// ##Ed - future fix - move this to controller
                        $rank_image_path = "images/ranks/" . $review->rank . ".png";
                        ?>
                        
			<div id="feed_section">
                          <div id="feed_img">
			  <a href="/profiles/{{ $review->profile_id }}">
                          {{ HTML::image($rank_image_path, 'rank') }}
			  </a>
                          </div>
                          
                          <div id="feed_body">
                          <p class="controls">
                          <b><a href="/profiles/{{ $review->profile_id }}">{{ $review->username }}</a></b>
			  <span class="light_gray_font pull-right">
				@if($review->created_at == $review->updated_at)
				{{ Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
				@else
				edited {{ Carbon\Carbon::parse($review->updated_at)->diffForHumans() }}
				@endif
			  </span>
                          
                          @if(Auth::check())
                          @if((Auth::user()->role == "admin") || (Auth::id() == $review->author_id))
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/reviews/{{ $review->id }}/edit">Edit &nbsp; </a></span>
			  <span class="light_gray_font pull-right">&nbsp;</span>
			{{ Form::open(array('url' => 'reviews/' . $review->id, 'class' => 'pull-right')) }}
			    {{ Form::hidden('_method', 'DELETE') }}
			    {{ Form::submit('Delete', array('class' => 'btn btn-feed', 'title' => 'Delete Review', 'onclick' => "return confirm('Are you sure?')")) }}
			{{ Form::close() }}
                          @endif
                          @endif
			  
                          @if(Auth::check())
                          @if(Auth::id() != $review->author_id)
			  <span class="light_gray_font pull-right"> &nbsp; &nbsp;</span>
			  <a title="Report" class="btn btn-report pull-right" href="/reports/create/{{ $review->id }}/review">Report</a>
                          @endif
                          @endif
                          
                          </p>
			  <p class="margin-20">
			  <span class="quote_item">
			  <span title="{{ $review->category_name }}"><i class="fa {{ $review->category_image }}"></i></span>
		          &nbsp;
			  <a href="/items/{{ $review->item_id}}">{{ $review->item_name }}</a>
			  </span>
			  </p>
			  
                          <p>
                          {{ nl2br($review->body) }}
                          </p>
                          </div>
                        </div> <!--/. feed section -->
                        
                        @endforeach
                        </div> <!--/. feed line -->
			
			@endif <!--/. ** -->
			
                        @if($review_count > 0)
                            @if($review_count > 3)
                            <a href="/profiles/reviews-feed/{{ $profile->id }}" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>No reviews by {{ $user->username }} yet</b></p>
                        @endif
			
                        
                        <!-- ------------------------------------ -->
                        
                        <h2 class="margin_top_40">Recent Discussions Posts</h2>
                        
			@if($post_count > 0) <!-- ** only show if there are posts -->
			<div id="feed_line">
                        @foreach($posts as $post)
                        
                        <div id="feed_section">
                          <div id="feed_img">
                          <!-- user profile pic -->
			  <a href="/profiles/{{ $post->profile_id }}">{{ HTML::image("images/profiles/small/" . $post->profile_image, 'user image') }}</a>
                          </div>
                          
                          <div id="feed_body">
                          <p class="controls">
                          <b><a href="/profiles/{{ $post->profile_id }}">{{ $post->username }}</a></b>
			  <span class="light_gray_font pull-right">
				@if($post->created_at == $post->updated_at)
				{{ Carbon\Carbon::parse($post->created_at)->diffForHumans() }}
				@else
				edited {{ Carbon\Carbon::parse($post->updated_at)->diffForHumans() }}
				@endif
			  </span>
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/replies/create/{{ $post->id }}">Reply &nbsp; </a></span>
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/posts/{{ $post->id }}">Replies ({{ $post->replies_count }}) &nbsp; </a></span>
                          
                          @if(Auth::check())
                          @if((Auth::user()->role == "admin") || (Auth::id() == $post->author_id))
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/posts/{{ $post->id }}/edit">Edit &nbsp; </a></span>
			  <span class="light_gray_font pull-right">&nbsp;</span>
			{{ Form::open(array('url' => 'posts/' . $post->id, 'class' => 'pull-right')) }}
			    {{ Form::hidden('_method', 'DELETE') }}
			    {{ Form::submit('Delete', array('class' => 'btn btn-feed', 'onclick' => "return confirm('Are you sure?')")) }}
			{{ Form::close() }}
                          @endif
                          @endif
			  
                          @if(Auth::check())
                          @if(Auth::id() != $post->author_id)
			  <span class="light_gray_font pull-right"> &nbsp; &nbsp;</span>
			  <a title="Report" class="btn btn-report pull-right" href="/reports/create/{{ $post->id }}/post">Report</a>
                          @endif
                          @endif
			  
                          </p>
			  <p class="margin-20">
			  <span class="quote_item_{{ $post->item_locked }}">
			  <span title="{{ $post->category_name }}"><i class="fa {{ $post->category_image }}"></i></span>
		          &nbsp;
			  <a href="/items/{{ $post->item_id}}">{{ $post->item_name }}</a>
			  </span>
			  </p>
			  
                          <p>
                          {{ nl2br($post->body) }}
                          </p>
                          </p>
                          </div>
                        </div> <!--/. feed section -->
                        
                        @endforeach
                        </div> <!--/. feed line -->
                        
			@endif <!--/. ** -->
			
                        @if($post_count > 0)
                            @if($post_count > 3)
                            <a href="/profiles/posts-feed/{{ $profile->id }}" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>No discussions started by {{ $user->username }} yet</b></p>
                        @endif
                        
			
			
			
                        <h2 class="margin_top_40">Recent Replies</h2>
                        
			@if($reply_count > 0) <!-- ** only show if there are replies -->
			<div id="feed_line">
                        @foreach($replies as $reply)
                        
                        <div id="feed_section">
                          <div id="feed_img">
                          <!-- user profile pic -->
			  <a href="/profiles/{{ $reply->profile_id }}">{{ HTML::image("images/profiles/small/" . $reply->profile_image, 'user image') }}</a>
                          </div>
                          
                          <div id="feed_body">
                          <p class="controls">
                          <b><a href="/profiles/{{ $reply->profile_id }}">{{ $reply->username }}</a></b>
			  <span class="light_gray_font pull-right">
				@if($reply->created_at == $reply->updated_at)
				{{ Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}
				@else
				edited {{ Carbon\Carbon::parse($reply->updated_at)->diffForHumans() }}
				@endif
			  </span>
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/posts/{{ $reply->post_id }}" title = "Original Post"> Original Post &nbsp; </a></span>
			  
                          @if(Auth::check())
                          @if((Auth::user()->role == "admin") || (Auth::id() == $reply->author_id))
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/replies/{{ $reply->id }}/edit">Edit &nbsp; </a></span>
			  <span class="light_gray_font pull-right">&nbsp;</span>
			{{ Form::open(array('url' => 'replies/' . $reply->id, 'class' => 'pull-right')) }}
			    {{ Form::hidden('_method', 'DELETE') }}
			    {{ Form::submit('Delete', array('class' => 'btn btn-feed', 'onclick' => "return confirm('Are you sure?')")) }}
			{{ Form::close() }}
                          @endif
                          @endif
			  
                          @if(Auth::check())
                          @if(Auth::id() !=  $reply->author_id)
			  <span class="light_gray_font pull-right"> &nbsp; &nbsp;</span>
			  <a title="Report" class="btn btn-report pull-right" href="/reports/create/{{ $reply->id }}/reply">Report</a>
                          @endif
                          @endif
			  
                          </p>
			  
			  <p class="margin-20">
			  <span class="quote_item_{{ $reply->item_locked }}">
			  <span title="{{ $reply->category_name }}"><i class="fa {{ $reply->category_image }}"></i></span>
		          &nbsp;
			  <a href="/items/{{ $reply->item_id}}">{{ $reply->item_name }}</a>
			  </span>
			  </p>
			   
			  <?php
			  // ##Ed Ideally this should be in controller, improve in the future
			  $original_post_author = User::find($reply->post_author_id);
			  ?>
			   
			  <p class="margin-20">
			  <span class="quote_original_post">
			  <span title="original post">
			  <i class="fa fa-share"></i>
			  &nbsp;
			  <a href="/posts/{{ $reply->post_id}}" title="Original Post">{{ $original_post_author->username }} :</a>
			  </span>
		          &nbsp;
			  {{ $reply->post_body }}
			  </span>
			  </p>
			  
			  <!-- If the reply is seen by author, and was not already marked as read, mark as read -->
			  @if(Auth::check())
			  @if((Auth::id() == $reply->post_author_id) && ($reply->post_author_read != 1))
			  <?php
			  $this_reply = Reply::find($reply->id);
			  $this_reply->timestamps = false;
			  $this_reply->post_author_read = 1;
			  $this_reply->save();
			  ?>
			  @endif
			  @endif
			  
			  @if($reply->quote_id != 0)
			  <?php
			  // ##Ed Ideally this should be in controller, improve in the future
			  $grabbed_quote = Reply::find($reply->quote_id);
			  $grabbed_quote_author = User::find($grabbed_quote->author_id);
			  // ##Ed We can get profile ID for quoted user ...but consider rempving it
			  // It is not worth the processing power
			  // $grabbed_quote_author_profile = Profile::whereUser_id($grabbed_quote_author->id)->first();
			  ?>
			  <p class="quote">
				<i class="fa fa-quote-left"></i>
				&nbsp;
				
				<b>{{ $grabbed_quote_author->username }}</b> :
				
				{{ nl2br($grabbed_quote->body) }}
				&nbsp;
				<i class="fa fa-quote-right"></i>
				
				<!-- If the quote is seen by author, and was not already marked as read, mark as read -->
				@if(Auth::check())
				@if((Auth::id() == $reply->quote_author_id) && ($reply->quote_author_read != 1))
				<?php
				$this_quote = Reply::find($reply->id);
				$this_quote->timestamps = false;
				$this_quote->quote_author_read = 1;
				$this_quote->save();
				?>
				@endif
				@endif
				
			  </p>
			  @endif
			  
			  
                          <p>
                          {{ nl2br($reply->body) }}
                          </p>
                          </p>
                          </div>
                        </div> <!--/. feed section -->
                        
                        @endforeach
                        </div> <!--/. feed line -->
                        
			@endif <!--/. ** -->
			
                        @if($reply_count > 0)
                            @if($reply_count > 3)
                            <a href="/profiles/replies-feed/{{ $profile->id }}" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>No discussion replies by {{ $user->username }} yet</b></p>
                        @endif
			
                        </div>
                    </div><!--/.col-md-4-->
		    
                    <div class="col-md-3 col-sm-6 wow margin_top_20" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow center">
                        <!-- right -->
                        <div class="ad_space">
                            {{ HTML::image(asset('images/ads/ad1.png'), 'ad') }}
                        </div>
                        
                        <h2><i>Summary</i></h2>
                        
                        
                        <canvas id="canvas" height="140" width="120"></canvas>
                        
                        <script>
                        var pieData = [
				{
					value: <?php echo $good_review_count; ?>,
					color:"#4CB848" // Good
				},
				{
					value : <?php echo $eh_review_count; ?>,
					color : "#CAC33C" // Eh
				},
				{
					value : <?php echo $bad_review_count; ?>,
					color : "#EE1C24" // Bad
				}
                                ];
                
                        var myPie = new Chart(document.getElementById("canvas").getContext("2d")).Pie(pieData);
                        </script>
                        
                        <br />
                        <br />
                        
                        <?php
			// Make number format (i.e 1000 will be rendered as 1,000)
                        $good_review_count = number_format($good_review_count);
                        $eh_review_count = number_format($eh_review_count);
                        $bad_review_count = number_format($bad_review_count);
			$review_count = number_format($review_count);
                        ?>
                        
			<!-- handling plural wording of Review / Reviews -->
			@if($review_count == 1)
			<p>{{ $review_count }} Review by {{ $user->username }}</p>
			@else
			<p>{{ $review_count }} Reviews by {{ $user->username }}</p>
			@endif
			
                        <div id="bottom_line">
                        </div>
			
                        <p class="">Good: {{ $good_review_count }}</p>
                        <p class="">Eh: {{ $eh_review_count }}</p>
                        <p class="">Bad: {{ $bad_review_count }}</p>
                        
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/ .container-->
    </section>
@stop
