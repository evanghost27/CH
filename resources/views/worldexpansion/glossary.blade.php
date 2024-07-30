@extends('worldexpansion.layout')

@section('title') Glossary @endsection

@section('content')
{!! breadcrumbs(['Lore' => 'info', 'Glossary' => 'info/glossary']) !!}
<h1>Glossary</h1>
<p>This page contains quick definitions of commonly used terms. If a term has a longer lore page, it will be linked via the name of the term</p>

<div class="mb-4">
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                ], Request::get('sort') ? : 'alpha', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $terms->render() !!}
@foreach($terms as $term)
    <div class="term">
        <h6>
            {!! $term->displayName !!}
            @if(Auth::check() && Auth::user()->hasPower('manage_world'))
                <a data-toggle="tooltip" title="[ADMIN] Edit Term" href="{{ url('admin/world/glossary/edit/').'/'.$term->id }}" class="float-right"><i class="fas fa-crown"></i></a>
            @endif
        </h6>
        <div class="parsed-text mt-2 pl-3 py-2" style="border-left: 4px solid lightgrey">{!! $term->parsed_description !!}</div>
    </div>
    <hr class="my-4 w-75"/>
@endforeach
{!! $terms->render() !!}

<div class="text-center mt-4 small text-muted">{{ $terms->total() }} result{{ $terms->total() == 1 ? '' : 's' }} found.</div>

@endsection


@section('scripts')
<style>
     .parsed-text p:last-child, .parsed-text ol:last-child, .parsed-text ul:last-child {
       margin-bottom: 0; 
    }
    
    .parsed-text ul, .parsed-text ol {
        padding-left: 0;
        list-style-position: inside;
    }
    
    .parsed-text li {
        padding-bottom: 10px;
    }
</style>
@endsection