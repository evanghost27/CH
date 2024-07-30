@extends('admin.layout')

@section('admin-title') Locations @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Locations' => 'admin/world/locations', ($location->id ? 'Edit' : 'Create').' Location' => $location->id ? 'admin/world/locations/edit/'.$location->id : 'admin/world/locations/create']) !!}

<h1>{{ $location->id ? 'Edit' : 'Create' }} Location
    @if($location->id)
        ({!! $location->displayName !!})
        <a href="#" class="btn btn-danger float-right delete-type-button">Delete Location</a>
    @endif
</h1>

{!! Form::open(['url' => $location->id ? 'admin/world/locations/edit/'.$location->id : 'admin/world/locations/create', 'files' => true]) !!}


<div class="card mb-3">
    <div class="card-header h3">Basic Information</div>
    <div class="card-body">

        <div class="row mx-0 px-0">
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Name*') !!}
                {!! Form::text('name', $location->name, ['class' => 'form-control']) !!}
            </div>
            @if(isset($location->parent_id))
                <div class="form-group col-md px-0 pr-md-1">
                    {!! Form::label('Style') !!} {!! add_help('How this location will be displayed. <br> Options are editable in the Location model.') !!}
                    {!! Form::select('style', $location->displayStyles, isset($location->display_style) ? $location->display_style : null, ['class' => 'form-control selectize']) !!}
                </div>
            @endif
        </div>


        <div class="row mx-0 px-0">
            <div class="form-group col-12 col-md-6 px-0 pr-md-1">
                {!! Form::label('Type*') !!} {!! add_help('What type of location is this?') !!}
                {!! Form::select('type_id', [0=>'Choose a Location Type'] + $types, $location->type_id, ['class' => 'form-control selectize', 'id' => 'type']) !!}
            </div>

            <div class="form-group col-12 col-md-6 px-0 px-md-1">
                {!! Form::label('Parent (Optional)') !!} {!! add_help('For instance, the parent of Paris is France. <br><strong>If left blank, this will be \'top level.\'</strong>""') !!}
                {!! Form::select('parent_id', [0=>'Choose a Parent'] + $locations, isset($location->parent_id) ? $location->parent_id : null, ['class' => 'form-control selectize']) !!}
            </div>
        </div>

        @if($user_enabled || $ch_enabled)
            <div class=" mx-0 px-0 text-center">
            @if($user_enabled)
                {!! Form::checkbox('user_home', 1, $location->id ? $location->is_user_home : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Users Can Live Here', 'data-off' => 'Users Cannot Live Here']) !!}
            @endif
            @if($ch_enabled)
                {!! Form::checkbox('character_home', 1, $location->id ? $location->is_character_home : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Characters Can Live Here', 'data-off' => 'Characters Cannot Live Here']) !!}
            @endif
            </div>
        @endif

        <div class="form-group">
            {!! Form::label('Summary (Optional)') !!}
            {!! Form::text('summary', $location->summary, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header h3">Images</div>
    <div class="card-body row">
        <div class="form-group col-md-6">
            @if($location->thumb_extension)
                <a href="{{$location->thumbUrl}}"  data-lightbox="entry" data-title="{{ $location->name }}"><img src="{{$location->thumbUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Thumbnail Image (Optional)') !!} {!! add_help('This thumbnail is used on the location index.') !!}
            <div>{!! Form::file('image_th') !!}</div>
            <div class="text-muted">Recommended size: 200x200</div>
            @if(isset($location->thumb_extension))
                <div class="form-check">
                    {!! Form::checkbox('remove_image_th', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-off' => 'Leave Thumbnail As-Is', 'data-on' => 'Remove Thumbnail Image']) !!}
                </div>
            @endif
        </div>

        <div class="form-group col-md-6">
            @if($location->image_extension)
                <a href="{{$location->imageUrl}}"  data-lightbox="entry" data-title="{{ $location->name }}"><img src="{{$location->imageUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('location Image (Optional)') !!} {!! add_help('This image is used on the location page as a header.') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: None (Choose a standard size for all location header images.)</div>
            @if(isset($location->image_extension))
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
            {!! Form::textarea('description', $location->description, ['class' => 'form-control wysiwyg']) !!}
        </div>
    </div>
</div>

@if($location->id)
    <div class="card mb-3">
        <div class="card-header h3">
            <div class="float-right"><a href="#" class="btn btn-sm btn-primary" id="addAttachment">Add Attachment</a></div>
            Attachments
        </div>
        <div class="card-body">
            @include('widgets._attachment_select', ['attachments' => $location->attachments])
        </div>
        @if($location->attachers->count())
            <div class="card-footer">
                <h5>Attached to the following</h5>
                <div class="row">
                    @foreach($location->attachers->groupBy('attacher_type') as $type => $attachers)
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
    {!! Form::checkbox('is_active', 1, $location->id ? $location->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the type will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit($location->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}
@include('widgets._attachment_select_row')
@endsection

@section('scripts')
@parent
@include('js._attachment_js')
<script>
$( document ).ready(function() {
    $('.delete-type-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/world/locations/delete') }}/{{ $location->id }}", 'Delete Location');
    });
    $('.selectize').selectize();
});

</script>
@endsection
