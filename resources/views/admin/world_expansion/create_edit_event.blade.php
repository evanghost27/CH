@extends('admin.layout')

@section('admin-title') Event @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Events' => 'admin/world/events', ($event->id ? 'Edit' : 'Create').' Event' => $event->id ? 'admin/world/events/edit/'.$event->id : 'admin/world/events/create']) !!}

<h1>{{ $event->id ? 'Edit' : 'Create' }} Event
    @if($event->id)
        ({!! $event->displayName !!})
        <a href="#" class="btn btn-danger float-right delete-event-button">Delete Event</a>
    @endif
</h1>

{!! Form::open(['url' => $event->id ? 'admin/world/events/edit/'.$event->id : 'admin/world/events/create', 'files' => true]) !!}

<div class="card mb-3">
    <div class="card-header h3">Basic Information</div>
    <div class="card-body">
        <div class="row mx-0 px-0">
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $event->name, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Category') !!} {!! add_help('What category of event is this?') !!}
                {!! Form::select('category_id', [0=>'Choose an Event Category'] + $categories, $event->category_id, ['class' => 'form-control selectize', 'id' => 'category']) !!}
            </div>
        </div>

        <div class="row mx-0 px-0">
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('occur_start', 'Start Date (Optional)') !!}
                {!! Form::text('occur_start', $event->occur_start, ['class' => 'form-control datepicker']) !!}
            </div>
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('occur_end', 'End Date (Optional)') !!} {!! add_help('If left blank but start date is set, this will be considered Ongoing.') !!}
                {!! Form::text('occur_end', $event->occur_end, ['class' => 'form-control datepicker']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('Summary (Optional)') !!}
            {!! Form::text('summary', $event->summary, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header h3">Images</div>
    <div class="card-body row">
        <div class="form-group col-md-6">
            @if($event->thumb_extension)
                <a href="{{$event->thumbUrl}}"  data-lightbox="entry" data-title="{{ $event->name }}"><img src="{{$event->thumbUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Thumbnail Image (Optional)') !!} {!! add_help('This thumbnail is used on the event index.') !!}
            <div>{!! Form::file('image_th') !!}</div>
            <div class="text-muted">Recommended size: 200x200</div>
            @if(isset($event->thumb_extension))
                <div class="form-check">
                    {!! Form::checkbox('remove_image_th', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-off' => 'Leave Thumbnail As-Is', 'data-on' => 'Remove Thumbnail Image']) !!}
                </div>
            @endif
        </div>

        <div class="form-group col-md-6">
            @if($event->image_extension)
                <a href="{{$event->imageUrl}}"  data-lightbox="entry" data-title="{{ $event->name }}"><img src="{{$event->imageUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Event Image (Optional)') !!} {!! add_help('This image is used on the event page as a header.') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: None (Choose a standard size for all event header images.)</div>
            @if(isset($event->image_extension))
                <div class="form-check">
                    {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-off' => 'Leave Header Image As-Is', 'data-on' => 'Remove Current Header Image']) !!}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header h3">
        {!! Form::label('Description (Optional)') !!}
    </div>
    <div class="card-body">
        <div class="form-group" style="clear:both">
            {!! Form::textarea('description', $event->description, ['class' => 'form-control wysiwyg']) !!}
        </div>
    </div>
</div>


@if($event->id)
    <div class="card mb-3">
        <div class="card-header h3">
            <div class="float-right"><a href="#" class="btn btn-sm btn-primary" id="addAttachment">Add Attachment</a></div>
            Attachments
        </div>
        <div class="card-body">
            @include('widgets._attachment_select', ['attachments' => $event->attachments])
        </div>
        @if($event->attachers->count())
            <div class="card-footer">
                <h5>Attached to the following</h5>
                <div class="row">
                    @foreach($event->attachers->groupBy('attacher_type') as $type => $attachers)
                        <div class="col-6 col-md-3"><div class="card"><div class="card-body p-2 text-center">
                            <div><strong>{!! $type !!}</strong> <small>({{ $attachers->count() }})</small></div>
                            <p class="mt-2 mb-1">
                                @foreach($attachers as $attacher)
                                    {!! $attacher->attacher->displayName !!}
                                    {{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        </div></div></div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif


<div class="form-group">
    {!! Form::checkbox('is_active', 1, $event->id ? $event->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the category will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit($event->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}




@include('widgets._attachment_select_row')

@endsection

@section('scripts')
@parent
@include('js._attachment_js')
<script>
$( document ).ready(function() {
    $('.delete-event-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/world/events/delete') }}/{{ $event->id }}", 'Delete Event');
    });
    $('.selectize').selectize();

    $( ".datepicker" ).datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: '',
    });
});

</script>
@endsection
