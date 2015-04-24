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
			<a href="/following-feed">
			<span title="following activities"><i class="fa fa-users fa-4x"></i></span>
			</a>
			</div>
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        
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
                        
			{{ $posts->links() }}
			
			@endif <!--/. ** -->
			
                        @if($post_count > 0)
			<!-- Do Nothing -->
                        @else
                        <p class="light_gray_font"><b>Sorry, no discussions yet</b></p>
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
