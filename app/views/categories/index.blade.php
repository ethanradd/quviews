@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }} | <a href="/categories/create">+ Add Category</a></h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Category Name</td>
            <td>Category Image</td>
            <td>Show Icon</td>
        </tr>
    </thead>
    <tbody>
    @foreach($categories as $category => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->name }}</td>
            <td>{{ $value->image }}</td>
            <td><span title="{{ $value->name }}"><i class="fa {{ $value->image }} fa-2x"></i></span></td>
            
            <!-- we will also add show, edit, and delete buttons -->
            <td>
                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                {{ Form::open(array('url' => 'categories/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Category', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}

                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <!--
                <a class="btn btn-small btn-success" href="{{ URL::to('categories/' . $value->id) }}">Show this Category</a>
                -->
                
                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('categories/' . $value->id . '/edit') }}">Edit this Category</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $categories->links(); ?>

</div>

@stop