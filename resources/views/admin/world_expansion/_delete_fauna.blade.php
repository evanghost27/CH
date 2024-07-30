@if($fauna)
    {!! Form::open(['url' => 'admin/world/faunas/delete/'.$fauna->id]) !!}

    <p>
        You are about to delete <strong>{!! $fauna->name !!}</strong>? This is not reversible.
        If you would like to hide the fauna from users, you can set it as inactive from the fauna settings page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $fauna->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Fauna', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid fauna selected.
@endif