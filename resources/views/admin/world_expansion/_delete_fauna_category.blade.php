@if ($category)
    {!! Form::open(['url' => 'admin/world/fauna-categories/delete/' . $category->id]) !!}

    <p>
        You are about to delete the fauna category <strong>{{ $category->name }}</strong>. This is not reversible.
    </p>
    <p>
        If you would like to hide the category from users, you can set it as inactive from the fauna category settings page.
    </p>

    @if (count($category->faunas))
        <div class="alert alert-danger">
            <h5>If you delete this category, you will also delete: </h5>
            @foreach ($category->faunas as $key => $fauna)
                <strong>{!! $fauna->displayName !!}</strong>
                @if ($key != count($category->faunas) - 1 && count($category->faunas) > 2)
                    ,
                    @endif @if ($key == count($category->faunas) - 2)
                        and
                    @endif
                @endforeach.
        </div>
    @endif

    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Fauna Category', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid fauna category selected.
@endif
