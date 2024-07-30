@extends('admin.layout')

@section('admin-title') Flora @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Flora' => 'admin/world/floras', ($flora->id ? 'Edit' : 'Create').' Flora' => $flora->id ? 'admin/world/floras/edit/'.$flora->id : 'admin/world/floras/create']) !!}

<h1>{{ $flora->id ? 'Edit' : 'Create' }} Flora
    @if($flora->id)
        ({!! $flora->displayName !!})
        <a href="#" class="btn btn-danger float-right delete-flora-button">Delete Flora</a>
    @endif
</h1>

{!! Form::open(['url' => $flora->id ? 'admin/world/floras/edit/'.$flora->id : 'admin/world/floras/create', 'files' => true]) !!}

<div class="card mb-3">
    <div class="card-header h3">Basic Information</div>
    <div class="card-body">
        <div class="row mx-0 px-0">
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $flora->name, ['class' => 'form-control']) !!}
            </div>
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Scientific Name (Optional)') !!}
                {!! Form::text('scientific_name', $flora->scientific_name, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('Category') !!} {!! add_help('What category of flora is this?') !!}
            {!! Form::select('category_id', [0=>'Choose a Category'] + $categories, $flora->category_id, ['class' => 'form-control selectize', 'id' => 'category']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Summary (Optional)') !!}
            {!! Form::text('summary', $flora->summary, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header h3">Images</div>
    <div class="card-body row">
        <div class="form-group col-md-6">
            @if($flora->thumb_extension)
                <a href="{{$flora->thumbUrl}}"  data-lightbox="entry" data-title="{{ $flora->name }}"><img src="{{$flora->thumbUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Thumbnail Image (Optional)') !!} {!! add_help('This thumbnail is used on the flora index.') !!}
            <div>{!! Form::file('image_th') !!}</div>
            <div class="text-muted">Recommended size: 200x200</div>
            @if(isset($flora->thumb_extension))
                <div class="form-check">
                    {!! Form::checkbox('remove_image_th', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-off' => 'Leave Thumbnail As-Is', 'data-on' => 'Remove Thumbnail Image']) !!}
                </div>
            @endif
        </div>

        <div class="form-group col-md-6">
            @if($flora->image_extension)
                <a href="{{$flora->imageUrl}}"  data-lightbox="entry" data-title="{{ $flora->name }}"><img src="{{$flora->imageUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Flora Image (Optional)') !!} {!! add_help('This image is used on the flora page as a header.') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: None (Choose a standard size for all flora header images.)</div>
            @if(isset($flora->image_extension))
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
            {!! Form::textarea('description', $flora->description, ['class' => 'form-control wysiwyg']) !!}
        </div>
    </div>
</div>

@if($flora->id)
    <div class="card mb-3">
        <div class="card-header h3">
            <div class="float-right"><a href="#" class="btn btn-sm btn-primary" id="addAttachment">Add Attachment</a></div>
            Attachments
        </div>
        <div class="card-body">
            @include('widgets._attachment_select', ['attachments' => $flora->attachments])
        </div>
        @if($flora->attachers->count())
            <div class="card-footer">
                <h5>Attached to the following</h5>
                <div class="row">
                    @foreach($flora->attachers->groupBy('attacher_type') as $type => $attachers)
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
    {!! Form::checkbox('is_active', 1, $flora->id ? $flora->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the category will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit($flora->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}
@include('widgets._attachment_select_row')

<div class="item-row hide mb-2 col-4">
    {!! Form::select('item_id[]', $items, null, ['class' => 'form-control mr-2 item-select', 'placeholder' => 'Select Item']) !!}
    <a href="#" class="remove-item btn btn-danger mb-2">×</a>
</div>

<div class="location-row hide mb-2 col-4">
    {!! Form::select('location_id[]', $locations, null, ['class' => 'form-control mr-2 location-select', 'placeholder' => 'Select Location']) !!}
    <a href="#" class="remove-location btn btn-danger mb-2">×</a>
</div>

@endsection

@section('scripts')
@parent
@include('js._attachment_js')
<script>
$( document ).ready(function() {
    $('.delete-flora-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/world/floras/delete') }}/{{ $flora->id }}", 'Delete Flora');
    });
    $('.selectize').selectize();
});

</script>
@endsection
