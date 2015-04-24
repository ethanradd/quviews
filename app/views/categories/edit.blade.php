@extends('layouts.master')

@section('content')

    <section id="recent-works">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        <span title="{{ $category->name }}"><i class="fa {{ $category->image }} fa-2x"></i></span>
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
                        
                        {{ Form::model($category, array('route' => array('categories.update', $category->id), 'method' => 'PUT')) }}
                             <div class="form-group">
                             {{ Form::label('name', 'Category name') }}
                             {{ Form::text('name', null, array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('image', 'Category Icon name (Fonts Awesome)') }}
                             {{ Form::text('image', null, array('class'=>'form-control')) }}
                             </div>
                             
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
