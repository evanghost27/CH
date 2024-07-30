@extends('worldexpansion.layout')

@section('title') Figure :: {{ $figure->name }} @endsection

@section('content')

@if(Auth::check() && Auth::user()->hasPower('manage_world'))
    <a data-toggle="tooltip" title="[ADMIN] Edit Figure" href="{{ url('admin/world/figures/edit/').'/'.$figure->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
@endif
{!! breadcrumbs(['World' => 'world', 'Figure' => 'world/figures', $figure->name => 'world/figures/'.$figure->id]) !!}
<h1 style="clear:both;"><img src="{{$figure->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $figure->displayName !!}</h1>

<h5>{!! $figure->category ? ucfirst($figure->category->displayName) : 'Miscellaneous' !!}
    {!! $figure->faction ? '・ Part of '.ucfirst($figure->faction->displayName) : '' !!}{!! $figure->factionRank ? ' ('.$figure->factionRank->name.')' : null !!}

@if($figure->birth_date || $figure->death_date)
    <span class="ml-4 text-muted">{!! $figure->birth_date ? 'Born: '.format_date($figure->birth_date, false) : 'Born: Unknown' !!} {!! $figure->death_date ? '- Died: '.format_date($figure->death_date, false) : '- Died: Unknown' !!}</span>
@endif
</h5>

@if($figure->image_extension)
    <div class="text-center"><img src="{{$figure->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@isset($figure->summary)
<div class="world-entry-text px-3 text-center">{!! $figure->summary !!}</div>
@endisset

@isset($figure->parsed_description)
<div class="world-entry-text px-3">
    {!! $figure->parsed_description !!}
</div>
@endisset

<div class="row justify-content-center mx-0 px-0 mt-3">

    @if(count(allAttachments($figure)))
        @foreach(allAttachments($figure) as $type => $attachments)
            <div class="text-center col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header h3">
                        Associated {{ $type }}{{ count($attachments) == 1 ? '' : 's' }}
                    </div>
                    <div class="card-body">
                        @foreach($attachments as $attachment)
                        <p class="mb-0">
                            {!! $attachment->displayName !!}
                        </p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</div>




@endsection
