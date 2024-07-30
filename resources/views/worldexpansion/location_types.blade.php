@extends('worldexpansion.layout')

@section('title') Location Types @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Location Types' => 'world/location-types']) !!}
<h1>Location Types</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $types->render() !!}
<div class="row mx-0">
    @foreach($types as $type)
        <div class="col-12 col-md-4 mb-3"><div class="card mb-3 p-3 h-100">
            <div class="world-entry-image">
            @isset($type->thumb_extension) 
                <a href="{{ $type->thumbUrl }}" data-lightbox="entry" data-title="{{ $type->name }}"><img src="{{ $type->thumbUrl }}" class="world-entry-image mb-3 mw-100" /></a>
            @endisset
            </div>
            <h3>
                {!! $type->displayName !!} ({!! ucfirst($type->names) !!})
                @if(isset($type->searchUrl) && $type->searchUrl) <a href="{{ $type->searchUrl }}" class="world-entry-search text-muted float-right"><i class="fas fa-search"></i></a>  @endif
            </h3>
            <div class="world-entry-text">
                {!! $type->summary !!}
            </div>
        </div></div>
    @endforeach
</div>
{!! $types->render() !!}

<div class="text-center mt-4 small text-muted">{{ $types->total() }} result{{ $types->total() == 1 ? '' : 's' }} found.</div>

@endsection
