@if ($term)
    {!! Form::open(['url' => 'admin/world/glossary/delete/' . $term->id]) !!}

    <p>
        You are about to delete <strong>{!! $term->name !!}</strong>? This is not reversible.
        If you would like to hide the term from users, you can set it as inactive from the event settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $term->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Term', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid event selected.
@endif
