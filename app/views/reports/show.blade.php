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
						
                        <p class="light_gray_font">
						<b>Reported Item: {{ $report->item_type }}</b>
						<small class="pull-right">
                        </small>
						</p>
                        
                        @if(($report->item_type == "post") || ($report->item_type =="reply") || ($report->item_type == "review"))
                        <p class="highlight">
						<b>{{ $reported_user->username }} :</b>
                        {{ $item->body }}
						</p>
                        
                        @elseif($report->item_type == "item")
                        
                        <p class="highlight">
                        <b>{{ $item->name }} ({{ $item->year }})</b>
                        <br />
                        by {{ $item->creator }}
                        </p>
                        <div class="item_image highlight">
                        <a href="/items/{{ $item->id }}">{{ HTML::image("images/items/" . $item->image, 'item image') }}</a>
                        </div>
						
                        @elseif($report->item_type == "profile")
                        <p class="highlight">
                        <b>{{ $reported_user->username }}'s profile</b>
                        </p>
                        <div class="item_image highlight">
                        <a href="/profiles/{{ $item->id }}">{{ HTML::image("images/profiles/" . $item->image, 'item image') }}</a>
                        </div>
                        @endif
						
						<br />
						
						<p class="light_gray_font"><b>Reason</b></p>
						<p class="highlight_red">{{ $report->reason }}</p>
						
						<br />
						
						<h2>Admin Action</h2>
								
		                <div id="bottom_line">
                        </div>
								
						<a class="btn btn-small btn-danger" href="/boss/change-user-role/{{ $reported_user->id }}" target="_blank">CHANGE USER ROLE (BAN)</a>
						
						<br />
						<br />
						
						<a title="Go"  target="_blank" class="btn btn-small btn-default" href="{{ $direct_link }}"><i class="fa fa-arrow-circle-o-right"></i> &nbsp; Go To {{ $report->item_type }}</a>
						
						&nbsp;
							
						<a title="Go"  target="_blank" class="btn btn-small btn-default" href="/profiles/{{ $reported_user_profile->id }}"><i class="fa fa-arrow-circle-o-right"></i> &nbsp; Go To Reported User</a>
								
						&nbsp;
							
						<br />
						<br />
						
						<a title="Go"  target="_blank" class="btn btn-small btn-success" href="/profiles/{{ $author_profile->id }}"><i class="fa fa-arrow-circle-o-right"></i> &nbsp; Go To Reporting User</a>
						
						<h2>Record Admin Action</h2>
						
                        <div id="bottom_line">
                        </div>
								
						{{ Form::model($report, array('route' => array('reports.update', $report->id), 'method' => 'PUT')) }}
							 
                             <div class="form-group">
                             {{ Form::label('action', 'This description will messaged to reported user', array('class'=>'light_gray_font')) }}
							 <br />
							 <br />
                             {{ Form::textarea('action', null, array('class'=>'form-control chr-count', 'rows'=>'5', 'maxlength'=>'200')) }}
                             </div>
								
							 {{ Form::submit('Save', array('class'=>'btn btn-default')) }}
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
    </section>

@stop