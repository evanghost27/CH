@extends('worldexpansion.layout')

@section('title') {{ $type->name }} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Factions Types' => 'world/faction-types', $type->names => 'world/factions/'.$type->id]) !!}
<h1 ><img src="{{$type->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $type->displayName !!} ({!! $type->names !!})</h1>

@if($type->image_extension)
    <div class="text-center"><img src="{{$type->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@isset($type->summary)
<div class="world-entry-text px-3 text-center">{!! $type->summary !!}</div>
@endisset

<div class="row mx-0 px-0 mt-3">
    @if(count($type->factions))
    <div class="text-center col-md mb-3"><div class="card h-100 py-3">

    <h5 class="mb-0">All {{ $type->names }} ({{count($type->factions)}})</h5>
    <hr>
        @foreach($type->factions->groupBy('type_id') as $group => $factions)
            @foreach($factions as $key => $child)
                <p class="mb-0">
                    <strong>{!! $child->fullDisplayNameUC !!}</strong> @if($child->parent) (part of {!! $child->parent->fullDisplayName !!}) @endif
                </p>
            @endforeach
        @endforeach

    </div></div>
    @else
        <h5 class="mt-3 mb-0 text-center col-12">There aren't any {{ $type->names }} yet</h5>
    @endif
</div>

@isset($type->parsed_description)
    <div class="world-entry-text px-3">
        {!! $type->parsed_description !!}
    </div>
@endisset



@endsection
