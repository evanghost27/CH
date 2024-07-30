@extends('worldexpansion.layout')

@section('title') {{ $location->style }} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Locations' => 'world/locations', $location->style => 'world/locations/'.$location->id, 'Gallery Submissions' => 'world/locations/'.$location->id.'/submissions']) !!}
<h1><img src="{{$location->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $location->fullDisplayNameUC !!}</h1>
<h5>{!! ucfirst($location->type->displayName) !!} {!! $location->parent ? 'inside '.$location->parent->displayName : '' !!}</h5>


@if($location->image_extension)
    <div class="text-center"><img src="{{$location->imageUrl}}" class="mw-100"/></div>
@endif

@isset($location->summary)
<div class="world-entry-text px-3 text-center">{!! $location->summary !!}</div>
@endisset



@if(count($submissions))
    <div class="d-flex align-content-around flex-wrap mb-2">
        @foreach($submissions->sortByDesc('created_at') as $submission)
            @include('galleries._thumb', ['submission' => $submission, 'gallery' => true])
        @endforeach
    </div>
{!! $submissions->render() !!}
@endif

<div class="text-center mt-4 small text-muted">{{ $submissions->total() }} result{{ $submissions->total() == 1 ? '' : 's' }} found.</div>


@endsection
