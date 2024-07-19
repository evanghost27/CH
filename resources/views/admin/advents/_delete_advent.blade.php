@if($advent)
    {!! Form::open(['url' => 'admin/data/advent-calendars/delete/'.$advent->id]) !!}

    <p>You are about to delete the advent calendar <strong>{{ $advent->name }}</strong>. This is not reversible. If users have participated in this advent calendar, you will not be able to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $advent->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Advent Calendar', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid advent calendar selected.
@endif
