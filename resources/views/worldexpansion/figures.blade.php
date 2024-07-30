@extends('worldexpansion.layout')

@section('title') Figures @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Figures' => 'world/figures']) !!}
<h1>Figures</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('type_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                    'category'          => 'Sort by Category',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $figures->render() !!}
<div class="row mx-0">
    @foreach($figures as $figure)
        <div class="col-12 col-md-4 mb-3"><div class="card h-100 text-center">
            <div class="card-header">
                <div class="world-entry-image">
                @isset($figure->thumb_extension)
                    <a href="{{ $figure->thumbUrl }}" data-lightbox="entry" data-title="{{ $figure->name }}"><img src="{{ $figure->thumbUrl }}" class="world-entry-image mb-3 mw-100" /></a>
                @endisset
                </div>
                <h3 class="mb-0">{!! $figure->displayName !!}</h3>
                <p class="mb-0">{!! $figure->category ? $figure->category->displayName : '' !!}{!! $figure->faction ? ' ・ '.ucfirst($figure->faction->displayName) : '' !!}</p>
            </div>

            @if(count(allAttachments($figure)))
                <div class="card-body">
                    @foreach(allAttachments($figure) as $type => $attachments)
                        <p class="text-center mb-0">Associated with {{ count($attachments) }} {{ strtolower($type) }}{{ count($attachments) == 1 ? '' : 's' }}.</p>
                    @endforeach
                </div>
            @endif

            @isset($figure->summary)
                <div class="card-footer mt-auto">
                    <p class="mb-0"> {!! $figure->summary !!}</p>
                </div>
            @endisset

        </div></div>
    @endforeach
</div>
{!! $figures->render() !!}

<div class="text-center mt-4 small text-muted">{{ $figures->total() }} result{{ $figures->total() == 1 ? '' : 's' }} found.</div>

@endsection
