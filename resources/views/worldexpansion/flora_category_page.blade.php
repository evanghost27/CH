@extends('worldexpansion.layout')

@section('title')
    {{ $category->name }} Flora
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Flora Categories' => 'world/flora-categories', $category->name => 'world/flora-categories/' . $category->id]) !!}
    <h1><img src="{{ $category->thumbUrl }}" style="max-height:25px;vertical-align:inherit;" /> {!! $category->displayName !!}</h1>

    @if ($category->image_extension)
        <div class="text-center"><img src="{{ $category->imageUrl }}" class="mw-100 mb-3" /></div>
    @endif

    @if (count($category->floras))
        <div class="text-center">
            <h5 class="mt-3 mb-0">All {{ $category->name }} Flora ({{ count($category->floras) }})</h5>
            @foreach ($category->floras as $key => $flora)
                <strong>{!! $flora->displayName !!}</strong>
                @if ($key != count($category->floras) - 1 && count($category->floras) > 2)
                    ,
                    @endif @if ($key == count($category->floras) - 2)
                        and
                    @endif
                @endforeach
        </div>
    @else
        <h5 class="mt-3 mb-0 text-center">There aren't any {{ $category->names }} yet</h5>
    @endif

    @isset($category->summary)
        <hr>
        <div class="text-center">{!! $category->summary !!}</div>
    @endisset

    @isset($category->parsed_description)
        <hr>
        <div class="world-entry-text">
            {!! $category->parsed_description !!}
        </div>
    @endisset
@endsection
