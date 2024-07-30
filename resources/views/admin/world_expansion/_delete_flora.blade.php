@if ($flora)
    {!! Form::open(['url' => 'admin/world/floras/delete/' . $flora->id]) !!}

    <p>
        You are about to delete <strong>{!! $flora->name !!}</strong>? This is not reversible.
        If you would like to hide the flora from users, you can set it as inactive from the flora settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $flora->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Flora', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid flora selected.
@endif
