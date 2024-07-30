@if ($category)
    {!! Form::open(['url' => 'admin/world/event-categories/delete/' . $category->id]) !!}

    <p>
        You are about to delete the event category <strong>{{ $category->name }}</strong>. This is not reversible.
    </p>
    <p>
        If you would like to hide the category from users, you can set it as inactive from the event category settings page.
    </p>

    @if (count($category->events))
        <div class="alert alert-danger">
            <h5>If you delete this category, you will also delete: </h5>
            @foreach ($category->events as $key => $event)
                <strong>{!! $event->displayName !!}</strong>
                @if ($key != count($category->events) - 1 && count($category->events) > 2)
                    ,
                    @endif @if ($key == count($category->events) - 2)
                        and
                    @endif
                @endforeach.
        </div>
    @endif

    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Event Category', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid event category selected.
@endif
