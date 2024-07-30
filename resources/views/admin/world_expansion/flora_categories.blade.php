@extends('admin.layout')

@section('admin-title') Flora Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Flora Categories' => 'admin/world/flora-categories']) !!}

<div class="float-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/world/flora-categories/create') }}"><i class="fas fa-plus mr-2"></i> Create New Flora Category</a>
    <a class="btn btn-secondary" href="{{ url('admin/world/floras') }}"><i class="fas fa-undo-alt mr-2"></i> Back to Flora</a>
</div>
<h1>Flora Categories</h1>

<p style="clear:both">Flora categories are not required but may help in differentiating native, flowering, etc.</p>


@if(!count($categories))
    <p>No flora categories found.</p>
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
                        <a href="{{ url('admin/world/flora-categories/edit/'.$category->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/world/flora-categories/sort']) !!}
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
