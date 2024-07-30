@if($type)
    {!! Form::open(['url' => 'admin/world/location-types/delete/'.$type->id]) !!}

    <p>
        You are about to delete the location type <strong>{{ $type->name }}</strong>. This is not reversible.
    </p>

    <p>
        If you would like to hide the type from users, you can set it as inactive from the location type settings page.
    </p>

    @if(count($type->locations))
        <div class="alert alert-danger">
            <h5>If you delete this, you will also delete: </h5>
            @foreach($type->locations as $key => $location) <strong>{!! $location->displayName !!}</strong>@if($key != count($type->locations)-1 && count($type->locations)>2),@endif @if($key == count($type->locations)-2) and @endif @endforeach.
        </div>
    @endif

    <p>Are you sure you want to delete <strong>{{ $type->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Location Type', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid location type selected.
@endif