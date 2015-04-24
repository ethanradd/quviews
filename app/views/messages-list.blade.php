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
			
			<p>
			<a class="btn btn-primary" title="My Sent Messages" href="/messages-list-outbox">
			Switch to [OUTBOX]
			</a>
			</p>
			
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        
			@if($message_count > 0) <!-- ** only show if there are posts -->
			<div id="feed_line">
                        @foreach($messages as $message)
                        
                        <div id="feed_section">
                          <div id="feed_img">
                          <!-- user profile pic -->
			  <a href="/profiles/{{ $message->profile_id }}">{{ HTML::image("images/profiles/small/" . $message->profile_image, 'user image') }}</a>
                          </div>
                          
                          <div id="feed_body">
				
                          <p>
                          <b>
						  <a href="/profiles/{{ $message->profile_id }}">{{ $message->username }}</a>
						  @if($message->role == "admin")
						  &nbsp;
				          <span class="admin_label">admin</span>
						  @endif
						  </b>
			  
			  <!-- Get number of unread messages -->
			  <?php
			  // ##Ed query in loop, improve
			  $unread_count = App::make('MessageController')->count_specific_unread_messages($message->sender_id, $message->receiver_id);
			  ?>
			  <span class="pull-right light_gray_font">{{ number_format($unread_count) }} unread / <b>{{ number_format($message->messages_count) }} total</b></span>
                          </p>
			  
			 <p>
			 <a title="Direct Messages" href="/messages/create/{{ $message->user_id }}">
			 <i class="fa fa-envelope-o fa-2x"></i> &nbsp; Open
			 </a>
			 </p>
			 
                          </div>
                        </div> <!--/. feed section -->
                        
                        @endforeach
                        </div> <!--/. feed line -->
                        
			{{ $messages->links() }}
			
			@endif <!--/. ** -->
			
                        @if($message_count > 0)
			<!-- Do Nothing -->
                        @else
                        <p class="light_gray_font"><b>No messages received yet</b></p>
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
