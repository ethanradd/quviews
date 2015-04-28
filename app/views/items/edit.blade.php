@extends('layouts.master')

@section('content')

    <section id="recent-works">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        <div class="item_image">
                        <a href="/items/{{ $item->id }}">{{ HTML::image($image_path, 'item image') }}</a>
                        </div>
                        <p class="light_gray_font">{{ $item->locked }}</p>
                        <p>
                        @if((Auth::check()) && (Auth::user()->role == "admin"))
                        <a class="btn btn-primary" href="/items/remove-item-image/{{ $item->id }}" onclick="return confirm('Are you sure?')">REMOVE Item Image</a>
                        @endif
                        </p>
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        
                        {{ Form::model($item, array('route' => array('items.update', $item->id), 'method' => 'PUT', 'files'=> true)) }}
                             <div class="form-group">
                             {{ Form::label('name', 'Title') }}
                             {{ Form::text('name', null, array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('creator_by', 'By: &nbsp;') }}
                             {{ Form::label('creator', '(name of Director / Network / Artist / Company / Author)', array('class'=>'light_gray_font')) }}
                             {{ Form::text('creator', null, array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('year', 'Release Year') }}
                             {{ Form::text('year', null, array('class'=>'form-control')) }}
                             </div>
                             
                             @if(Auth::user()->role == "admin")
                             <div class="form-group">
                             {{ Form::label('category', '*Admin: Category - extra categories for admin') }}
                             {{ Form::select('category_id', array('1' => 'Movies', '2' => 'TV',
                             '3' => 'Music' ,'4' => 'Games','5' => 'Books','6' => 'Gadgets' ,
                             '7' => 'News*', '8' => 'Politics*', '9' => 'Entertainment*',
                             '10' => 'Fashion*', '12' => 'Sports*'), null, array('class' => 'form-control')) }}
                             </div>
                             @else
                             <div class="form-group">
                             {{ Form::label('category', 'Category') }}
                             {{ Form::select('category_id', array('1' => 'Movies', '2' => 'TV', '3' => 'Music' ,'4' => 'Games','5' => 'Books','6' => 'Gadgets'), null, array('class' => 'form-control')) }}
                             </div>
                             @endif
                             
                             <div class="form-group">
                            {{ Form::label('image', 'New Item Image') }}
                            {{ Form::file('image') }}
                             </div>
                             
                             @if(Auth::user()->role == "admin")
                             <br />
                             <div class="form-group">
                             {{ Form::label('locked', '*Admin : Item Editable / Locked / Topic') }}
                             {{ Form::select('locked', array('editable' => 'Editable', 'locked' => 'Locked', 'topic' => 'Topic'), null, array('class' => 'form-control')) }}
                             </div>
                             @else
                             {{ Form::hidden('locked', null, array('id' => 'locked')) }}
                             @endif
                             
                             <br />
                             
                             {{ Form::submit('Save Changes', array('class'=>'btn btn-default')) }}
                        
                        {{ Form::close() }}
                        </div>
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
