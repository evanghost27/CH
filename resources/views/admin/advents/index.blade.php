@extends('admin.layout')

@section('admin-title') Advent Calendars @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Advent Calendars' => 'admin/data/advent-calendars']) !!}

<h1>Advent Calendars</h1>

<p>This is a list of advent calendars.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/advent-calendars/create') }}"><i class="fas fa-plus"></i> Create New Advent Calendar</a></div>

@if(!count($advents))
    <p>No advent calendars found.</p>
@else
    {!! $advents->render() !!}

    <div class="row ml-md-2">
      <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-4 col-md-1 font-weight-bold">Active</div>
        <div class="col-4 col-md-3 font-weight-bold">Name</div>
        <div class="col-4 col-md-3 font-weight-bold">Display Name</div>
        <div class="col-4 col-md-2 font-weight-bold">Start</div>
        <div class="col-4 col-md-2 font-weight-bold">End</div>
      </div>
      @foreach($advents as $advent)
      <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-2 col-md-1">
          {!! $advent->isActive ? '<i class="text-success fas fa-check"></i>' : '' !!}
        </div>
        <div class="col-5 col-md-3 text-truncate">
          {{ $advent->name }}
        </div>
        <div class="col-5 col-md-3">
          {!! $advent->displayLink !!}
        </div>
        <div class="col-4 col-md-2">
          {!! pretty_date($advent->start_at) !!}
        </div>
        <div class="col-4 col-md-2">
          {!! pretty_date($advent->end_at) !!}
        </div>
        <div class="col-3 col-md-1 text-right">
          <a href="{{ url('admin/data/advent-calendars/edit/'.$advent->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
        </div>
      </div>
      @endforeach
    </div>

    {!! $advents->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection
