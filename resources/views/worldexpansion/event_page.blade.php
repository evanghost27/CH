@extends('worldexpansion.layout')

@section('title')
    Event :: {{ $event->name }}
@endsection

@section('content')
    @if (Auth::check() && Auth::user()->hasPower('manage_world'))
        <a data-toggle="tooltip" title="[ADMIN] Edit Event" href="{{ url('admin/world/events/edit/') . '/' . $event->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
    @endif
    {!! breadcrumbs(['World' => 'world', 'Event' => 'world/events', $event->name => 'world/events/' . $event->id]) !!}
    <h1 style="clear:both;"><img src="{{ $event->thumbUrl }}" style="max-height:25px;vertical-align:inherit;" />{!! $event->displayName !!}</h1>
    <h5>{!! $event->category ? ucfirst($event->category->displayName) : 'Miscellaneous' !!}

        @if ($event->occur_start || $event->occur_end)
            <span class="ml-4 text-muted">{!! $event->occur_start ? format_date($event->occur_start, false) : '' !!} {!! $event->occur_end ? '- ' . format_date($event->occur_end, false) : 'Ongoing' !!}</span>
        @endif
    </h5>

    @if ($event->image_extension)
        <div class="text-center"><img src="{{ $event->imageUrl }}" class="mw-100 mb-3" /></div>
    @endif

    @isset($event->summary)
        <div class="world-entry-text px-3 text-center">{!! $event->summary !!}</div>
    @endisset


    @isset($event->parsed_description)
        <div class="world-entry-text px-3">
            {!! $event->parsed_description !!}
        </div>
    @endisset

    <div class="row justify-content-center mx-0 px-0 mt-3">

        @if (count(allAttachments($event)))
            @foreach (allAttachments($event) as $type => $attachments)
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
