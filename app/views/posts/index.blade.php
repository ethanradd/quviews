@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }}</h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Item ID</td>
            <td>Author ID</td>
            <td>Body</td>
        </tr>
    </thead>
    <tbody>
    @foreach($posts as $post => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->item_id }}</td>
            <td>{{ $value->author_id }}</td>
            <td>{{ $value->body }}</td>
            
            <!-- we will also add show, edit, and delete buttons -->
            <td>
                
                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                
                {{ Form::open(array('url' => 'posts/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Post', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}
                
                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('posts/' . $value->id) }}">Show this Post</a>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('posts/' . $value->id . '/edit') }}">Edit this Post</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $posts->links(); ?>

</div>

@stop