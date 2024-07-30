@if($type)
    {!! Form::open(['url' => 'admin/world/faction-types/delete/'.$type->id]) !!}

    <p>
        You are about to delete the faction type <strong>{{ $type->name }}</strong>. This is not reversible.
    </p>

    <p>
        If you would like to hide the type from users, you can set it as inactive from the faction type settings page.
    </p>

    @if(count($type->factions))
        <div class="alert alert-danger">
            <h5>If you delete this, you will also delete: </h5>
            @foreach($type->factions as $key => $faction) <strong>{!! $faction->displayName !!}</strong>@if($key != count($type->factions)-1 && count($type->factions)>2),@endif @if($key == count($type->factions)-2) and @endif @endforeach.
        </div>
    @endif

    <p>Are you sure you want to delete <strong>{{ $type->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Faction Type', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid faction type selected.
@endif
