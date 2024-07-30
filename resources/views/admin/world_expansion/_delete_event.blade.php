@if($event)
    {!! Form::open(['url' => 'admin/world/events/delete/'.$event->id]) !!}

    <p>
        You are about to delete <strong>{!! $event->name !!}</strong>? This is not reversible.
        If you would like to hide the event from users, you can set it as inactive from the event settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $event->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Event', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid event selected.
@endif