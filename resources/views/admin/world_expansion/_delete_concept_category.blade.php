@if ($category)
    {!! Form::open(['url' => 'admin/world/concept-categories/delete/' . $category->id]) !!}

    <p>
        You are about to delete the concept category <strong>{{ $category->name }}</strong>. This is not reversible.
    </p>
    <p>
        If you would like to hide the category from users, you can set it as inactive from the concept category settings page.
    </p>

    @if (count($category->concepts))
        <div class="alert alert-danger">
            <h5>If you delete this category, you will also delete: </h5>
            @foreach ($category->concepts as $key => $concept)
                <strong>{!! $concept->displayName !!}</strong>
                @if ($key != count($category->concepts) - 1 && count($category->concepts) > 2)
                    ,
                    @endif @if ($key == count($category->concepts) - 2)
                        and
                    @endif
                @endforeach.
        </div>
    @endif

    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Concept Category', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid concept category selected.
@endif
