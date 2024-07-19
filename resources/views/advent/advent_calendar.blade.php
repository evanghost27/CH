@extends('layouts.app')

@section('title') Advent Calendar :: {{ $advent->displayName }} @endsection

@section('content')
{!! breadcrumbs(['Advent Calendar' => $advent->url, $advent->displayName => $advent->url]) !!}

<div class="row">
    <div class="col-sm">
    </div>
    <div class="col-lg-8 col-md-12">
        <div class="mb-3">

            <h2>{{ $advent->displayName }}</h2>

            @if($advent->isActive)
                <div class="mb-3 pt-3 text-center card">
                    <div class="card-body">
                        @if($advent->item($advent->day))
                            <h5>It's day {{ $advent->day }}! Today's prize is:</h5>
                            <p>
                                {!! $advent->displayItemLong($advent->day) !!}
                            </p>

                            <p>
                                Congratulations! Click the button below to claim today's prize.
                            </p>

                            <div class="mb-4">
                                @if($advent->isActive)
                                    @if(!isset($dayLog))
                                        {!! Form::open(['url' => 'advent-calendars/'.$advent->id]) !!}
                                            {!! Form::submit('Claim', ['class' => 'btn btn-primary']) !!}
                                        {!! Form::close() !!}
                                    @else
                                        <p><strong>You've already claimed this!</strong></p>
                                    @endif
                                @else
                                    <p>This advent calendar isn't active.</p>
                                @endif
                            </div>
                        @elseif($advent->isActive)
                            <p>There doesn't seem to be a prize for today!</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h4>Information</h4>
                </div>
                <div class="card-body">
                <div><strong>Start{{ $advent->start_at->isPast() ? 'ed' : 's' }}: </strong>{!! pretty_date($advent->start_at) !!}</div>
                <div {{ $advent->isActive ? '' : 'style="mb-2"' }}><strong>End{{ $advent->end_at->isPast() ? 'ed' : 's' }}: </strong>{!! pretty_date($advent->end_at) !!}</div>
                @if($advent->isActive)
                <div class="mb-2">It's <strong>day {{ $advent->day }}</strong> of {{ $advent->days }}! This day started {!! $advent->day == 1 ? pretty_date($advent->start_at) : pretty_date(Carbon\Carbon::now()->startOf('day')) !!} and {{ $advent->day == $advent->days ? 'ends' : 'day '.($advent->day+1).' is' }} {!! $advent->day == $advent->days ? pretty_date($advent->end_at) : pretty_date(Carbon\Carbon::now()->endOf('day')) !!}.</div>
                @endif

                @if($advent->summary)
                    <i>{{ $advent->summary }}</i>
                @endif

                @if($participantLog->count())
                    <hr/>
                    <p>You {{ $advent->isActive ? 'have ' : '' }}claimed these prizes{{ $advent->isActive ? ' thus far' : '' }}:</p>
                    <div class="d-flex align-content-around flex-wrap mb-2">
                        @foreach($participantLog as $log)
                            <div class="align-self-center mx-1">
                                {!! $advent->displayItemShort($log->day) !!}
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>
            </div>

        </div>
    </div>
    <div class="col-sm">
    </div>
</div>

@endsection
