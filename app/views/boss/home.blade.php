@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="center wow">
                <h1 class="big_blue">QuViews</h1>
                <p class="lead">BOSS PAGE</p>
            </div>
                    
           <div class="row">
                <div class="col-md-4 col-sm-6">
                        <!-- left -->
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6  wow">
                        <!-- center -->
                        <p><a  class="btn btn-default"  href="/boss/user-statistics">USER STATISTICS</a></p>
                        <p><a  class="btn btn-default"  href="/boss/content-statistics">CONTENT STATISTICS</a></p>
                        <p><a  class="btn btn-default"  href="/boss/activity-statistics">ACTIVITY STATISTICS</a></p> 
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
        </div><!--/.container-->
    </section><!--/#feature-->

    
@stop
