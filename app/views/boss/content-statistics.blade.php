@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="row">
                <div class="col-md-4 col-sm-6">
                        <!-- left -->
                        <div class="item_category margin_top_20">
                        <a href="/boss/user-statistics">
                        <span title="following activities"><i class="fa fa-folder-open-o fa-4x"></i></span>
                        </a>
                        </div>
                
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6  wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                            
                        <div id="bottom_line">
                        </div>
                        
                        <p><b>Total non-topic items:</b> {{ $item_all }}</p>
                        
                        <p><b>Total movies: </b> {{ $item_movie }}</p>
                            
                        <p><b>Total TV:</b> {{ $item_tv }}</p>
                        
                        <p><b>Total Games:</b> {{ $item_game }}</p>
                            
                        <p><b>Total Music:</b> {{ $item_music }}</p>
                            
                        <p><b>Total Books:</b> {{ $item_book }}</p>
                            
                        <p><b>Total Gadgets:</b> {{ $item_gadget }}</p>
                            
                        <div id="bottom_line">
                        </div>
                            
                        <p><b>Total topic items:</b> {{ $item_all_topic }}</p>
                        <p><b>Total locked items:</b> {{ $item_all_locked }}</p>
                        <p><b>Total editable items:</b> {{ $item_all_editable }}</p>
                            
                        <div id="bottom_line">
                        </div>
                            
                        <p><b>Total Channels:</b>  {{ $channel_all }}</p>
                        
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
           
        </div><!--/.container-->
    </section><!--/#feature-->
@stop
