@extends('layouts.master')

@section('content')
    
    <section id="recent-works">
            <div class="container">
            <div class="row">
                <div class="features">
                    <div class="col-md-3 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
			
			<div class="item_category margin_top_20">
			<span title="following activities"><i class="fa fa-users fa-4x"></i></span>
			</div>
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
			
                        <div id="bottom_line">
                        </div>
                        
                        <h2>Recent Quick Reviews</h2>
                        
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
			    {{ Form::submit('Delete', array('class' => 'btn btn-feed', 'title' => 'Delete Review', 'onclick' => "return confirm('Are you sure?')" )) }}
			{{ Form::close() }}
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
                            <a href="/following-reviews-feed" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>No reviews yet</b></p>
                        @endif
			
                        
                        <!-- ------------------------------------ -->
                        
                        <h2>Recent Discussions started</h2>
                        
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
                          </p>
			  
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
                            <a href="/following-posts-feed" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>No discussions started yet</b></p>
                        @endif
                        
			
			
			
                        <h2>Recent Replies</h2>
                        
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
                          </p>
			  
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
                            <a href="/following-replies-feed" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>No discussion replies yet</b></p>
                        @endif
			
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
