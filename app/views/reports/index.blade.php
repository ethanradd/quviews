@extends('layouts.master')

@section('content')

<div class="container">


<h2>{{ $header }}</h2>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <td>ID</td>
            <td>Author ID</td>
            <td>Reported User ID</td>
            <td>Reason</td>
            <td>Item ID</td>
            <td>Item Type</td>
            <td>Resolved</td>
            <td>Action</td>
            <td>Admin ID</td>
        </tr>
    </thead>
    <tbody>
    @foreach($reports as $report => $value)
        <tr>
            <td>{{ $value->id }}</td>
            <td>{{ $value->author_id }}</td>
            <td>{{ $value->reported_user_id }}</td>
            <td>{{ $value->reason }}</td>
            <td>{{ $value->item_id }}</td>
            <td>{{ $value->item_type }}</td>
            <td>{{ $value->resolved }}</td>
            <td>{{ $value->action }}</td>
            <td>{{ $value->admin_id }}</td>

            <!-- we will also add show, edit, and delete buttons -->
            <td>

                <!-- delete the nerd (uses the destroy method DESTROY /nerds/{id} -->
                <!-- we will add this later since its a little more complicated than the other two buttons -->
                {{ Form::open(array('url' => 'reports/' . $value->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit('Delete this Report', array('class' => 'btn btn-warning', 'onclick' => "return confirm('Are you sure?')")) }}
                {{ Form::close() }}

                <!-- show the nerd (uses the show method found at GET /nerds/{id} -->
                <a class="btn btn-small btn-success" href="{{ URL::to('reports/' . $value->id) }}">Show this Report</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<?php echo $reports->links(); ?>

</div>

@stop