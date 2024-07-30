@extends('worldexpansion.layout')

@section('title')
    {{ $category->name }} Concept
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Concept Categories' => 'world/concept-categories', $category->name => 'world/concept-categories/' . $category->id]) !!}
    <h1><img src="{{ $category->thumbUrl }}" style="max-height:25px;vertical-align:inherit;" /> {!! $category->displayName !!}</h1>

    @if ($category->image_extension)
        <div class="text-center"><img src="{{ $category->imageUrl }}" class="mw-100 mb-3" /></div>
    @endif

    @if (count($category->concepts))
        <div class="text-center">
            <h5 class="mt-3 mb-0">All {{ $category->name }} Concept ({{ count($category->concepts) }})</h5>
            @foreach ($category->concepts as $key => $concept)
                <strong>{!! $concept->displayName !!}</strong>
                @if ($key != count($category->concepts) - 1 && count($category->concepts) > 2)
                    ,
                    @endif @if ($key == count($category->concepts) - 2)
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
