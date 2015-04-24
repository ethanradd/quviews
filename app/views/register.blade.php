@extends('layouts.master')

@section('content')
    
    <section id="recent-works">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        </div>
                    </div><!--/.col-md-4-->
                        
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        {{ Form::open(array('url' => 'register')) }}
                             <div class="form-group">
                             {{ Form::label('email', 'Email Address') }}
                             {{ Form::text('email', '', array('class'=>'form-control')) }}
                             </div>
                        
                             <div class="form-group">
                             {{ Form::label('username', 'Username') }}
                             {{ Form::text('username', '', array('class'=>'form-control')) }}
                             </div>
                        
                             <div class="form-group">
                             {{ Form::label('password', 'Password') }}
                             {{ Form::password('password', array('class'=>'form-control')) }}
                             </div>
                                
                             <div class="form-group"> 
                             {{ Form::captcha() }}
                             </div>
                                
                             <p><small>By clicking Sign Up, you agree to our <a href="/about#tos" target="_blank">Terms</a></small></p>
                             
                             {{ Form::submit('Sign Up', array('class'=>'btn btn-default')) }}
                        
                        {{ Form::close() }}
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- right -->
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
    </section>

@stop
