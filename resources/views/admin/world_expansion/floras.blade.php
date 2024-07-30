@extends('admin.layout')

@section('admin-title')
    Flora
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Flora' => 'admin/world/floras']) !!}

    <h1>Flora</h1>

    <p class="mb-0">Flora are the plants of your world. </p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/world/flora-categories') }}"><i class="fas fa-folder mr-2"></i> Flora Categories</a>
        <a class="btn btn-primary" href="{{ url('admin/world/floras/create') }}"><i class="fas fa-plus mr-2"></i> Create New Flora</a>
    </div>
    @if (!count($floras))
        <p>No flora found.</p>
    @else
        <table class="table table-sm type-table">
            <tbody id="sortable" class="sortable">
                @foreach ($floras as $flora)
                    <tr class="sort-item" data-id="{{ $flora->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            <a href={!! $flora->url !!}
                                @if ($flora->thumb_extension) data-toggle="tooltip" title="<img src='{{ $flora->thumbUrl }}' style='max-width:100px;' class='my-1'/><br> {{ ucfirst($flora->name) }} " @endif />{!! $flora->name !!}</a>
                            {{ $flora->summary ? '(' . $flora->summary . ')' : '' }}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/world/floras/edit/' . $flora->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/world/floras/sort']) !!}
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
