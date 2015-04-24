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
			
			{{ $reviews->links() }}
			
			@endif <!--/. ** -->
			
                        @if($review_count > 0)
			<!-- do nothing -->
                        @else
                        <p class="light_gray_font"><b>Sorry, no reviews by {{ $user->username }} yet</b></p>
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
                        
			<p>Reviews by {{ $user->username }}</p>
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
