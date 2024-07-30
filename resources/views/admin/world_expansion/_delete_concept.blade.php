@if ($concept)
    {!! Form::open(['url' => 'admin/world/concepts/delete/' . $concept->id]) !!}

    <p>
        You are about to delete <strong>{!! $concept->name !!}</strong>? This is not reversible.
        If you would like to hide the concept from users, you can set it as inactive from the concept settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $concept->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Concept', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid concept selected.
@endif
