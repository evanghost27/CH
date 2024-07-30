@extends('admin.layout')

@section('admin-title')
    Factions
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Factions' => 'admin/world/factions']) !!}


    <div class="float-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/world/faction-types') }}"><i class="fas fa-folder mr-2"></i> Faction Types</a>
        @if (count($types))
            <a class="btn btn-primary" href="{{ url('admin/world/factions/create') }}"><i class="fas fa-plus mr-2"></i> Create New Faction</a>
        @endif
    </div>
    <h1>Factions</h1>

    <p class="mb-0" style="clear:both;">Factions are specific organizations within your world.</p>
    <p>The sorting order reflects the order in which the factions will be listed on the faction index.</p>


    @if (!count($types))
        <div class="alert alert-warning">You will need to create a faction type before you can create any factions, as type is required.</div>
    @endif
    @if (!count($factions))
        <p>No factions found.</p>
    @else
        <table class="table table-sm type-table">
            <tbody id="sortable" class="sortable">
                @foreach ($factions as $faction)
                    <tr class="sort-item" data-id="{{ $faction->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            <a href={!! $faction->url !!}
                                @if ($faction->thumb_extension) data-toggle="tooltip" title="<img src='{{ $faction->thumbUrl }}' style='max-width:100px;' class='my-1'/><br> {{ ucfirst($faction->style) }} " @endif />{!! $faction->name !!}</a>
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/world/factions/edit/' . $faction->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/world/factions/sort']) !!}
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
