@extends('admin.layout')

@section('admin-title') Fauna Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Fauna Categories' => 'admin/world/fauna-categories']) !!}

<div class="float-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/world/fauna-categories/create') }}"><i class="fas fa-plus mr-2"></i> Create New Fauna Category</a>
    <a class="btn btn-secondary" href="{{ url('admin/world/faunas') }}"><i class="fas fa-undo-alt mr-2"></i> Back to Fauna</a>
</div>
<h1>Fauna Categories</h1>

<p style="clear:both">Fauna categories are not required but may help in differentiating wild, domesticated, dangerous, etc.</p>


@if(!count($categories))
    <p>No fauna categories found.</p>
@else
    <table class="table table-sm category-table">
        <tbody id="sortable" class="sortable">
            @foreach($categories as $category)
                <tr class="sort-item" data-id="{{ $category->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        <a href={!! $category->url !!} @if($category->thumb_extension) data-toggle="tooltip" title="<img src='{{$category->thumbUrl}}' style='max-width:100px;'/><br> {{ucfirst($category->name)}} " @endif />{!! $category->name !!}</a>
                        {{ $category->summary ? '('.$category->summary.')' : '' }}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/world/fauna-categories/edit/'.$category->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/world/fauna-categories/sort']) !!}
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
