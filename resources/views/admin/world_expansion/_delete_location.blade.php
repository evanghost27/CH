@if($location)
    {!! Form::open(['url' => 'admin/world/locations/delete/'.$location->id]) !!}

    <p>
        You are about to delete the <strong>{!! $location->style !!}</strong>? This is not reversible.
        If you would like to hide the location from users, you can set it as inactive from the location settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $location->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Location', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid location selected.
@endif