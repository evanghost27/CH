@extends('admin.layout')

@section('admin-title')
    Glossary
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Glossary Terms' => 'admin/world/glossary']) !!}

    <div class="alert {{ Settings::get('WE_glossary') ? 'alert-success' : 'alert-warning' }} d-flex justify-content-between align-items-center">
        <span>
            The glossary is currently {{ Settings::get('WE_glossary') ? 'visible' : 'not visible' }}.
        </span>
        {!! Form::open(['url' => 'admin/world/glossary/toggle-setting']) !!}
        {!! Form::submit('Toggle', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>

    <h1>Glossary Terms</h1>

    <p class="mb-0">These are relatively short definitions of terms that will be displayed together on a searchable page for your users.</p>

    <div class="text-right mb-3 mt-2">
        <a class="btn btn-primary" href="{{ url('admin/world/glossary/create') }}"><i class="fas fa-plus mr-2"></i> Glossary Term</a>
    </div>

    @if (!count($glossaries))
        <p>No Glossary Terms found.</p>
    @else
        <table class="table table-sm type-table">
            <tbody>
                @foreach ($glossaries as $term)
                    <tr data-id="{{ $term->id }}">
                        <td>
                            {!! $term->displayName !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/world/glossary/edit/' . $term->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    @endif

@endsection

@section('scripts')
    @parent
@endsection
