@extends('admin.layout')

@section('admin-title') {{ucfirst(__('awards.award'))}} Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', ucfirst(__('awards.award')).' Categories' => 'admin/data/award-categories']) !!}

<h1>{{ucfirst(__('awards.award'))}} Categories</h1>

<p>This is a list of {{__('awards.award')}} categories that will be used to sort {{__('awards.awards')}} in the {{__('awards.awardcase')}}. Creating {{__('awards.award')}} categories is entirely optional, but recommended if you have a lot of {{__('awards.awards')}} in the game.</p>
<p>The sorting order reflects the order in which the {{__('awards.award')}} categories will be displayed in the {{__('awards.awardcase')}}, as well as on the world pages.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/award-categories/create') }}"><i class="fas fa-plus"></i> Create New {{ucfirst(__('awards.award'))}} Category</a></div>
@if(!count($categories))
    <p>No {{__('awards.award')}} categories found.</p>
@else
    <table class="table table-sm category-table">
        <tbody id="sortable" class="sortable">
            @foreach($categories as $category)
                <tr class="sort-award" data-id="{{ $category->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {!! $category->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/award-categories/edit/'.$category->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/data/award-categories/sort']) !!}
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
        awards: '.sort-award',
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
