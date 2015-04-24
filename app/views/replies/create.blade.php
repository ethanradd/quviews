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
                        
                        <h2 class="light_gray_font">Reply</h2>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        
                        {{ Form::open(array('url' => 'replies')) }}
                        
                             {{ Form::hidden('passed_post_id', $post->id, array('id' => 'passed_post_id')) }}
                             
                             @if($quote)
			     <?php
			     // ##Ed Ideally this should be in controller, improve in the future
			     $quote_author = User::find($quote->author_id);
			     ?>
                             <p class="quote">
                             Quote: &nbsp;
			     <i class="fa fa-quote-left"></i>
			     &nbsp;
                             <b>{{ $quote_author->username }}</b>: {{ nl2br($quote->body) }}
                             {{ Form::hidden('passed_quote_id', $quote->id, array('id' => 'passed_quote_id')) }}
			     &nbsp;
		             <i class="fa fa-quote-right"></i>
                             </p>
                             
                             @else
                             
                             {{ Form::hidden('passed_quote_id', '', array('id' => 'passed_quote_id')) }}
                             
                             @endif
                             
                             <div class="form-group">
                             <!-- {{ Form::label('your reply', 'Your Reply', array('class'=>'light_gray_font')) }} -->
                             {{ Form::textarea('body', '', array('class'=>'form-control chr-count', 'rows'=>'5', 'maxlength'=>'200')) }}
                             </div>
                             
                             {{ Form::submit('Reply', array('class'=>'btn btn-default')) }}
                             <span class="countdown"></span>
                        
                        {{ Form::close() }}
                        
                        <div id="bottom_line">
                        </div>
                        
                        </div>
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow fadeInDown">
                        <!-- right -->
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/.container-->
    </section>

@stop
