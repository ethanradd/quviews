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
			
                        <div id="bottom_line">
                        </div>
			
			<p><b class="light_gray_font">About:</b> {{ $profile->about }}</p>
			<!-- <p><b class="light_gray_font">Country:</b> {{ $profile->country }}</p> -->
			@if(Auth::check())
			@if (($user->id == Auth::id()) OR (Auth::user()->role == "admin"))
			<p><a href="/profiles/{{ $profile->id }}/edit">Edit Profile</a></p>
			@endif
			@endif
                        <div id="bottom_line">
                        </div>
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
			
			<p class="pull-right">
			<span class="light_gray_font"> post replies: {{ number_format($unread_reply_count) }} , quoted: {{ number_format($unread_quote_count) }}</span>
			</p>
			
			<br />
			
			@if($reply_count > 0) <!-- ** only show if there are replies -->
			<div id="feed_line">
                        @foreach($replies as $reply)
                        
			<?php
			// ##Ed temporary solution to ensure we ALWAYS show the reply author's pic and name in feed
			// The SQL is shoddy and sometimes we dont show the right person
			// Review code to streamline it
			
			// Get Reply Author
			$this_reply_author = User::find($reply->author_id);
			// Get Reply Author Profile
			$this_reply_profile = Profile::whereUser_id($reply->author_id)->first();
			
			?>
			
                        <div id="feed_section">
                          <div id="feed_img">
                          <!-- user profile pic -->
			  <a href="/profiles/{{ $this_reply_profile->id }}">{{ HTML::image("images/profiles/small/" . $this_reply_profile->image, 'user image') }}</a>
                          </div>
                          
                          <div id="feed_body">
                          <p class="controls">
                          <b><a href="/profiles/{{ $this_reply_profile->id }}">{{ $this_reply_author->username }}</a></b>
			  <span class="light_gray_font pull-right">
				@if($reply->created_at == $reply->updated_at)
				{{ Carbon\Carbon::parse($reply->created_at)->diffForHumans() }}
				@else
				edited {{ Carbon\Carbon::parse($reply->updated_at)->diffForHumans() }}
				@endif
			  </span>
                          <span class="light_gray_font pull-right">. &nbsp;</span>
                          <span class="light_gray_font pull-right"><a href="/posts/{{ $reply->post_id }}" title = "Original Post"> Original Post &nbsp; </a></span>
			  <span class="light_gray_font pull-right">. &nbsp;</span>
			  <span class="light_gray_font pull-right"><a href="/replies/create/{{ $reply->post_id}}/{{ $reply->id }}">Quote &nbsp; </a></span>
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
                        
			{{ $replies->links() }}
			
			@endif <!--/. ** -->
			
                        @if($reply_count > 0)
                            @if($reply_count > 3)
                            <!-- Do Nothing -->
                            @endif
                        @else
                        <p class="light_gray_font"><b>No new notifications</b></p>
                        @endif
			
                        
                        <!-- ------------------------------------ -->
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow margin_top_20" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow center">
                        <!-- right -->
                        <div class="ad_space">
                            {{ HTML::image(asset('images/ads/ad1.png'), 'ad') }}
                        </div>
                        
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/ .container-->
    </section>
@stop