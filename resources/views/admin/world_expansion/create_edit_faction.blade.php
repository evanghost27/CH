@extends('admin.layout')

@section('admin-title') Factions @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Factions' => 'admin/world/factions', ($faction->id ? 'Edit' : 'Create').' Faction' => $faction->id ? 'admin/world/factions/edit/'.$faction->id : 'admin/world/factions/create']) !!}

<h1>{{ $faction->id ? 'Edit' : 'Create' }} Faction
    @if($faction->id)
        ({!! $faction->displayName !!})
        <a href="#" class="btn btn-danger float-right delete-type-button">Delete Faction</a>
    @endif
</h1>

{!! Form::open(['url' => $faction->id ? 'admin/world/factions/edit/'.$faction->id : 'admin/world/factions/create', 'files' => true]) !!}

<div class="card mb-3">
    <div class="card-header h3">Basic Information</div>
    <div class="card-body">
        <div class="row mx-0 px-0">
            <div class="form-group col-md px-0 pr-md-1">
                {!! Form::label('Name*') !!}
                {!! Form::text('name', $faction->name, ['class' => 'form-control']) !!}
            </div>
            @if(isset($faction->parent_id))
                <div class="form-group col-md px-0 pr-md-1">
                    {!! Form::label('Style') !!} {!! add_help('How this faction will be displayed. <br> Options are editable in the Faction model.') !!}
                    {!! Form::select('style', $faction->displayStyles, isset($faction->display_style) ? $faction->display_style : null, ['class' => 'form-control selectize']) !!}
                </div>
            @endif
        </div>


        <div class="row mx-0 px-0">
            <div class="form-group col-12 col-md-6 px-0 pr-md-1">
                {!! Form::label('Type*') !!} {!! add_help('What type of faction is this?') !!}
                {!! Form::select('type_id', [0=>'Choose a Faction Type'] + $types, $faction->type_id, ['class' => 'form-control selectize', 'id' => 'type']) !!}
            </div>

            <div class="form-group col-12 col-md-6 px-0 px-md-1">
                {!! Form::label('Parent (Optional)') !!} {!! add_help('For instance, the parent of Paris is France. <br><strong>If left blank, this will be \'top level.\'</strong>""') !!}
                {!! Form::select('parent_id', [0=>'Choose a Parent'] + $factions, isset($faction->parent_id) ? $faction->parent_id : null, ['class' => 'form-control selectize']) !!}
            </div>
        </div>

        @if($user_enabled || $ch_enabled)
            <div class=" mx-0 px-0 text-center">
            @if($user_enabled)
                {!! Form::checkbox('user_faction', 1, $faction->id ? $faction->is_user_faction : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Users Can Join', 'data-off' => 'Users Cannot Join']) !!}
            @endif
            @if($ch_enabled)
                {!! Form::checkbox('character_faction', 1, $faction->id ? $faction->is_character_faction : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Characters Can Join', 'data-off' => 'Characters Cannot Join']) !!}
            @endif
            </div>
        @endif

        <div class="form-group">
            {!! Form::label('Summary (Optional)') !!}
            {!! Form::text('summary', $faction->summary, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header h3">Images</div>
    <div class="card-body row">
        <div class="form-group col-md-6">
            @if($faction->thumb_extension)
                <a href="{{$faction->thumbUrl}}"  data-lightbox="entry" data-title="{{ $faction->name }}"><img src="{{$faction->thumbUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('Thumbnail Image (Optional)') !!} {!! add_help('This thumbnail is used on the faction index.') !!}
            <div>{!! Form::file('image_th') !!}</div>
            <div class="text-muted">Recommended size: 200x200</div>
            @if(isset($faction->thumb_extension))
                <div class="form-check">
                    {!! Form::checkbox('remove_image_th', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-off' => 'Leave Thumbnail As-Is', 'data-on' => 'Remove Thumbnail Image']) !!}
                </div>
            @endif
        </div>

        <div class="form-group col-md-6">
            @if($faction->image_extension)
                <a href="{{$faction->imageUrl}}"  data-lightbox="entry" data-title="{{ $faction->name }}"><img src="{{$faction->imageUrl}}" class="mw-100 float-left mr-3" style="max-height:125px"></a>
            @endif
            {!! Form::label('faction Image (Optional)') !!} {!! add_help('This image is used on the faction page as a header.') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: None (Choose a standard size for all faction header images.)</div>
            @if(isset($faction->image_extension))
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
            {!! Form::textarea('description', $faction->description, ['class' => 'form-control wysiwyg']) !!}
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header h3">Ranks</div>
    <div class="card-body">
        <p>Factions may have multiple ranks, which may be either open (available for users and/or characters to have/climb through) or closed (occupyable by characters or figures and changeable only by staff). Open ranks require a "breakpoint"-- the amount of faction standing (as represented by the set currency) required to attain that rank. Ranks are adjusted automatically/on the fly based on the user/character's current amount of standing. Ranks should have unique breakpoints within the faction. To create a basic/entry rank, set a breakpoint of 0.</p>
        <div class="form-group">
            <div id="rankList" class="row mb-2">
                @foreach($faction->ranks()->orderBy('sort')->get() as $rank)
                    <div class="rank-row-entry col-md-12 mb-2">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::label('Sort/Internal Ranking') !!} {!! add_help('The order of the rank within the faction, with 1 being the highest rank.') !!}
                                            {!! Form::number('rank_sort[]', $rank->sort, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            {!! Form::label('Rank Name') !!}
                                            <div class="d-flex">
                                                {!! Form::text('rank_name[]', $rank->name, ['class' => 'form-control']) !!}
                                                <a href="#" class="remove-rank btn btn-danger ml-2 mb-2">×</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Description') !!}
                                    {!! Form::text('rank_description[]', $rank->description, ['class' => 'form-control']) !!}
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group mb-2">
                                            {!! Form::label('Is Open') !!}
                                            {!! Form::select('rank_is_open[]', [1 => 'Yes', 0 => 'No'], $rank->is_open, ['class' => 'form-control rank-is-open', 'placeholder' => 'Choose a Setting']) !!}
                                        </div>
                                    </div>
                                    <div class="rankOpenSetting col-md-8">
                                        <div class="{{ $rank->is_open ? 'show' : 'hide' }} openOptions">
                                            <div class="form-group">
                                                {!! Form::label('Breakpoint') !!} {!! add_help('The amount of standing required to achieve this rank.') !!}
                                                {!! Form::number('rank_breakpoint[]', $rank->breakpoint, ['class' => 'form-control', 'placeholder' => 'Enter a Breakpoint']) !!}
                                            </div>
                                        </div>
                                        <div class="{{ !$rank->is_open ? 'show' : 'hide' }} closeOptions">
                                            <div class="form-group">
                                                {!! Form::label('Available Positions') !!} {!! add_help('The number of positions of this rank available. Please note that reducing this will remove any existing members over the new number from their position.') !!}
                                                {!! Form::number('rank_amount[]', $rank->amount, ['class' => 'form-control', 'placeholder' => 'Enter an Amount']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$rank->is_open)
                                    <h5>Occupants</h5>
                                    @if($rank->members()->count())
                                        @foreach($rank->members as $member)
                                            <div class="rank-member-entry row">
                                                <div class="col-md">
                                                    <div class="form-group mb-2">
                                                        {!! Form::label('Member Type') !!} {!! add_help('Only members of this faction may be selected.') !!}
                                                        {!! Form::select('rank_member_type['.$rank->id.'][]', ['figure' => 'Figure'] + (Settings::get('WE_user_factions') > 0 ? ['user' => 'User'] : []) + (Settings::get('WE_character_factions') > 0 ? ['character' => 'Character'] : []), $member->member_type, ['class' => 'form-control rank-member-type', 'placeholder' => 'Choose a Type']) !!}
                                                    </div>
                                                </div>
                                                <div class="rankMemberSetting col-md-8 mt-auto">
                                                    <div class="{{ $member->member_type == 'figure' ? 'show' : 'hide' }} figureOptions">
                                                        <div class="form-group">
                                                            {!! Form::label('Figure') !!}
                                                            {!! Form::select('rank_figure_id['.$rank->id.'][]', $figureOptions, $member->member_type == 'figure' ? $member->member_id : null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select Figure']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="{{ $member->member_type == 'user' ? 'show' : 'hide' }} userOptions">
                                                        <div class="form-group">
                                                            {!! Form::label('User') !!}
                                                            {!! Form::select('rank_user_id['.$rank->id.'][]', $users, $member->member_type == 'user' ? $member->member_id : null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select User']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="{{ $member->member_type == 'character' ? 'show' : 'hide' }} characterOptions">
                                                        <div class="form-group">
                                                            {!! Form::label('Character') !!}
                                                            {!! Form::select('rank_character_id['.$rank->id.'][]', $characters, $member->member_type == 'character' ? $member->member_id : null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select Character']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if(!$rank->members()->count() || $rank->members()->count() < $rank->amount)
                                        @for($i = 0; $i < ($rank->amount - $rank->members()->count()); $i++)
                                            <div class="rank-member row">
                                                <div class="col-md">
                                                    <div class="form-group mb-2">
                                                        {!! Form::label('Member Type') !!} {!! add_help('Only members of this faction may be selected.') !!}
                                                        {!! Form::select('rank_member_type['.$rank->id.'][]', ['figure' => 'Figure'] + (Settings::get('WE_user_factions') > 0 ? ['user' => 'User'] : []) + (Settings::get('WE_character_factions') > 0 ? ['character' => 'Character'] : []), null, ['class' => 'form-control rank-member-type', 'placeholder' => 'Choose a Type']) !!}
                                                    </div>
                                                </div>
                                                <div class="rankMemberSetting col-md-8 mt-auto">
                                                    <div class="show defaultOptions">
                                                        <p>Please select a member type.</p>
                                                    </div>
                                                    <div class="hide figureOptions">
                                                        <div class="form-group">
                                                            {!! Form::label('Figure') !!}
                                                            {!! Form::select('rank_figure_id['.$rank->id.'][]', $figureOptions, null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select Figure']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="hide userOptions">
                                                        <div class="form-group">
                                                            {!! Form::label('User') !!}
                                                            {!! Form::select('rank_user_id['.$rank->id.'][]', $users, null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select User']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="hide characterOptions">
                                                        <div class="form-group">
                                                            {!! Form::label('Character') !!}
                                                            {!! Form::select('rank_character_id['.$rank->id.'][]', $characters, null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select Character']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-right"><a href="#" class="btn btn-primary" id="add-rank">Add Rank</a></div>
        </div>
    </div>
</div>

@if($faction->id)
    <div class="card mb-3">
        <div class="card-header h3">
            <div class="float-right"><a href="#" class="btn btn-sm btn-primary" id="addAttachment">Add Attachment</a></div>
            Attachments
        </div>
        <div class="card-body">
            @include('widgets._attachment_select', ['attachments' => $faction->attachments])
        </div>
        @if($faction->attachers->count())
            <div class="card-footer">
                <h5>Attached to the following</h5>
                <div class="row">
                    @foreach($faction->attachers->groupBy('attacher_type') as $type => $attachers)
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
    {!! Form::checkbox('is_active', 1, $faction->id ? $faction->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the type will not be visible to regular users.') !!}
</div>

<div class="text-right">
    {!! Form::submit($faction->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<div class="rank-row col-md-12 hide mb-2">
    <div class="card w-100">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('Sort/Internal Ranking') !!} {!! add_help('The order of the rank within the faction, with 1 being the highest rank.') !!}
                        {!! Form::number('rank_sort[]', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        {!! Form::label('Rank Name') !!}
                        <div class="d-flex">
                            {!! Form::text('rank_name[]', null, ['class' => 'form-control']) !!}
                            <a href="#" class="remove-rank btn btn-danger ml-2 mb-2">×</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Description') !!}
                {!! Form::text('rank_description[]', null, ['class' => 'form-control']) !!}
            </div>
            <div class="row">
                <div class="col-md">
                    <div class="form-group mb-2">
                        {!! Form::label('Is Open') !!}
                        {!! Form::select('rank_is_open[]', [1 => 'Yes', 0 => 'No'], null, ['class' => 'form-control rank-is-open', 'placeholder' => 'Choose a Setting']) !!}
                    </div>
                </div>
                <div class="rankOpenSetting col-md-8 mt-auto">
                    <div class="show defaultOptions">
                        <p>Please set whether this rank should be open or not.</p>
                    </div>
                    <div class="hide openOptions">
                        <div class="form-group">
                            {!! Form::label('Breakpoint') !!} {!! add_help('The amount of standing required to achieve this rank.') !!}
                            {!! Form::number('rank_breakpoint[]', null, ['class' => 'form-control', 'placeholder' => 'Enter a Breakpoint']) !!}
                        </div>
                    </div>
                    <div class="hide closeOptions">
                        <div class="form-group">
                            {!! Form::label('Available Positions') !!} {!! add_help('The number of positions of this rank available.') !!}
                            {!! Form::number('rank_amount[]', null, ['class' => 'form-control', 'placeholder' => 'Enter an Amount']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('widgets._attachment_select_row')

@endsection

@section('scripts')
@parent
@include('js._attachment_js')
<script>
$( document ).ready(function() {
    $('.delete-type-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/world/factions/delete') }}/{{ $faction->id }}", 'Delete Faction');
    });
    $('.selectize').selectize();

    $('#rankList .rank-row-entry').each(function(index) {
        attachRankListeners($(this).find('.rank-is-open'));
        attachRankMemberListeners($(this).find('.rank-member-type'));
    });
    $('#add-rank').on('click', function(e) {
        e.preventDefault();
        addRankRow();
    });
    $('.remove-rank').on('click', function(e) {
        e.preventDefault();
        removeRankRow($(this));
    })
    function addRankRow() {
        var $clone = $('.rank-row').clone();
        $('#rankList').append($clone);
        $clone.removeClass('hide rank-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-rank').on('click', function(e) {
            e.preventDefault();
            removeRankRow($(this));
        })
        attachRankListeners($clone.find('.rank-is-open'));
    }
    function removeRankRow($trigger) {
        $trigger.parent().parent().parent().parent().parent().parent().remove();
    }

    function attachRankListeners(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().parent().find('.rankOpenSetting');

            $cell.children().addClass('hide');
            $cell.children().children().val(null);

            if(val == 1) {
                $cell.children('.openOptions').addClass('show');
                $cell.children('.openOptions').removeClass('hide');
            }
            else if (val == 0){
                $cell.children('.closeOptions').addClass('show');
                $cell.children('.closeOptions').removeClass('hide');
            }
        });
    }

    function attachRankMemberListeners(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().parent().find('.rankMemberSetting');

            $cell.children().addClass('hide');
            $cell.children().children().val(null);

            if(val == 'figure') {
                $cell.children('.figureOptions').addClass('show');
                $cell.children('.figureOptions').removeClass('hide');
            }
            else if (val == 'user'){
                $cell.children('.userOptions').addClass('show');
                $cell.children('.userOptions').removeClass('hide');
            }
            else if (val == 'character'){
                $cell.children('.characterOptions').addClass('show');
                $cell.children('.characterOptions').removeClass('hide');
            }
            else {
                $cell.children('.defaultOptions').addClass('show');
                $cell.children('.defaultOptions').removeClass('hide');
            }
        });
    }

});

</script>
@endsection
