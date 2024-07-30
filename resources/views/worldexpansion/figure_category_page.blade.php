@extends('worldexpansion.layout')

@section('title') {{ $category->name }} Figure @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Figure Categories' => 'world/figure-categories', $category->name => 'world/figure-categories/'.$category->id]) !!}
<h1 ><img src="{{$category->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/> {!! $category->displayName !!}</h1>

@if($category->image_extension)
    <div class="text-center"><img src="{{$category->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@if(count($category->figures))
<div class="text-center"><h5 class="mt-3 mb-0">{{ $category->name }} Figure{{ count($category->figures) == 0 ? '('.count($category->figures).')' : 's ('.count($category->figures).')'}}</h5>
    @foreach($category->figures as $key => $figure) <strong>{!! $figure->displayName !!}</strong>@if($key != count($category->figures)-1 && count($category->figures)>2),@endif @if($key == count($category->figures)-2) and @endif @endforeach
</div>
@else
    <h5 class="mt-3 mb-0 text-center">There aren't any {{ $category->name }} Figures yet</h5>
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
