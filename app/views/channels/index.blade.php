@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }} | <a href="/channels/create">+ Add Channel</a></h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Name</td>
            <td>Category ID</td>
            <td>Source</td>
            <td>Description</td>
            <td>Country</td>
            <td>Live</td>
            <td>Image</td>
        </tr>
    </thead>
    <tbody>
    @foreach($channels as $channel => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->name }}</td>
            <td>{{ $value->category_id }}</td>
            <td>in - database</td>
            <td>{{ $value->description }}</td>
            <td>{{ $value->country }}</td>
            <td>{{ $value->live }}</td>
            <td>{{ $value->image }}</td>

            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                {{ Form::open(array('url' => 'channels/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Channel', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}
                
                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('channels/' . $value->id) }}">Show this Channel</a>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('channels/' . $value->id . '/edit') }}">Edit this Channel</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $channels->links(); ?>

</div>

@stop