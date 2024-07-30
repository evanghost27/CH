@extends('worldexpansion.layout')

@section('title')
    Concept :: {{ $concept->name }}
@endsection

@section('content')
    @if (Auth::check() && Auth::user()->hasPower('manage_world'))
        <a data-toggle="tooltip" title="[ADMIN] Edit Concept" href="{{ url('admin/world/concepts/edit/') . '/' . $concept->id }}" class="mb-2 float-right">
            <i class="fas fa-crown"></i></a>
    @endif
    {!! breadcrumbs(['World' => 'world', 'Concept' => 'world/concepts', $concept->name => 'world/concepts/' . $concept->id]) !!}
    <h1 style="clear:both;"><img src="{{ $concept->thumbUrl }}" style="max-height:25px;vertical-align:inherit;" />{!! $concept->displayName !!}@isset($concept->scientific_name)
        <span class="ml-2" style="opacity:0.5; font-size:0.7em;font-style:italic">{!! $concept->scientific_name !!}</span>
    @endisset
</h1>
<h5>{!! $concept->category ? ucfirst($concept->category->displayName) : 'Miscellaneous' !!}</h5>

@if ($concept->image_extension)
    <div class="text-center"><img src="{{ $concept->imageUrl }}" class="mw-100 mb-3" /></div>
@endif

@isset($concept->summary)
    <div class="world-entry-text px-3 text-center">{!! $concept->summary !!}</div>
@endisset

@isset($concept->parsed_description)
    <div class="world-entry-text px-3">
        {!! $concept->parsed_description !!}
    </div>
@endisset



<div class="row justify-content-center mx-0 px-0 mt-3">

    @if (count(allAttachments($concept)))
        @foreach (allAttachments($concept) as $type => $attachments)
            <div class="text-center col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header h3">
                        Associated {{ $type }}{{ count($attachments) == 1 ? '' : 's' }}
                    </div>
                    <div class="card-body">
                        @foreach ($attachments as $attachment)
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
