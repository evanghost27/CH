@extends('worldexpansion.layout')

@section('title') {{ $category->name }} Fauna @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Fauna Categories' => 'world/fauna-categories', $category->name => 'world/fauna-categories/'.$category->id]) !!}
<h1 ><img src="{{$category->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/> {!! $category->displayName !!}</h1>

@if($category->image_extension)
    <div class="text-center"><img src="{{$category->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@if(count($category->faunas))
<div class="text-center"><h5 class="mt-3 mb-0">All {{ $category->name }} Fauna ({{count($category->faunas)}})</h5>
    @foreach($category->faunas as $key => $fauna) <strong>{!! $fauna->displayName !!}</strong>@if($key != count($category->faunas)-1 && count($category->faunas)>2),@endif @if($key == count($category->faunas)-2) and @endif @endforeach
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
