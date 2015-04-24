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

                    <div class="col-md-4 col-sm-6" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="">
                        <!-- center -->
                        <h2>Do another search</h2>
                        {{ Form::open(array('url' => '/search-item')) }}
                        <div class="form-group">
                        {{ Form::text('keyword', null, array('placeholder'=>'search by keyword', 'class'=>'form-control')) }}
                        </div>
                        {{ Form::submit('Search', array('class'=>'btn btn-default')) }}
                        {{ Form::close() }}
                        
                        <br />
                        
                        <h2>
                        {{ $header }}
                        </h2>
                        
                        <div id="bottom_line">
                        </div>
                        
                        @if (isset($items))
                        @foreach($items as $item)
                        
                        <h3>
                        <span title="{{ $item->category_name }}"><i class="fa {{ $item->category_image }}"></i></span>
                        &nbsp;
                        <b><a href="{{ url('items/' . $item->id) }}">{{ $item->name }} - ({{ $item->year }})</a> <span class="light_gray_font"> &nbsp; by {{ $item->creator }}</span></b>
                        </h3>
                        <div id="bottom_line">
                        </div>
                        @endforeach
                        <!-- pagination links -->
                        {{ $items->links() }}
                        @endif
                        
                        @if ($items_count > 0)
                        <p>Can't find what you are looking for? ...<a href="/add-item">you can add it!</a></p>
                        @else
                        <p>Sorry, we couldn't find that ...BUT, <a href="/add-item">you can add it!</a></p>
                        @endif
                        
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- right -->
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
    </section>
@stop
