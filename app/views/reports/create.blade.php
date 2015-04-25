@extends('layouts.master')

@section('content')

    <section id="recent-works">
            <div class="row">
                <div class="features">
                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="center wow">
                        <!-- left -->
                        
						<div class="item_category margin_top_20">
						<span title="Report"><i class="fa fa-exclamation-triangle fa-4x"></i></span>
						</div>
                        
                        </div>
                    </div><!--/.col-md-4-->

                    <div class="col-md-4 col-sm-6 wow" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="wow">
                        <!-- center -->
                        <h2>{{ $header }}</h2>
                        
                        <div id="bottom_line">
                        </div>
                        <p class="light_gray_font"><b>Report this {{ $item_type }}</b></p>
                        
                        @if(($item_type == "post") || ($item_type =="reply") || ($item_type == "review"))
                        <p class="highlight">
						<b>{{ $reported_user->username }} :</b>
                        {{ $item->body }}
						</p>
                        
                        @elseif($item_type == "item")
                        
                        <p class="highlight">
                        <b>{{ $item->name }} ({{ $item->year }})</b>
                        <br />
                        by {{ $item->creator }}
                        </p>
                        <div class="item_image highlight">
                        <a href="/items/{{ $item->id }}">{{ HTML::image("images/items/" . $item->image, 'item image') }}</a>
                        </div>
						
                        @elseif($item_type == "profile")
                        <p class="highlight">
                        <b>{{ $reported_user->username }}'s profile</b>
                        </p>
                        <div class="item_image highlight">
                        <a href="/profiles/{{ $item->id }}">{{ HTML::image("images/profiles/" . $item->image, 'item image') }}</a>
                        </div>
                        @endif
                        
                        <div id="bottom_line">
                        </div>
                        
                        <p class="light_gray_font"><b>Report for:</b></p>
                        
                        <!-- if there are creation errors, they will show here -->
                        <span class="form_messages">
                        {{ HTML::ul($errors->all()) }}
                        </span>
                        
                        <!-- form goes here -->
                        
                        {{ Form::open(array('url' => 'reports', 'files'=> true)) }}
                             
                             {{ Form::hidden('reported_user_id', $reported_user->id, array('id' => 'reported_user_id')) }}
                             
                             {{ Form::hidden('item_id', $item_id, array('id' => 'item_id')) }}
                             
                             {{ Form::hidden('item_type', $item_type, array('id' => 'item_type')) }}
                             
                             <div class="form-group">
                             @if($item_type == "item")
                             {{ Form::radio('reason', 'Duplicate', null, ['class' => '', 'id' => 'reason_1']) }}
                             {{ Form::label('reason_1', ' &nbsp; Duplicate (Another identical Item exists)') }}
                             <br />
                             <br />
                             
                             {{ Form::radio('reason', 'Wrong Information', null, ['class' => '', 'id' => 'reason_2']) }}
                             {{ Form::label('reason_2', ' &nbsp; Wrong Information') }}
                             <br />
                             <br />
                             @endif
                             {{ Form::radio('reason', 'Spam or Scam', null, ['class' => '', 'id' => 'reason_3']) }}
                             {{ Form::label('reason_3', ' &nbsp; Spam or Scam') }}
                             <br />
                             <br />
                             {{ Form::radio('reason', 'Hate speech', null, ['class' => '', 'id' => 'reason_4']) }}
                             {{ Form::label('reason_4', ' &nbsp; Hate speech') }}
                             <br />
                             <br />
                             {{ Form::radio('reason', 'Violence', null, ['class' => '', 'id' => 'reason_5']) }}
                             {{ Form::label('reason_5', ' &nbsp; Violence') }}
                             <br />
                             <br />
                             {{ Form::radio('reason', 'Pornographic content', null, ['class' => '', 'id' => 'reason_6']) }}
                             {{ Form::label('reason_6', ' &nbsp; Pornographic content') }}
                             <br />
                             <br />
                             {{ Form::radio('reason', 'Spoilers', null, ['class' => '', 'id' => 'reason_7']) }}
                             {{ Form::label('reason_7', ' &nbsp; Spoilers') }}

                             </div>
                             
                             <div id="bottom_line">
                             </div>
                             
                             {{ Form::submit('Submit', array('class'=>'btn btn-default')) }}
                        
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
