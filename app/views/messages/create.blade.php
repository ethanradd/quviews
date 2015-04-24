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
			<a href="/messages-list">
			<span title="My Messages"><i class="fa fa-envelope fa-4x"></i></span>
			</a>
			</div>
			
			</div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>
			{{ $header }}
			</h2>
                        
			@if(($friendship_following_me != 0) OR (Auth::user()->role == "admin"))
			<!-- If User follows me, show reply form -->
			
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        {{ Form::open(array('url' => 'messages')) }}
                        
                             {{ Form::hidden('passed_receiver_id', $receiver->id, array('id' => 'passed_receiver_id')) }}
			     
			     {{ Form::hidden('passed_receiver_profile_id', $profile->id, array('id' => 'passed_receiver_profile_id')) }}
                             
                             <div class="form-group">
                             {{ Form::label('your message', 'Your Message' , array('class'=>'light_gray_font')) }}
                             {{ Form::textarea('body', '', array('class'=>'form-control chr-count', 'rows'=>'5', 'maxlength'=>'200')) }}
                             </div>
                             
                             {{ Form::submit('Send', array('class'=>'btn btn-default')) }}
                             <span class="countdown"></span>
                        
                        {{ Form::close() }}
			
			@elseif($friendship_following_me == 0)
			
			<!-- If user is not following me, show message -->
			<p class="light_gray_font">You can't send messages to <b>{{ $receiver->username }}</b> until they are also following you</p>
			
			@endif
                        
                        <div id="bottom_line">
                        </div>
                        
                        <h2>Conversation</h2>
			
			@if($message_count > 0) <!-- ** only show if there are replies -->
			<div id="feed_line">
                        @foreach($messages as $message)
                        
                        <div id="feed_section">
                          <div id="feed_img">
                          <!-- user profile pic -->
			  <a href="/profiles/{{ $message->profile_id }}">{{ HTML::image("images/profiles/small/" . $message->profile_image, 'user image') }}</a>
                          </div>
                          
                          <div id="feed_body">
                          <p class="controls">
                          <b><a href="/profiles/{{ $message->profile_id }}">{{ $message->username }}</a></b>
						  @if($message->role == "admin")
						  &nbsp;
				          <span class="admin_label">admin</span>
						  @endif
						  
			  <span class="light_gray_font pull-right">
				{{ Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
				&nbsp;
				{{  date("d-M-Y",strtotime($message->created_at))}}
			  </span>
			  
                          @if(Auth::check())
                          @if((Auth::user()->role == "admin") || (Auth::id() == $message->sender_id))
			  <span class="light_gray_font pull-right">&nbsp;</span>
			{{ Form::open(array('url' => 'messages/' . $message->id, 'class' => 'pull-right')) }}
			    {{ Form::hidden('_method', 'DELETE') }}
			    {{ Form::submit('Delete', array('class' => 'btn btn-feed', 'onclick' => "return confirm('Are you sure?')")) }}
			{{ Form::close() }}
                          @endif
                          @endif
                          </p>
			  
			  <!-- If the message is seen by author, and was not already marked as read, mark as read -->
			  @if(Auth::check())
			  @if((Auth::id() == $message->receiver_id) && ($message->receiver_read != 1))
			  <?php
			  $this_message = Message::find($message->id);
			  $this_message->timestamps = false;
			  $this_message->receiver_read = 1;
			  $this_message->save();
			  ?>
			  @endif
			  @endif
			  
                          <p>
                          {{ nl2br($message->body) }}
                          </p>
			  
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
                        <p class="light_gray_font"><b>No messages yet</b></p>
                        @endif
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- right -->
			<h2 class="light_gray_font"><i> {{ $receiver->username }} </i></h2>
                        <div class="user_image">
                        <a href="/profiles/{{ $profile->id }}">{{ HTML::image($image_path, 'user image') }}</a>
                        </div>
			
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/.container-->
    </section>

@stop
