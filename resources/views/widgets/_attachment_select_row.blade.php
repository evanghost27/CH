@php
    // This file represents a common source and definition for assets used in attachment_select
    // While it is not per se as tidy as defining these in the controller(s),
    // doing so this way enables better compatibility across disparate extensions
    $items = \App\Models\Item\Item::orderBy('name')->pluck('name', 'id');
    $newses = \App\Models\News::orderBy('title')->pluck('title', 'id');
    $prompts = \App\Models\Prompt\Prompt::orderBy('name')->pluck('name', 'id');
    $locations = \App\Models\WorldExpansion\Location::getLocationsByType();
    $figures = \App\Models\WorldExpansion\Figure::getFiguresByCategory();
    $faunas = \App\Models\WorldExpansion\Fauna::getFaunasByCategory();
    $floras = \App\Models\WorldExpansion\Flora::getFlorasByCategory();
    $concepts = \App\Models\WorldExpansion\Concept::getConceptsByCategory();
    $factions = \App\Models\WorldExpansion\Faction::getFactionsByType();
    $events = \App\Models\WorldExpansion\Event::getEventsByCategory();
@endphp


<div id="attachmentRowData" class="row col-12 mt-1 px-0 hide">
    <div class="table table-sm px-0">
        <div id="attachmentRow" class="row px-0">
            <div class="attachment-row col-12 row py-1 logs-table-row">
                <div class="col-6 col-md-4">
                    {!! Form::select(
                        'attachment_type[]',
                        [
                            'Item' => 'Item',
                            'Prompt' => 'Prompt',
                            'News' => 'News',
                            'Figure' => 'Figure',
                            'Fauna' => 'Fauna',
                            'Flora' => 'Flora',
                            'Faction' => 'Faction',
                            'Concept' => 'Concept',
                            'Location' => 'Location',
                            'Event' => 'Event',
                        ],
                        null,
                        ['class' => 'form-control attachment-type', 'placeholder' => 'Select Attachment Type'],
                    ) !!}
                </div>
                <div class="col-6 col-md-3 attachment-row-select"></div>
                <div class="col-6 col-md-3">{!! Form::text('attachment_data[]', null, ['class' => 'form-control']) !!}</div>
                <div class="col-6 col-md text-right"><a href="#" class="btn btn-danger remove-attachment-button">Remove</a></div>
            </div>
        </div>
    </div>

    {!! Form::select('attachment_id[]', $items, null, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
    {!! Form::select('attachment_id[]', $newses, null, ['class' => 'form-control news-select', 'placeholder' => 'Select News Post']) !!}
    {!! Form::select('attachment_id[]', $locations, null, ['class' => 'form-control location-select', 'placeholder' => 'Select Location']) !!}
    {!! Form::select('attachment_id[]', $figures, null, ['class' => 'form-control figure-select', 'placeholder' => 'Select Figure']) !!}
    {!! Form::select('attachment_id[]', $faunas, null, ['class' => 'form-control fauna-select', 'placeholder' => 'Select Fauna']) !!}
    {!! Form::select('attachment_id[]', $floras, null, ['class' => 'form-control flora-select', 'placeholder' => 'Select Flora']) !!}
    {!! Form::select('attachment_id[]', $factions, null, ['class' => 'form-control faction-select', 'placeholder' => 'Select Faction']) !!}
    {!! Form::select('attachment_id[]', $concepts, null, ['class' => 'form-control concept-select', 'placeholder' => 'Select Concept']) !!}
    {!! Form::select('attachment_id[]', $prompts, null, ['class' => 'form-control prompt-select', 'placeholder' => 'Select Prompt']) !!}
    {!! Form::select('attachment_id[]', $events, null, ['class' => 'form-control event-select', 'placeholder' => 'Select Event']) !!}
</div>
