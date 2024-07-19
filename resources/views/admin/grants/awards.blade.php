@extends('admin.layout')

@section('admin-title') Grant {{ucfirst(__('awards.awards'))}} @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Grant '.ucfirst(__('awards.awards')) => 'admin/grants/awards']) !!}

<h1>Grant {{ucfirst(__('awards.awards'))}}</h1>
<p>
    There should be at least one {{__('awards.award')}} selected for either {{__('lorekeeper.character')}} grants or user grants.
</p>

{!! Form::open(['url' => 'admin/grants/awards']) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="mb-0">User Grants</h3></div>
            <div class="card-body">
                <div class="form-group">
                    {!! Form::label('names[]', 'Username(s)') !!} {!! add_help('You can select up to 10 users at once.') !!}
                    {!! Form::select('names[]', $userOptions, null, ['id' => 'usernameList', 'class' => 'form-control', 'multiple']) !!}
                </div>

                <div>
                    <div class="float-right"><a href="#" class="btn btn-primary add-award btn-sm">Add {{ucfirst(__('awards.award'))}}</a></div>
                    {!! Form::label(ucfirst(__('awards.award')).'(s)') !!} {!! add_help('Must have at least 1 '.__('awards.award').' and quantity must be at least 1.') !!}

                    <div class="award-list mt-2">
                    </div>
                    <div class="award-row hide mt-1" style="clear:both;">
                        {!! Form::select('award_ids[]', $userAwardOptions, null, ['class' => 'form-control mr-2 award-select', 'placeholder' => 'Select '.ucfirst(__('awards.award'))]) !!}
                        {!! Form::number('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                        <a href="#" class="remove-award btn btn-danger mb-2">✖</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="mb-0">{{ucfirst(__('lorekeeper.character'))}} Grants</h3></div>
            <div class="card-body">
                <div class="form-group">
                    {!! Form::label('character_names[]', 'Character(s)') !!} {!! add_help('You can select up to 10 users at once.') !!}
                    {!! Form::select('character_names[]', $characterOptions, null, ['id' => 'characterList', 'class' => 'form-control', 'multiple']) !!}
                </div>

                <div>
                    <div class="float-right"><a href="#" class="btn btn-primary add-award btn-sm">Add {{ucfirst(__('awards.award'))}}</a></div>
                    {!! Form::label(ucfirst(__('awards.award')).'(s)') !!} {!! add_help('Must have at least 1 '.__('awards.award').' and quantity must be at least 1.') !!}

                    <div class="award-list mt-2">
                    </div>
                    <div class="award-row hide mt-1">
                        {!! Form::select('character_award_ids[]', $characterAwardOptions, null, ['class' => 'form-control mr-2 award-select', 'placeholder' => 'Select '.ucfirst(__('awards.award'))]) !!}
                        {!! Form::number('character_quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                        <a href="#" class="remove-award btn btn-danger mb-2">✖</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-header"><h3 class="mb-0">Additional Data</h3></div>
            <div class="card-body">
                <div class="form-group d-flex align-items-center no-gutters">
                    <div class="col-md-2">{!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs and in the '.__('awards.awardcase').' description.') !!}</div>
                    {!! Form::text('data', null, ['class' => 'col-md-10 form-control px-2', 'maxlength' => 400]) !!}
                </div>
                <div class="form-group d-flex align-items-center no-gutters">
                    <div class="col-md-2">{!! Form::label('notes', 'Notes (Optional)') !!} {!! add_help('Additional notes for the '.__('awards.award').'. This will appear in the '.__('awards.award').'\'s description, but not in the logs.') !!}</div>
                    {!! Form::text('notes', null, ['class' => 'col-md-10 form-control px-2', 'maxlength' => 400]) !!}
                </div>
                <div class="form-group">
                    {!! Form::checkbox('disallow_transfer', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('disallow_transfer', 'Account-bound', ['class' => 'form-check-label ml-3']) !!}
                    {!! add_help('If this is on, the recipient(s) will not be able to transfer this '.__('awards.award').' to other users. '.ucfirst(__('awards.awards')).' that disallow transfers by default will still not be transferrable.') !!}
                </div>
            </div>
        </div>
    </div>

</div>


<div class="text-right mt-2">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary px-5']) !!}
</div>

{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('#usernameList').selectize({
            maxItems: 10
        });
        $('#characterList').selectize({
            maxItems: 10
        });
        $('.default.award-select').selectize();
        $('.add-award').on('click', function(e) {
            e.preventDefault();
            addAwardRow($(this));
        });
        $('.remove-award').on('click', function(e) {
            e.preventDefault();
            removeAwardRow($(this));
        })
        function addAwardRow($trigger) {

            var $section = $trigger.parent().parent();
            var $list = $section.find('.award-list');
            var $clone = $section.find('.award-row').clone();
            $list.append($clone);
            $clone.removeClass('hide award-row');
            $clone.addClass('d-flex award');
            $clone.find('.remove-award').on('click', function(e) {
                e.preventDefault();
                removeAwardRow($(this));
            })
            $clone.find('.award-select').selectize();
        }

        function removeAwardRow($trigger) {
            $trigger.parent().remove();
        }
    });

</script>

@endsection
