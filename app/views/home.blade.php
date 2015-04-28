@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="center wow">
                <h1 class="big_blue">QuViews</h1>
                <p class="lead">Your Quick Reviews</p>
            </div>
                    
           <div class="row">
                <div class="col-md-4 ">
                        <!-- left -->
                </div><!--/.col-md-4-->
                <div class="col-md-4  wow center">
                        <!-- center -->
                        {{ Form::open(array('url' => '/search-item')) }}
                        <div class="form-group">
                        {{ Form::text('keyword', null, array('placeholder'=>'', 'class'=>'home-input')) }}
                        </div>
                        {{ Form::submit('Search', array('class'=>'btn btn-default')) }}
                        {{ HTML::link('/random-item', 'Random', array('class'=>'btn btn-default')) }} 
                        {{ Form::close() }}
                </div><!--/.col-md-4-->
                <div class="col-md-4 ">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
        </div><!--/.container-->
    </section><!--/#feature-->
    
    
    
    <section id="recent-works">
        <div class="container">
            <div class="center wow fadeInDown">
                <h2 id="recent_items">Recent Items</h2>
                <p class="lead">Find Items that interest you for review and discussion</p>
            </div>
            
            <div class="row">
                
                @foreach($items as $item)
                <div class="col-xs-12 col-sm-4 col-md-3">
                    <div class="recent-work-wrap">
                        <a href="/items/{{ $item->id }}">
                        <img class="img-responsive" src="images/items/medium/{{ $item->image }}" alt="">
                        </a>
                        <div class="overlay">
                            <div class="recent-work-inner">
                                <h3><a href="/items/{{ $item->id }}">{{ $item->name }} ({{ $item->year }})</a> </h3>
                                <p>By {{ $item->creator }}</p>
                                <p><a class="preview channel-category" href="/items/{{ $item->id }}"><i class="fa {{ $item->category_image }}"></i> &nbsp; Open</a></p>
                            </div>
                        </div>
                    </div>
                    <h3 class="recent-work-description"><a href="/items/{{ $item->id }}">{{ Str::limit($item->name, 25) }} ({{ $item->year }})</a> </h3>
                </div>
                @endforeach
                
            </div><!--/.row-->
        </div><!--/.container-->
    </section><!--/#recent-works-->
    
    <section id="partner">
        <div class="container">
            <div class="center wow fadeInDown">
                <h1 id="tv">QuViews TV</h1>
                <p class="lead">Watch Free LIVE Channels and Recroded Shows</p>
            </div>    

            <div class="partners">
                <ul>
                    @foreach($channels as $channel)
                    <li>
                    <a title="{{ $channel->name }}" href="/channels/{{ $channel->id }}">
                    <img class="img-responsive wow fadeInDown" data-wow-duration="1000ms" data-wow-delay="300ms" src="images/channels/{{ $channel->image }}" />
                    <h2 class="hidden-xs hidden-sm">{{ $channel->live }}</h2>
                    </a>
                    </li>
                    @endforeach
                </ul>
            </div>        
        </div><!--/.container-->
    </section><!--/#partner-->
    
    <section id="recent-works">
        <div class="container">
            <div class="center wow fadeInDown">
                <h2 id="discussion_topics">Discussion Topics</h2>
                <p class="lead">General discussion topics</p>
            </div>
            
            <div class="row">
                
                @foreach($topics as $topic)
                <div class="col-xs-12 col-sm-4 col-md-3">
                    <div class="recent-work-wrap">
                        <a href="/items/{{ $topic->id }}">
                        <img class="img-responsive" src="images/items/{{ $topic->image }}" alt="">
                        </a>
                        <div class="overlay">
                            <div class="recent-work-inner">
                                <h3><a href="/items/{{ $topic->id }}">{{ $topic->name }}</a> </h3>
                                <p><a class="preview channel-category" href="/items/{{ $topic->id }}"><i class="fa {{ $topic->category_image }}"></i> &nbsp; Open</a></p>
                            </div> 
                        </div>
                    </div>
                    <h3 class="recent-work-description center"><a href="/items/{{ $topic->id }}">{{ $topic->name }}</a> </h3>
                </div>
                @endforeach
                
            </div><!--/.row-->
        </div><!--/.container-->
    </section><!--/#recent-works-->
    
@stop
