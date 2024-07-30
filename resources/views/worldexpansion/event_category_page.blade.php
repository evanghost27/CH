@extends('worldexpansion.layout')

@section('title') {{ $category->name }} Event @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Event Categories' => 'world/event-categories', $category->name => 'world/event-categories/'.$category->id]) !!}
<h1 ><img src="{{$category->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/> {!! $category->displayName !!}</h1>

@if($category->image_extension)
    <div class="text-center"><img src="{{$category->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@if(count($category->events))
<div class="text-center"><h5 class="mt-3 mb-0">{{ $category->name }} Event{{ count($category->events) == 0 ? '('.count($category->events).')' : 's ('.count($category->events).')'}}</h5>
    @foreach($category->events as $key => $event) <strong>{!! $event->displayName !!}</strong>@if($key != count($category->events)-1 && count($category->events)>2),@endif @if($key == count($category->events)-2) and @endif @endforeach
</div>
@else
    <h5 class="mt-3 mb-0 text-center">There aren't any {{ $category->name }} Events yet</h5>
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
