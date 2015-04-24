@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }}</h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>User ID</td>
            <td>First Name</td>
            <td>Last Name</td>
            <td>Gender</td>
            <td>Birthday</td>
            <td>Country</td>
            <td>About</td>
            <td>Image</td>
            <td>Favorite Item ID</td>
        </tr>
    </thead>
    <tbody>
    @foreach($profiles as $profile => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->user_id }}</td>
            <td>{{ $value->first_name }}</td>
            <td>{{ $value->last_name }}</td>
            <td>{{ $value->gender }}</td>
            <td>{{ $value->birthday }}</td>
            <td>{{ $value->country }}</td>
            <td>{{ $value->about }}</td>
            <td>{{ $value->image }}</td>
            <td>{{ $value->favorite_item_id }}</td>
            
            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                <!--
                {{ Form::open(array('url' => 'profiles/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Profile', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}
                -->
                
                <p><a  class="btn btn-danger" onclick="return confirm('Are you sure?')" href="/delete-user/{{ $value->user_id }}"> <i class="fa fa-exclamation-circle"></i> &nbsp; DELETE ACCOUNT</a></p>
                
                <!--
                Deleting profiles from database is not a good idea for now, delete account instead
                {{ Form::open(array('url' => 'profiles/' . $value->id, 'class' => 'pull-left')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Profile', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}
                -->

                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('profiles/' . $value->id) }}">Show this Profile</a>

                <!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
                <a class="btn btn-small btn-info" href="{{ URL::to('profiles/' . $value->id . '/edit') }}">Edit this Profile</a>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $profiles->links(); ?>

</div>

@stop