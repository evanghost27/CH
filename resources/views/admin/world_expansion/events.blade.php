@extends('admin.layout')

@section('admin-title') Events @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Events' => 'admin/world/events']) !!}

<h1>Events</h1>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/world/event-categories') }}"><i class="fas fa-folder mr-2"></i> Event Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/world/events/create') }}"><i class="fas fa-plus mr-2"></i> Create New Event</a>
</div>   
@if(!count($events))
    <p>No events found.</p>
@else 
    <table class="table table-sm type-table">
        <thead>
            <tr>
            <td class="font-weight-bold" style="width:25%;">
                Name
            </td>
            <td class="font-weight-bold" style="width:15%;">
                Category
            </td>
            <td class="font-weight-bold" >
                Summary
            </td>
            <td class="font-weight-bold" style="width:10%;">
                Start
            </td>
            <td class="font-weight-bold" style="width:10%;">
                End
            </td>
            <td></td>
            </tr>
        </thead>
        <tbody id="sortable" class="sortable">
            @foreach($events as $event)
                <tr class="sort-item" data-id="{{ $event->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        <a href={!! $event->url !!} @if($event->thumb_extension) data-toggle="tooltip" title="<img src='{{$event->thumbUrl}}' style='max-width:100px;' class='my-1'/><br> {{ucfirst($event->name)}} " @endif />{!! $event->name !!}</a>
                    </td>
                    <td>
                        {!! $event->category ? $event->category->displayName : '' !!}
                    </td>
                    <td>
                        {{ $event->summary ? $event->summary : '' }}
                    </td>
                    <td>
                        {!! $event->occur_start ? format_date($event->occur_start, false) : '' !!}
                    </td>
                    <td>
                        {!! $event->occur_end ? format_date($event->occur_end, false) : '' !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/world/events/edit/'.$event->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/world/events/sort']) !!}
        {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>

$( document ).ready(function() {
    $('.handle').on('click', function(e) {
        e.preventDefault();
    });
    $( "#sortable" ).sortable({
        items: '.sort-item',
        handle: ".handle",
        placeholder: "sortable-placeholder",
        stop: function( event, ui ) {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        },
        create: function() {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        }
    });
    $( "#sortable" ).disableSelection();
});
</script>
@endsection