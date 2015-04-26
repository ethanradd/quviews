@extends('layouts.master')

@section('content')

    <section id="recent-works">
        <div class="container">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>
                        {{ $header }}
                        </h2>
                        
                        <div id="bottom_line">
                        </div>
                        
                        @if (isset($items))
                        @foreach($items as $item)
                        
                        <h3 class="highlight">
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
                        <p class="highlight_red">If you are sure your item is <b>not</b> the same as the possible matches above, go ahead and add it</p>
                        @else
                        <p class="highlight_green">The item doesn't exists yet, you can go ahead and add it</p>
                        @endif
                        
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        
                        <!--
                        
                        {{ Form::open(array('url' => '/check-item')) }}
                             <div class="form-group">
                             {{ Form::label('name', 'Title') }}
                             {{ Form::text('name', $name, array('class'=>'form-control')) }}
                             </div>
                             
                             
                             @if(Auth::user()->role == "admin")
                             <div class="form-group">
                             {{ Form::label('category', '*Admin: Category - extra categories for admin') }}
                             {{ Form::select('category_id', array('1' => 'Movies', '2' => 'TV',
                             '3' => 'Music' ,'4' => 'Games','5' => 'Books','6' => 'Gadgets' ,
                             '7' => 'News*', '8' => 'Politics*', '9' => 'Entertainment*',
                             '10' => 'Fashion*', '12' => 'Sports*'), $category_id, array('class' => 'form-control')) }}
                             </div>
                             @else
                             <div class="form-group">
                             {{ Form::label('category', 'Category') }}
                             {{ Form::select('category_id', array('1' => 'Movies', '2' => 'TV', '3' => 'Music' ,'4' => 'Games','5' => 'Books','6' => 'Gadgets'), $category_id, array('class' => 'form-control')) }}
                             </div>
                             @endif
                        
                        {{ Form::submit('Add', array('class'=>'btn btn-default')) }}
                        
                        {{ Form::close() }}
                        
                        -->
                        
                        <!---------------------------------------------------------------->
                        <div id="bottom_line">
                        </div>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        
                        {{ Form::open(array('url' => 'items', 'files'=> true)) }}
                             <div class="form-group">
                             {{ Form::label('name', 'Title') }}
                             {{ Form::text('name', $name, array('class'=>'form-control')) }}
                             </div>
                             
                             @if(Auth::user()->role == "admin")
                             <div class="form-group">
                             {{ Form::label('category', '*Admin: Category - extra categories for admin') }}
                             {{ Form::select('category_id', array('1' => 'Movies', '2' => 'TV',
                             '3' => 'Music' ,'4' => 'Games','5' => 'Books','6' => 'Gadgets' ,
                             '7' => 'News*', '8' => 'Politics*', '9' => 'Entertainment*',
                             '10' => 'Fashion*', '12' => 'Sports*'), $category_id, array('class' => 'form-control')) }}
                             </div>
                             @else
                             <div class="form-group">
                             {{ Form::label('category', 'Category') }}
                             {{ Form::select('category_id', array('1' => 'Movies', '2' => 'TV', '3' => 'Music' ,'4' => 'Games','5' => 'Books','6' => 'Gadgets'), $category_id, array('class' => 'form-control')) }}
                             </div>
                             @endif
                             
                             <br />
                             <p class="light_gray_font"><b>Additional Details</b></p>
                             <div id="bottom_line">
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('creator_by', 'By: &nbsp;') }}
                             {{ Form::label('creator', '(name of Director / Network / Artist / Company / Author)', array('class'=>'light_gray_font')) }}
                             {{ Form::text('creator', '', array('class'=>'form-control')) }}
                             </div>
                             
                             <div class="form-group">
                             {{ Form::label('year', 'Release Year') }}
                             {{ Form::text('year', '', array('class'=>'form-control')) }}
                             </div>
                             
                             <br />
                             
                             <div class="form-group">
                            {{ Form::label('image', 'Add Item Image') }}
                            {{ Form::file('image') }}
                             </div>
                             
                             @if(Auth::user()->role == "admin")
                             <br />
                             <div class="form-group">
                             {{ Form::label('locked', '*Admin : Item Editable / Locked / Topic') }}
                             {{ Form::select('locked', array('editable' => 'Editable', 'locked' => 'Locked', 'topic' => 'Topic'), null, array('class' => 'form-control')) }}
                             </div>
                             @else
                             {{ Form::hidden('locked', "editable", array('id' => 'locked')) }}
                             @endif
                             
                             <br />
                             
                             <div class="form-group">
                             {{ Form::label('review', 'Your Quick Review') }}
                             {{ Form::textarea('review', '', array('class'=>'form-control chr-count', 'rows'=>'5', 'maxlength'=>'200')) }}
                             </div>
                             
                             <div id="rank" class="pull-right"></div>
                             <div class="form-group">
                             {{ Form::radio('rank', 'Good', null, ['class' => 'bgCollection', 'id' => 'good', 'onclick' => 'display_good_Result()']) }}
                             {{ Form::label('good', ' &nbsp; Good') }}
                             <br />
                             {{ Form::radio('rank', 'Eh', null, ['class' => 'bgCollection', 'id' => 'eh', 'onclick' => 'display_eh_Result()']) }}
                             {{ Form::label('eh', ' &nbsp; Eh') }}
                             <br />
                             {{ Form::radio('rank', 'Bad', null, ['class' => 'bgCollection', 'id' => 'bad', 'onclick' => 'display_bad_Result()']) }}
                             {{ Form::label('bad', ' &nbsp; Bad') }}
                             </div>
                             
                             {{ Form::submit('+ Add', array('class'=>'btn btn-default')) }}
                             <span class="countdown"></span>
                        
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
        </div><!--/.container-->
    </section>

@stop
