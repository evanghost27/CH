@extends('admin.layout')

@section('admin-title') Location types @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Locations' => 'admin/world/locations']) !!}

<div class="float-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/world/location-types') }}"><i class="fas fa-folder mr-2"></i> Location Types</a>
    @if(count($types))
        <a class="btn btn-primary" href="{{ url('admin/world/locations/create') }}"><i class="fas fa-plus mr-2"></i> Create New Location</a>
    @endif
</div>
<h1>Locations</h1>

<p class="mb-0" style="clear:both;">Locations are specific areas of your world. <strong>eg. Canada, Europe, Las Vegas.</strong></p>
<p>The sorting order reflects the order in which the locations will be listed on the location index.</p>


@if(!count($types))
    <div class="alert alert-warning">You will need to create a location type before you can create any locations, as type is required.</div>
@endif

@if(!count($locations))
    <p>No locations found.</p>
@else
    <table class="table table-sm type-table">
        <tbody id="sortable" class="sortable">
            @foreach($locations as $location)
                <tr class="sort-item" data-id="{{ $location->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        <a href={!! $location->url !!} @if($location->thumb_extension) data-toggle="tooltip" title="<img src='{{$location->thumbUrl}}' style='max-width:100px;' class='my-1'/><br> {{ucfirst($location->style)}} " @endif />{!! $location->name !!}</a>
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/world/locations/edit/'.$location->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/world/locations/sort']) !!}
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
