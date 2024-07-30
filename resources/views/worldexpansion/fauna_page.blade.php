@extends('worldexpansion.layout')

@section('title') Fauna :: {{ $fauna->name }} @endsection

@section('content')@if(Auth::check() && Auth::user()->hasPower('manage_world'))
    <a data-toggle="tooltip" title="[ADMIN] Edit Fauna" href="{{ url('admin/world/faunas/edit/').'/'.$fauna->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
@endif
{!! breadcrumbs(['World' => 'world', 'Fauna' => 'world/faunas', $fauna->name => 'world/faunas/'.$fauna->id]) !!}
<h1  style="clear:both;"><img src="{{$fauna->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $fauna->displayName !!}@isset($fauna->scientific_name)<span class="ml-2" style="opacity:0.5; font-size:0.7em;font-style:italic">{!! $fauna->scientific_name !!}</span>@endisset</h1>
<h5>{!! $fauna->category ? ucfirst($fauna->category->displayName) : 'Miscellaneous' !!}</h5>

@if($fauna->image_extension)
    <div class="text-center"><img src="{{$fauna->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@isset($fauna->summary)
<div class="world-entry-text px-3 text-center">{!! $fauna->summary !!}</div>
@endisset

@isset($fauna->parsed_description)
<div class="world-entry-text px-3">
    {!! $fauna->parsed_description !!}
</div>
@endisset



<div class="row justify-content-center mx-0 px-0 mt-3">

    @if(count(allAttachments($fauna)))
        @foreach(allAttachments($fauna) as $type => $attachments)
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
