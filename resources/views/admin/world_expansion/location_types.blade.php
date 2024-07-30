@extends('admin.layout')

@section('admin-title') Location types @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Location types' => 'admin/world/location-types']) !!}

<div class="float-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/world/location-types/create') }}"><i class="fas fa-plus mr-2"></i> Create New Location Type</a>
    <a class="btn btn-secondary" href="{{ url('admin/world/locations') }}"><i class="fas fa-undo-alt mr-2"></i> Back to Locations</a>
</div>
<h1>Location types</h1>

<p class="mb-0" style="clear:both">Location types are effectively categories for locations - but mostly for organization and display. <strong>eg. Country, Continent, Island.</strong></p>
<p>The sorting order reflects the order in which the types will be listed on the location type index.</p>

@if(!count($types))
    <p>No location types found.</p>
@else
    <table class="table table-sm type-table">
        <tbody id="sortable" class="sortable">
            @foreach($types as $type)
                <tr class="sort-item" data-id="{{ $type->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        <a href={!! $type->url !!} @if($type->thumb_extension) data-toggle="tooltip" title="<img src='{{$type->thumbUrl}}' style='max-width:100px;'/><br> {{ucfirst($type->name)}} " @endif />{!! $type->name !!}</a>
                        ({!! $type->names !!})
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/world/location-types/edit/'.$type->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/world/location-types/sort']) !!}
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
