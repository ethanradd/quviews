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
						
						@if(Auth::check())
                        <p>
                        <a href="{{ URL::to('items/' . $item->id . '/edit') }}" title="Edit Item" class="btn btn-edit-item">
                        {{ $item->locked }}
                        </a>
                        </p>
                        
                        <p>
                        <a href="{{ URL::to('favorite-item/' . $item->id) }}" title="Make Favorite" class="btn btn-favorite-item">
                        <i class="fa fa-star fa-2x"></i>
                        </a>
                        </p>
                        @endif
						
                        <p>
                            @if($favorite_count == 0)
                            <!-- Do Nothing -->
                            @elseif($favorite_count == 1)
                            <!-- Single -->
                            1 Person's favorite
                            @else
                            <!-- Plural -->
                            {{ number_format($favorite_count) }} people's favorite
                            @endif
                        </p>
                        
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
                        
						@if((Auth::check()) && (Auth::user()->role == "admin"))
						<br />
						<br />
						<p>
						{{ Form::open(array('url' => 'items/' . $item->id, 'class' => '')) }}
							{{ Form::hidden('_method', 'DELETE') }}
							{{ Form::submit('Delete Item', array('class' => 'btn btn-danger', 'onclick' => "return confirm('Are you sure?')")) }}
						{{ Form::close() }}
						</p>
						@endif
						
                        @if($item->locked != "topic")
                        <h2 class="margin_top_40">
                        Recent Quick Reviews
                        
                        @if(Auth::check())
                        <small class="pull-right">
						<a title="Report Item" class="font-small light_gray_font" href="/reports/create/{{ $item->id }}/item"><i class="fa fa-exclamation-triangle"></i> &nbsp; Report Item</a>
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
                          
                          @if(Auth::check())
                          @if(Auth::id() != $review->author_id)
			  <span class="light_gray_font pull-right"> &nbsp; &nbsp;</span>
			  <a title="Report" class="btn btn-report pull-right" href="/reports/create/{{ $review->id }}/review">Report</a>
                          @endif
                          @endif
                          
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
                            <a href="/reviews/feed/{{ $item->id }}" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>Sorry, no reviews yet, you can <a href="/reviews/create/{{ $item->id }}">make one!</a></b></p>
                        @endif
                        
                        @endif <!-- end topic check -->
                        <!-- ------------------------------------ -->
                        
                        <h2 class="margin_top_40">Recent Discussion Posts</h2>
                        
                        @if($post_count > 0) <!-- ** only show if there are reviews -->
                        <div id="feed_line">
                        @foreach($posts as $post)
                        
                        <div id="feed_section">
                          <div id="feed_img">
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
			  <span class="light_gray_font pull-right"> &nbsp;</span>
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
                          </div>
                        </div> <!--/. feed section -->
                        
                        @endforeach
                        </div> <!--/. feed line -->
                        
                        @endif <!--/. ** -->
                        
                        @if($post_count > 0)
                            @if($post_count > 3)
                            <a href="/posts/feed/{{ $item->id }}" title="Open Discussion Feed" class="btn btn-more">More</a>
                            @endif
                        @else
                        <p class="light_gray_font"><b>Sorry, no discussions yet, you can <a href="/posts/create/{{ $item->id }}">start one!</a></b></p>
                        @endif
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow margin_top_20" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow center">
                        <!-- right -->
                        <div class="ad_space">
                            {{ HTML::image(asset('images/ads/ad1.png'), 'ad') }}
                        </div>
                        
                        <!-- no review summary for topics -->
                        @if($item->locked != "topic")
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
                        $good_review_count = number_format($good_review_count);
                        $eh_review_count = number_format($eh_review_count);
                        $bad_review_count = number_format($bad_review_count);
                        ?>
                        
                        <p class="">Good: {{ $good_review_count }}</p>
                        <p class="">Eh: {{ $eh_review_count }}</p>
                        <p class="">Bad: {{ $bad_review_count }}</p>
                        @endif
                        
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/ .container-->
    </section>
@stop
