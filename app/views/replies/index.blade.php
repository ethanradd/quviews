@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }}</h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Author ID</td>
            <td>Body</td>
            <td>Post ID</td>
            <td>Post Author ID</td>
            <td>Post Author Read</td>
            <td>Quote ID</td>
            <td>Quote Author ID</td>
            <td>Quote Author Read</td>
        </tr>
    </thead>
    <tbody>
    @foreach($replies as $reply => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->author_id }}</td>
            <td>{{ $value->body }}</td>
            <td>{{ $value->post_id }}</td>
            <td>{{ $value->post_author_id }}</td>
            <td>{{ $value->post_author_read }}</td>
            <td>{{ $value->quote_id }}</td>
            <td>{{ $value->quote_author_id }}</td>
            <td>{{ $value->quote_author_read }}</td>

            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                
                {{ Form::open(array('url' => 'replies/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Reply', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}
                
                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('replies/' . $value->id) }}">Show this Reply</a>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('replies/' . $value->id . '/edit') }}">Edit this Reply</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $replies->links(); ?>

</div>

@stop