@extends('layouts.master')

@section('content')
    
    <section id="recent-works">
        <div class="container">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        </div>
                    </div><!--/.col-md-4-->
                        
                    <div class="col-md-4 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        {{ Form::open(array('url' => 'password-reset')) }}
                        
                             <div class="form-group">
                             {{ Form::label('email_username', 'Your Email or Username') }}
                             {{ Form::text('email_username', '', array('class'=>'form-control')) }}
                             </div>
                                
                             <div class="form-group"> 
                             {{ Form::captcha() }}
                             </div>
                             
                             {{ Form::submit('Reset Password', array('class'=>'btn btn-default')) }}
                        
                        {{ Form::close() }}
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- right -->
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
        </div><!--/.container-->
    </section>

@stop
