@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
		
            <div class="center wow">
		<!-- Channel Screen -->
		{{ $channel->source }}
		<br />
		<br />
		<button title="Dim Background" class="btn btn-dim" onclick="dim_background()"> <!-- <i class="fa fa-lightbulb-o"></i> &nbsp; --> Dim Background</button>
            </div>
	    
            <div class="row">
                <div class="features">
                    <div class="col-md-3 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
			<!-- left -->
			
                        <div class="channel_image">
                        <a href="/channels/{{ $channel->id }}">{{ HTML::image($image_path, 'channel_image') }}</a>
                        </div>
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 wow channel-description" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
			<span class="channel-title">{{ $channel->name }}</span>
			<span class="pull-right" title="{{ $category->category }}"><i class="fa {{ $category->image }} fa-2x"></i></span>
			<span class="light_gray_font pull-right"> &nbsp; &nbsp;</span>
			<span class="pull-right channel-category">{{ $category->name }}</span>
			
			<div id="bottom_line">
                        </div>
			
			<p><b>About Channel</b></p>
                        <p>{{ $channel->description }}</p>
							
						<!-- ##Ed Quick fix for spacing -->
						<br />
						<br />
                        </div>
                    </div><!--/.col-md-4-->
		    
                    <div class="col-md-3 wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow fadeInDown">
                        <!-- right -->
			<span class="{{ $channel->live }}">{{ strtoupper($channel->live); }}</span>
                        </div>
                    </div><!--/.col-md-4-->
		    
                </div><!--/.services-->
            </div><!--/.row-->
        </div><!--/.container-->
    </section><!--/#feature-->
@stop
