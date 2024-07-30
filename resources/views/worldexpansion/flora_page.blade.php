@extends('worldexpansion.layout')

@section('title') Flora :: {{ $flora->name }} @endsection

@section('content')

@if(Auth::check() && Auth::user()->hasPower('manage_world'))
    <a data-toggle="tooltip" title="[ADMIN] Edit Flora" href="{{ url('admin/world/floras/edit/').'/'.$flora->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
@endif
{!! breadcrumbs(['World' => 'world', 'Flora' => 'world/floras', $flora->name => 'world/floras/'.$flora->id]) !!}
<h1 style="clear:both;"><img src="{{$flora->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $flora->displayName !!}@isset($flora->scientific_name)<span class="ml-2" style="opacity:0.5; font-size:0.7em;font-style:italic">{!! $flora->scientific_name !!}</span>@endisset</h1>
<h5>{!! $flora->category ? ucfirst($flora->category->displayName) : 'Miscellaneous' !!}</h5>

@if($flora->image_extension)
    <div class="text-center"><img src="{{$flora->imageUrl}}" class="mw-100 mb-3"/></div>
@endif

@isset($flora->summary)
<div class="world-entry-text px-3 text-center">{!! $flora->summary !!}</div>
@endisset


@isset($flora->parsed_description)
<div class="world-entry-text px-3">
    {!! $flora->parsed_description !!}
</div>
@endisset


<div class="row justify-content-center mx-0 px-0 mt-3">

    @if(count(allAttachments($flora)))
        @foreach(allAttachments($flora) as $type => $attachments)
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
