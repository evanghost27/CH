@extends('worldexpansion.layout')

@section('title') Event Categories @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Event Categories' => 'world/fauna-categories']) !!}
<h1>Event Categories</h1>

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

{!! $categories->render() !!}
<div class="row mx-0">
    @foreach($categories as $category)
        <div class="col-12 col-md-4 mb-3"><div class="card mb-3 p-3 h-100">
            <div class="world-entry-image">
            @isset($category->thumb_extension) 
                <a href="{{ $category->thumbUrl }}" data-lightbox="entry" data-title="{{ $category->name }}"><img src="{{ $category->thumbUrl }}" class="world-entry-image mb-3 mw-100" /></a>
            @endisset
            </div>
            <h3>
                {!! $category->displayName !!}
                @if(isset($category->searchUrl) && $category->searchUrl) <a href="{{ $category->searchUrl }}" class="world-entry-search text-muted float-right"><i class="fas fa-search"></i></a>  @endif
            </h3>
            <div class="world-entry-text">
                {!! $category->summary !!}
            </div>
        </div></div>
    @endforeach
</div>
{!! $categories->render() !!}

<div class="text-center mt-4 small text-muted">{{ $categories->total() }} result{{ $categories->total() == 1 ? '' : 's' }} found.</div>

@endsection
