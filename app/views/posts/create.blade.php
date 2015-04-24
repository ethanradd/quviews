@extends('layouts.master')

@section('content')
    
    <section id="recent-works">
            <div class="container">
            <div class="row">
                <div class="features">
                    <div class="col-md-3 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        <div class="item_image">
                        <a href="/items/{{ $item->id }}">{{ HTML::image($image_path, 'item image') }}</a>
                        </div>
                        
                        </div>
                    </div><!--/.col-md-4-->
                    
                    <div class="col-md-6 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        
                        <div id="header_section">
                          <div id="header_img" class="text-center">
                            <h2>
                            <i class="fa {{ $category->image }} fa-2x"></i>
                            <br />
                            <span class="light_gray_font">{{ $category->name }}</span>
                            </h2>
                            
                          </div>
                          <div id="header_body" class="border_left">
                            <h2><span title="{{ $category->name }}">{{ $header }}</h2>
                          </div>
                        </div>
                        
                        <!-- no reviews for topics -->
                        @if($item->locked != "topic")
                        <a href="/reviews/create/{{ $item->id }}" title="Add your review" class="btn btn-review-add">+ Add Review</a>
                        <a href="/reviews/feed/{{ $item->id }}" title="Open Quick Reviews Feed" class="btn btn-review-feed">Read Reviews</a>
                        @endif
                        
                        <a href="/posts/create/{{ $item->id }}" title="Add new discussion Post" class="btn btn-selected"> + Add Post</a>
                        <a href="/posts/feed/{{ $item->id }}" title="Open Discussion Feed" class="btn btn-post-feed">Read Posts</a>
                        
                        <h2 class="margin_top_40">Add a Post</h2>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        {{ Form::open(array('url' => 'posts')) }}
                        
                             {{ Form::hidden('passed_item_id', $item->id, array('id' => 'passed_item_id')) }}
                             
                             <div class="form-group">
                             <!-- {{ Form::label('your post', 'Your Post' , array('class'=>'light_gray_font')) }} -->
                             {{ Form::textarea('body', '', array('class'=>'form-control chr-count', 'rows'=>'5', 'maxlength'=>'200')) }}
                             </div>
                             
                             {{ Form::submit('Post', array('class'=>'btn btn-default')) }}
                             <span class="countdown"></span>
                        
                        {{ Form::close() }}
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-3 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- right -->
                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.services-->
            </div><!--/.row-->
            </div><!--/.container-->
    </section>

@stop
