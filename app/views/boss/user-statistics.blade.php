@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="row">
                <div class="col-md-4 col-sm-6">
                        <!-- left -->
                        <div class="item_category margin_top_20">
                        <a href="/boss/user-statistics">
                        <span title="following activities"><i class="fa fa-users fa-4x"></i></span>
                        </a>
                        </div>
                
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6  wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                            
                        <div id="bottom_line">
                        </div>
                        
                        <p><b>Total registered users:</b> {{ $user_all }}</p>
                        
                        <p><b>No Profile users: </b> {{ $user_no_profile }}</p>
                            
                        <p><b>Active users:</b> {{ $user_active }}</p>
                        
                        <div id="bottom_line">
                        </div>
                            
                        <p><b>Banned users:</b>  {{ $user_banned }}</p>
                            
                        <p><b>Deleted users:</b>  {{ $user_deleted }}</p>
                        
                        <div id="bottom_line">
                        </div>
                        
                        <p><b>Admin users: </b> {{ $user_admin }}</p>
                        
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
           
        </div><!--/.container-->
    </section><!--/#feature-->
@stop
