@extends('layouts.master')

@section('content')
    
    <section id="recent-works">
            <div class="container">
            <div class="row">
                <div class="features">
                    <div class="col-md-3 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        <div class="item_image">
                        <a href="/items/{{ $item->id }}">{{ HTML::image($image_path, 'item image') }}</a>
                        </div>
                        <p>
                        
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        
                        <div id="header_section">
                          <div id="header_img" class="text-center">
                            <h2>
                            <i class="fa {{ $category->image }} fa-2x"></i>
                            <br />
                            <span class="light_gray_font">{{ $category->name }}</span>
                            </h2>
                            
                          </div>
                          <div id="header_body" class="border_left">
                            <h2><span title="{{ $category->name }}">{{ $header }}</h2>
                          </div>
                        </div>
                        
                        <!-- no reviews for topics -->
                        @if($item->locked != "topic")
                        <a href="/reviews/create/{{ $item->id }}" title="Add your review" class="btn btn-review-add">+ Add Review</a>
                        <a href="/reviews/feed/{{ $item->id }}" title="Open Quick Reviews Feed" class="btn btn-review-feed">Read Reviews</a>
                        @endif
                        
                        <a href="/posts/create/{{ $item->id }}" title="Add new discussion Post" class="btn btn-post-add"> + Add Post</a>
                        <a href="/posts/feed/{{ $item->id }}" title="Open Discussion Feed" class="btn btn-post-feed">Read Posts</a>
			
                        <h2 class="margin_top_40">Original Post</h2>
			
			
			<div id="feed_line">
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
			  
                          <p>
                          {{ nl2br($post->body) }}
                          </p>
                          </p>
                          </div>
                        </div> <!--/. feed section -->
                        </div> <!--/. feed line -->
			
			<h2 class="light_gray_font">Reply by {{ $reply->username }}</h2>
			
			<div id="feed_line">
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
                          @if(Auth::id() != $reply->author_id)
			  <span class="light_gray_font pull-right"> &nbsp; &nbsp;</span>
			  <a title="Report" class="btn btn-report pull-right" href="/reports/create/{{ $reply->id }}/reply">Report</a>
                          @endif
                          @endif
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
                        </div> <!--/. feed line -->
			
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow fadeInDown margin_top_20" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow fadeInDown center">
                        <!-- right -->
			
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/ .container-->
    </section>
@stop
