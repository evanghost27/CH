@extends('admin.layout')

@section('admin-title')
    Concept
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Concept' => 'admin/world/concepts']) !!}

    <h1>Concept</h1>

    <p class="mb-0">From artistic concepts to planned storylines, concepts can be whatever you want! </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/world/concept-categories') }}"><i class="fas fa-folder mr-2"></i> Concept Categories</a>
        <a class="btn btn-primary" href="{{ url('admin/world/concepts/create') }}"><i class="fas fa-plus mr-2"></i> Create New Concept</a>
    </div>

    @if (!count($concepts))
        <p>No concept found.</p>
    @else
        <table class="table table-sm type-table">
            <tbody id="sortable" class="sortable">
                @foreach ($concepts as $concept)
                    <tr class="sort-item" data-id="{{ $concept->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            <a href={!! $concept->url !!}
                                @if ($concept->thumb_extension) data-toggle="tooltip" title="<img src='{{ $concept->thumbUrl }}' style='max-width:100px;' class='my-1'/><br> {{ ucfirst($concept->name) }} " @endif />{!! $concept->name !!}</a>
                            {{ $concept->summary ? '(' . $concept->summary . ')' : '' }}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/world/concepts/edit/' . $concept->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/world/concepts/sort']) !!}
            {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
            {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.handle').on('click', function(e) {
                e.preventDefault();
            });
            $("#sortable").sortable({
                items: '.sort-item',
                handle: ".handle",
                placeholder: "sortable-placeholder",
                stop: function(event, ui) {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                }
            });
            $("#sortable").disableSelection();
        });
    </script>
@endsection
