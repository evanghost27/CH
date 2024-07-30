@php
    // This file represents a common source and definition for assets used in attachment_select
    // While it is not per se as tidy as defining these in the controller(s),
    // doing so this way enables better compatibility across disparate extensions
    $items               = \App\Models\Item\Item::orderBy('name')->pluck('name', 'id');
    $newses              = \App\Models\News::orderBy('title')->pluck('title', 'id');
    $prompts             = \App\Models\Prompt\Prompt::orderBy('name')->pluck('name', 'id');
    $locations           = \App\Models\WorldExpansion\Location::getLocationsByType();
    $figures             = \App\Models\WorldExpansion\Figure::getFiguresByCategory();
    $faunas              = \App\Models\WorldExpansion\Fauna::getFaunasByCategory();
    $floras              = \App\Models\WorldExpansion\Flora::getFlorasByCategory();
    $concepts            = \App\Models\WorldExpansion\Concept::getConceptsByCategory();
    $factions            = \App\Models\WorldExpansion\Faction::getFactionsByType();
    $events              = \App\Models\WorldExpansion\Event::getEventsByCategory();
@endphp


<div class="row no-gutters" id="attachments">
    <div class="row col-12  logs-table-header align-items-center ">
        <div class="col-4 py-1 h-100 col-md-4 font-weight-bold"><div class="logs-table-cell">Attachment Type</div></div>
        <div class="col-4 py-1 h-100 col-md-3 font-weight-bold"><div class="logs-table-cell">Attachment</div></div>
        <div class="col py-1 h-100 col-md-3 font-weight-bold"><div class="logs-table-cell">Notes</div></div>
    </div>
    <div id="attachmentsBody" class="row col-12 px-0">
        @if($attachments)
            @foreach($attachments as $attachment)
            <div class="row col-12 py-1 logs-table-row attachment-row">
                <div class="col-6 col-md-4">{!! Form::select('attachment_type[]', [
                    'Item'      => 'Item',      'Prompt'    => 'Prompt',    'News'      => 'News',
                    'Figure'    => 'Figure',    'Fauna'     => 'Fauna',     'Flora'     => 'Flora',
                    'Faction'   => 'Faction',   'Concept'   => 'Concept',   'Location'  => 'Location',  'Event' => 'Event'
                ], $attachment->attachment_type, ['class' => 'form-control attachment-type', 'placeholder' => 'Select Attachment Type']) !!}</div>
                <div class="col-6 col-md-3 attachment-row-select">
                    @if($attachment->attachment_type == 'Item')
                        {!! Form::select('attachment_id[]', $items, $attachment->attachment_id, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
                    @elseif($attachment->attachment_type == 'Prompt')
                        {!! Form::select('attachment_id[]', $prompts, $attachment->attachment_id, ['class' => 'form-control prompt-select', 'placeholder' => 'Select Prompt']) !!}
                    @elseif($attachment->attachment_type == 'News')
                        {!! Form::select('attachment_id[]', $newses, $attachment->attachment_id, ['class' => 'form-control newse-select', 'placeholder' => 'Select News Post']) !!}
                    @elseif($attachment->attachment_type == 'Figure')
                        {!! Form::select('attachment_id[]', $figures, $attachment->attachment_id, ['class' => 'form-control figure-select', 'placeholder' => 'Select Figure']) !!}
                    @elseif($attachment->attachment_type == 'Fauna')
                        {!! Form::select('attachment_id[]', $faunas, $attachment->attachment_id, ['class' => 'form-control fauna-select', 'placeholder' => 'Select Fauna']) !!}
                    @elseif($attachment->attachment_type == 'Flora')
                        {!! Form::select('attachment_id[]', $floras, $attachment->attachment_id, ['class' => 'form-control flora-select', 'placeholder' => 'Select Flora']) !!}
                    @elseif($attachment->attachment_type == 'Faction')
                        {!! Form::select('attachment_id[]', $factions, $attachment->attachment_id, ['class' => 'form-control faction-select', 'placeholder' => 'Select Faction']) !!}
                    @elseif($attachment->attachment_type == 'Concept')
                        {!! Form::select('attachment_id[]', $concepts, $attachment->attachment_id, ['class' => 'form-control concept-select', 'placeholder' => 'Select Concept']) !!}
                    @elseif($attachment->attachment_type == 'Location')
                        {!! Form::select('attachment_id[]', $locations, $attachment->attachment_id, ['class' => 'form-control location-select', 'placeholder' => 'Select Location']) !!}
                    @elseif($attachment->attachment_type == 'Event')
                        {!! Form::select('attachment_id[]', $events, $attachment->attachment_id, ['class' => 'form-control event-select', 'placeholder' => 'Select Event']) !!}
                    @endif
                </div>
                <div class="col-6 col-md-3">{!! Form::text('attachment_data[]', $attachment->data, ['class' => 'form-control']) !!}</div>
                <div class="col-6 col-md text-right"><a href="#" class="btn btn-danger remove-attachment-button">Remove</a></div>
            </div>
            @endforeach
        @endif

    </div>
</div>
