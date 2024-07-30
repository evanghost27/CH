@if($category)
    {!! Form::open(['url' => 'admin/world/flora-categories/delete/'.$category->id]) !!}

    <p>
        You are about to delete the flora category <strong>{{ $category->name }}</strong>. This is not reversible.
    </p>
    <p>
        If you would like to hide the category from users, you can set it as inactive from the flora category settings page.
    </p>

    @if(count($category->floras))
    <div class="alert alert-danger">
        <h5>If you delete this category, you will also delete: </h5>
        @foreach($category->floras as $key => $flora) <strong>{!! $flora->displayName !!}</strong>@if($key != count($category->floras)-1 && count($category->floras)>2),@endif @if($key == count($category->floras)-2) and @endif @endforeach.
    </div>
    @endif

    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Flora Category', ['class' => 'btn btn-danger w-100']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid flora category selected.
@endif