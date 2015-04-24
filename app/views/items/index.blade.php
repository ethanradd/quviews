@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }} | <a href="/items/create">+ Add Item</a></h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Category ID</td>
            <td>Name</td>
            <td>Creator</td>
            <td>Year</td>
            <td>Last Editor ID</td>
            <td>Locked</td>
            <td>Image</td>
        </tr>
    </thead>
    <tbody>
    @foreach($items as $item => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->category_id }}</td>
            <td>{{ $value->name }}</td>
            <td>{{ $value->creator }}</td>
            <td>{{ $value->year }}</td>
            <td>{{ $value->last_editor_id }}</td>
            <td>{{ $value->locked }}</td>
            <td>{{ $value->image }}</td>

            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                {{ Form::open(array('url' => 'items/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Item', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}

                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('items/' . $value->id) }}">Show this Item</a>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('items/' . $value->id . '/edit') }}">Edit this Item</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $items->links(); ?>

</div>

@stop