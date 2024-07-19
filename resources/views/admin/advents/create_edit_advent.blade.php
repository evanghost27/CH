@extends('admin.layout')

@section('admin-title') {{ $advent->id ? 'Edit' : 'Create' }} Advent Calendar @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Advent Calendars' => 'admin/data/advent-calendars', ($advent->id ? 'Edit' : 'Create').' Advent Calendar' => $advent->id ? 'admin/data/advent-calendars/edit/'.$advent->id : 'admin/data/advent-calendars/create']) !!}

<h1>{{ $advent->id ? 'Edit' : 'Create' }} Advent Calendar
    @if($advent->id)
        <a href="#" class="btn btn-danger float-right delete-advent-button">Delete Advent Calendar</a>
    @endif
</h1>

{!! Form::open(['url' => $advent->id ? 'admin/data/advent-calendars/edit/'.$advent->id : 'admin/data/advent-calendars/create']) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!} {!! add_help('This is the name you will use to identify this advent calendar internally. This name will not be shown to users; a name that can be easily identified is recommended.') !!}
    {!! Form::text('name', $advent->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Display Name') !!} {!! add_help('This is the name that will be shown to users. This is for display purposes and can be something more vague than the above.') !!}
    {!! Form::text('display_name', $advent->getRawOriginal('display_name'), ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Summary (Optional)') !!} {!! add_help('This is a short blurb that shows up when viewing an advent calendar\'s page. HTML cannot be used here.') !!}
    {!! Form::text('summary', $advent->summary, ['class' => 'form-control', 'maxLength' => 250]) !!}
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('start_at', 'Start Time') !!} {!! add_help('Advent calendars cannot be viewed before the starting time, nor can prizes be claimed.') !!}
            {!! Form::text('start_at', $advent->start_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('end_at', 'End Time') !!} {!! add_help('Advent calendars can be viewed after the ending time, but targets cannot be claimed.') !!}
            {!! Form::text('end_at', $advent->end_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
</div>

@if($advent->id)
    <p>With these start and end times, the advent calendar will run for {{ $advent->days }} days, with the first day on {{ $advent->start_at->toFormattedDateString() }} and last day on {{ $advent->end_at->endOf('day')->toFormattedDateString() }}. While you do not need to set the start time to the beginning of the first day nor the end time to the end of the last day, you should give users enough time to claim these days' prizes, since they may not be available for the full 24 hours that the other days' prizes are.</p>
@endif

@if($advent->id)
    <h3>Prizes <a class="small inventory-collapse-toggle collapse-toggle" href="#prizes" data-toggle="collapse">Show</a></h3>
    <div class="mb-3 collapse form-group" id="prizes">
        <p>These are the prizes for each day. You can specify a item and quantity, which users will be able to claim on the respective day. If you do not specify an item, no prize will be available for that day.</p>

        @for($day = 1; $day <= $advent->days; $day++)
            <div class="form-group">
                {!! Form::label('Day '.$day.' Prize') !!}
                <div id="itemList">
                    <div class="d-flex mb-2">
                        {!! Form::select('item_ids['.$day.']', $items, isset($advent->data[$day]) ? $advent->data[$day]['item'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                        {!! Form::text('quantities['.$day.']', isset($advent->data[$day]) ? $advent->data[$day]['quantity'] : 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <h5>Bonus Prize</h5>
    <p>This prize, if an item is set, will be given to users who collect every prize from this advent. This is checked on the final day.</p>
    <div class="d-flex mb-2">
        {!! Form::select('item_ids[bonus]', $items, isset($advent->data['bonus']) ? $advent->data['bonus']['item'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
        {!! Form::text('quantities[bonus]', isset($advent->data['bonus']) ? $advent->data['bonus']['quantity'] : 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
    </div>
@endif

<div class="text-right">
    {!! Form::submit($advent->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($advent->id)
    <h3>Display Link</h3>
    <p>For convenience, here is the advent calendar's url as well as the full HTML to display a link to the advent calendar's user-facing page. Users claim the day's prize from this page.</p>
    <div class="alert alert-secondary">
        {{ $advent->url }}
    </div>
    <div class="alert alert-secondary">
        {{ $advent->displayLink }}
    </div>
@endif

@if($advent->id)
    <h3>Log</h3>
    <p>
        This is the log of claimed prizes. Each claimed prize has its own row, since there can be any number of prizes per calendar.
    </p>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
            <div class="form-group mr-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort by User (A-Z)',
                    'alpha-reverse'  => 'Sort by User (Z-A)',
                    'day'            => 'Sort by Day (Asc)',
                    'day-reverse'    => 'Sort by Day (Desc)',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group mb-3">
                {!! Form::submit('Sort', ['class' => 'btn btn-primary']) !!}
            </div>
        {!! Form::close() !!}
    </div>

    @if(count($advent->participants))
    {!! $participants->render() !!}

    <div class="row ml-md-2 mb-3">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-md-2 font-weight-bold">User</div>
            <div class="col-md font-weight-bold text-center">Day</div>
            <div class="col-md font-weight-bold text-center">Claimed</div>
        </div>
        @foreach($participants as $participant)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-md-2">
                {!! $participant->user->displayName !!}
            </div>
            <div class="col-md text-center">
                {{ $participant->day }} - {!! ($advent->item($participant->day)) ? $advent->item($participant->day)->displayName : 'Deleted Asset' !!} ×{{ $advent->itemQuantity($participant->day) }}
            </div>
            <div class="col-md text-center">
                {!! pretty_date($participant->claimed_at) !!}
            </div>
        </div>
        @endforeach
    </div>

    {!! $participants->render() !!}
    @else
        <p>No participants found!</p>
    @endif

@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.default.item-select').selectize();

    $('.delete-advent-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/advent-calendars/delete') }}/{{ $advent->id }}", 'Delete Advent Calendar');
    });

    $( ".datepicker" ).datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: 'HH:mm:ss',
    });
});

</script>
@endsection
