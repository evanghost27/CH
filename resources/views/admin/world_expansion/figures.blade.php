@extends('admin.layout')

@section('admin-title')
    Figures
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Figures' => 'admin/world/figures']) !!}


    <div class="float-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/world/figure-categories') }}"><i class="fas fa-folder mr-2"></i> Figure Categories</a>
        <a class="btn btn-primary" href="{{ url('admin/world/figures/create') }}"><i class="fas fa-plus mr-2"></i> Create New Figure</a>
    </div>
    <h1>Figures</h1>

    <p class="mb-0" style="clear:both;">Figures are individuals who do not fit in the regular Character setup.</p>

    @if (!count($figures))
        <p>No figures found.</p>
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
                    <td class="font-weight-bold">
                        Summary
                    </td>
                    <td></td>
                </tr>
            </thead>
            <tbody id="sortable" class="sortable">
                @foreach ($figures as $figure)
                    <tr class="sort-item" data-id="{{ $figure->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            <a href={!! $figure->url !!}
                                @if ($figure->thumb_extension) data-toggle="tooltip" title="<img src='{{ $figure->thumbUrl }}' style='max-width:100px;' class='my-1'/><br> {{ ucfirst($figure->name) }} " @endif />{!! $figure->name !!}</a>
                        </td>
                        <td>
                            {!! $figure->category ? $figure->category->displayName : '' !!}
                        </td>
                        <td>
                            {{ $figure->summary ? $figure->summary : '' }}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/world/figures/edit/' . $figure->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/world/figures/sort']) !!}
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
                e.prfigureDefault();
            });
            $("#sortable").sortable({
                items: '.sort-item',
                handle: ".handle",
                placeholder: "sortable-placeholder",
                stop: function(figure, ui) {
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
