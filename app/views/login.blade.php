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
                        <h2>{{ $header }} <span class="pull-right"><a href="/register">Register</a></span></h2>
                        
                        <div id="bottom_line">
                        </div>
                        
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        {{ Form::open(array('url' => 'login')) }}
                             <div class="form-group">
                             {{ Form::label('username', 'Username') }}
                             {{ Form::text('username', '', array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('password', 'Password') }}
                             {{ Form::password('password', array('class'=>'form-control')) }}
                             </div>
                                
                             <div class="form-group">
                             {{ Form::checkbox('remember', null, null, array('id'=>'remember')) }}
                             {{ Form::label('remember', ' &nbsp; Remember me', array('class'=>'light_gray_font')) }}
                             
                             <p class="pull-right"><a href="/password-reset">Forgot your password?</a></p>
                             </div>
                             
                             {{ Form::submit('Log In', array('class'=>'btn btn-default')) }}
                        
                        {{ Form::close() }}
                        </div>
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