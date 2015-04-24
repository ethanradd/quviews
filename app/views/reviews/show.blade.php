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
			
                        <h2 class="margin_top_40">Review by {{ $review->username }}</h2>
			
                        <div id="feed_line">
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
                          <p>
                          <b>
                          <a href="/profiles/{{ $review->profile_id }}">{{ $review->username }}</a>
                          </b>
                          
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
			  <span class="light_gray_font pull-right"> &nbsp;</span>
			{{ Form::open(array('url' => 'reviews/' . $review->id, 'class' => 'pull-right')) }}
			    {{ Form::hidden('_method', 'DELETE') }}
			    {{ Form::submit('Delete', array('class' => 'btn btn-feed', 'onclick' => "return confirm('Are you sure?')")) }}
			{{ Form::close() }}
                          @endif
                          @endif
                          </p>
                          
                          <p>
                          {{ nl2br($review->body) }}
                          </p>
                          </div>
                        </div> <!--/. feed section -->
                        </div> <!--/. feed line -->
			
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
