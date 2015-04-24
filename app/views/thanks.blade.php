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
                        <p><b>You're now registered!</b></p>
                        <p>A confirmation link has been sent to: <b>{{ $theEmail }}</b></p>
                        <p>Please use the link sent to your email to validate your account.</p>
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
