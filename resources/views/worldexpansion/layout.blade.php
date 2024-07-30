@extends('layouts.app')

@section('title') 
    World :: 
    @yield('worldexpansion-title')
@endsection

@section('sidebar')
    @include('worldexpansion._sidebar')
@endsection

@section('content')
    @yield('worldexpansion-content')
@endsection

@section('scripts')
@parent
@endsection