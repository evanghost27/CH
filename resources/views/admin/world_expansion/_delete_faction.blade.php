@if($faction)
    {!! Form::open(['url' => 'admin/world/factions/delete/'.$faction->id]) !!}

    <p>
        You are about to delete the <strong>{!! $faction->style !!}</strong>? This is not reversible.
        If you would like to hide the faction from users, you can set it as inactive from the faction settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $faction->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Faction', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid faction selected.
@endif
