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
            <td>Rank</td>
        </tr>
    </thead>
    <tbody>
    @foreach($reviews as $review => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->item_id }}</td>
            <td>{{ $value->author_id }}</td>
            <td>{{ $value->body }}</td>
            <td>{{ $value->rank }}</td>
            
            <!-- we will also add show, edit, and delete buttons -->
            <td>
                
                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                
                {{ Form::open(array('url' => 'reviews/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Review', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}
                
                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('reviews/' . $value->id) }}">Show this Review</a>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('reviews/' . $value->id . '/edit') }}">Edit this Review</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $reviews->links(); ?>

</div>

@stop