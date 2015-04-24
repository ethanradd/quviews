@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="row">
                <div class="col-md-4 col-sm-6">
                        <!-- left -->
                        <div class="item_category margin_top_20">
                        <a href="/boss/user-statistics">
                        <span title="following activities"><i class="fa fa-comment-o fa-4x"></i></span>
                        </a>
                        </div>
                
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6  wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                            
                        <div id="bottom_line">
                        </div>
                        
                        <p><b>Total reviews:</b> {{ $review_all }}</p>
                        
                        <p><b>Total posts: </b> {{ $post_all }}</p>
                            
                        <p><b>Total replies:</b> {{ $reply_all }}</p>
                          
                        <div id="bottom_line">
                        </div>
                            
                        <p><b>Total messages:</b> {{ $message_all }}</p>
                        
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
           
        </div><!--/.container-->
    </section><!--/#feature-->
@stop
