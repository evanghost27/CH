@extends('worldexpansion.layout')

@section('title')
    {{ $world->title }}
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

    <h1>{{ $world->title }}</h1>

    <div class="site-page-content parsed-text">
        {!! $world->parsed_text !!}
    </div>
@endsection
