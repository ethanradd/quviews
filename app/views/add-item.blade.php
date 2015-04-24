@extends('layouts.master')

@section('content')
    
    <section id="home_page" >
        <div class="container">
           <div class="row">
                <div class="col-md-4 col-sm-6">
                        <!-- left -->
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6  wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        {{ Form::open(array('url' => '/check-item')) }}
                             <div class="form-group">
                             {{ Form::label('name', 'Title') }}
                             {{ Form::text('name', null, array('class'=>'form-control')) }}
                             </div>
                             
                             <br />
                             
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
                             
                            <br />
                        {{ Form::submit('Add', array('class'=>'btn btn-default')) }}
                        
                        {{ Form::close() }}
                        
                </div><!--/.col-md-4-->
                <div class="col-md-4 col-sm-6">
                        <!-- right -->
                </div><!--/.col-md-4-->
            </div> <!--/.row-->
           
        </div><!--/.container-->
    </section><!--/#feature-->
@stop
