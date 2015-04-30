@extends('layouts.master')

@section('content')
            
    <section id="recent-works">
        <div class="container">
            <div class="center wow">
                <h2 id="recent_items">{{ $header }}</h2>
                <br />
                <a class="btn btn-default" href="/feed/{{ $item_type }}/reviews">{{ $item_type }} reviews</a>
                <a class="btn btn-default" href="/feed/{{ $item_type }}/posts">{{ $item_type }} discussions</a>
            </div>
            
            <div class="row">
                
                @foreach($items as $item)
                <div class="col-xs-12 col-sm-4 col-md-3">
                    <div class="recent-work-wrap">
                        <a href="/items/{{ $item->id }}">
                        <img class="img-responsive" src="/images/items/medium/{{ $item->image }}" alt="">
                        </a>
                        <div class="overlay">
                            <div class="recent-work-inner">
                                <h3><a href="/items/{{ $item->id }}">{{ $item->name }} ({{ $item->year }})</a> </h3>
                                <p>By {{ $item->creator }}</p>
                                <p><a class="preview channel-category" href="/items/{{ $item->id }}"><i class="fa {{ $item->category_image }}"></i> &nbsp; Open</a></p>
                            </div>
                        </div>
                    </div>
                    <h3 class="recent-work-description"><a href="/items/{{ $item->id }}">{{ Str::limit($item->name, 20) }} ({{ $item->year }})</a> </h3>
                </div>
                @endforeach
                
            </div><!--/.row-->
            
            <?php echo $items->links(); ?>
            
        </div><!--/.container-->
    </section><!--/#recent-works-->
    
@stop
