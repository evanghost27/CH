@extends('admin.layout')

@section('admin-title') Concept @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Concept' => 'admin/world/concepts', ($concept->id ? 'Edit' : 'Create').' Concept' => $concept->id ? 'admin/world/concepts/edit/'.$concept->id : 'admin/world/concepts/create']) !!}

<h1>{{ $concept->id ? 'Edit' : 'Create' }} Concept
    @if($concept->id)
        ({!! $concept->displayName !!})
        <a href="#" class="btn btn-danger float-right delete-concept-button">Delete Concept</a>
    @endif
</h1>

{!! Form::open(['url' => $concept->id ? 'admin/world/concepts/edit/'.$concept->id : 'admin/world/concepts/create', 'files' => true]) !!}


<div class="card mb-3">
    <div class="card-header h3">Basic Information</div>
    <div class="card-body">
        <div class="row mx-0 px-0">
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $concept->name, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('Category') !!} {!! add_help('What category of concept is this?') !!}
            {!! Form::select('category_id', [0=>'Choose a Concept Category'] + $categories, $concept->category_id, ['class' => 'form-control selectize', 'id' => 'category']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Summary (Optional)') !!}
            {!! Form::text('summary', $concept->summary, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header h3">Images</div>
    <div class="card-body row">
        <div class="form-group col-md-6">
            @if($concept->thumb_extension)
                <a href="{{$concept->thumbUrl}}"  data-lightbox="entry" data-title="{{ $concept->name }}"><img src="{{$concept->thumbUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Thumbnail Image (Optional)') !!} {!! add_help('This thumbnail is used on the concept index.') !!}
            <div>{!! Form::file('image_th') !!}</div>
            <div class="text-muted">Recommended size: 200x200</div>
            @if(isset($concept->thumb_extension))
                <div class="form-check">
                    {!! Form::checkbox('remove_image_th', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-off' => 'Leave Thumbnail As-Is', 'data-on' => 'Remove Thumbnail Image']) !!}
                </div>
            @endif
        </div>

        <div class="form-group col-md-6">
            @if($concept->image_extension)
                <a href="{{$concept->imageUrl}}"  data-lightbox="entry" data-title="{{ $concept->name }}"><img src="{{$concept->imageUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Event Image (Optional)') !!} {!! add_help('This image is used on the concept page as a header.') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: None (Choose a standard size for all concept header images.)</div>
            @if(isset($concept->image_extension))
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
            {!! Form::textarea('description', $concept->description, ['class' => 'form-control wysiwyg']) !!}
        </div>
    </div>
</div>

@if($concept->id)
    <div class="card mb-3">
        <div class="card-header h3">
            <div class="float-right"><a href="#" class="btn btn-sm btn-primary" id="addAttachment">Add Attachment</a></div>
            Attachments
        </div>
        <div class="card-body">
            @include('widgets._attachment_select', ['attachments' => $concept->attachments])
        </div>
        @if($concept->attachers->count())
            <div class="card-footer">
                <h5>Attached to the following</h5>
                <div class="row">
                    @foreach($concept->attachers->groupBy('attacher_type') as $type => $attachers)
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
    {!! Form::checkbox('is_active', 1, $concept->id ? $concept->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the category will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit($concept->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}



@include('widgets._attachment_select_row')

@endsection

@section('scripts')
@parent

@include('js._attachment_js')
<script>
$( document ).ready(function() {
    $('.delete-concept-button').on('click', function(e) {
        e.prconceptDefault();
        loadModal("{{ url('admin/world/concepts/delete') }}/{{ $concept->id }}", 'Delete Concept');
    });
    $('.selectize').selectize();
});

</script>
@endsection
