@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="row">
                <div class="col-md-4 col-sm-6">
                        <!-- left -->
                        <div class="user_image">
                        <a href="/profiles/{{ $profile->id }}">{{ HTML::image($image_path, 'user image') }}</a>
                        </div>
                            
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6  wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                            
                        <div id="bottom_line">
                        </div>
                        
                        <p><b>Username:</b> {{ $user->username }}</p>
                            
                        <p><b>Current role:</b>  {{ $user->role }}</p>
                            
                        <p><b>Email Address:</b>  {{ $user->email }}</p>
                            
                        <br />
                            
                        <a title="Go"  target="_blank" class="btn btn-small btn-default" href="/profiles/{{ $profile->id }}"><i class="fa fa-arrow-circle-o-right"></i> &nbsp; Go To User Profile</a>
                        
                        <br />
                        
                        <h2 class="light_gray_font">Action</h2>
                            
                        <div id="bottom_line">
                        </div>
                         
                        <p><a class="btn btn-danger" onclick="return confirm('Are you sure?')" href="/ban-user/{{ $user->id }}">BAN USER</a></p>
                            
                        <p><a  class="btn btn-warning" onclick="return confirm('Are you sure?')" href="/delete-user/{{ $user->id }}">DELETE USER</a></p>
                            
                        <p><a  class="btn btn-success" onclick="return confirm('Are you sure?')" href="/revive-user/{{ $user->id }}">REVIVE USER</a></p>
                            
                        <div id="bottom_line">
                        </div>
                            
                        <p><a  class="btn btn-primary" onclick="return confirm('Are you sure?')" href="/promote-user/{{ $user->id }}">PROMOTE USER TO ADMIN</a></p>
                        
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
           
        </div><!--/.container-->
    </section><!--/#feature-->
@stop
