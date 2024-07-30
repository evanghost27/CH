@extends('admin.layout')

@section('admin-title') Fauna @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Fauna' => 'admin/world/faunas']) !!}

<h1>Fauna</h1>

<p class="mb-0">Fauna are the animals of your world. </p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/world/fauna-categories') }}"><i class="fas fa-folder mr-2"></i> Fauna Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/world/faunas/create') }}"><i class="fas fa-plus mr-2"></i> Create New Fauna</a>
</div> 

@if(!count($faunas))
    <p>No fauna found.</p>
@else 
    <table class="table table-sm type-table">
        <tbody id="sortable" class="sortable">
            @foreach($faunas as $fauna)
                <tr class="sort-item" data-id="{{ $fauna->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        <a href={!! $fauna->url !!} @if($fauna->thumb_extension) data-toggle="tooltip" title="<img src='{{$fauna->thumbUrl}}' style='max-width:100px;' class='my-1'/><br> {{ucfirst($fauna->name)}} " @endif />{!! $fauna->name !!}</a>
                        {{ $fauna->summary ? '('.$fauna->summary.')' : '' }}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/world/faunas/edit/'.$fauna->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/world/faunas/sort']) !!}
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