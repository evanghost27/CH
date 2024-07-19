@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s {{ __('awards.awards') }} @endsection

@section('profile-content')
@if($character->is_myo_slot)
{!! breadcrumbs([ucfirst(__('lorekeeper.myo')).' Masterlist' => 'myos', $character->fullName => $character->url, ucfirst(__('awards.awardcase'))  => $character->url.'/awardcase']) !!}
@else
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : ucfirst(__('lorekeeper.character')).' masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, ucfirst(__('awards.awardcase')) => $character->url.'/'.__('awards.awardcase')]) !!}
@endif

@include('character._header', ['character' => $character])

<h3>
    @if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
        <a href="#" class="float-right btn btn-outline-info btn-sm" id="grantButton" data-toggle="modal" data-target="#grantModal"><i class="fas fa-cog"></i> Admin</a>
    @endif
    {{ __('awards.awardcase') }}
</h3>

@foreach($awards as $categoryId=>$categoryAwards)
    <div class="card mb-3 awards-category">
        <h5 class="card-header awards-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
        </h5>
        <div class="card-body awards-body">
            @foreach($categoryAwards->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $awardId=>$stack)
                        <div class="col-sm-3 col-6 text-center awards-award" data-id="{{ $stack->first()->pivot->id }}" data-name="{{ $character->name ? $character->name : $character->slug }}'s {{ $stack->first()->name }}">
                            <div class="mb-1">
                                <a href="#" class="awards-stack {{ $stack->first()->is_featured ? 'alert alert-success' : '' }}"><img src="{{ $stack->first()->imageUrl }}" alt="{{ $stack->first()->name }}" class="mw-100"/></a>
                            </div>
                            <div>
                                <a href="#" class="awards-stack awards-stack-name">{{ $stack->first()->name }}@if($stack->first()->user_limit != 1) x{{ $stack->sum('pivot.count') }}@endif</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach

<h3>Latest Activity</h3>
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Sender</div>
    <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
    <div class="col-6 col-md-2 font-weight-bold">{{ucfirst(__('awards.award'))}}</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
      @foreach($logs as $log)
          @include('user._award_log_row', ['log' => $log, 'owner' => $character])
      @endforeach
</div>
<div class="text-right">
    <a href="{{ url($character->url.'/'.__('awards.award').'-logs') }}">View all...</a>
</div>

@if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
    <div class="modal fade" id="grantModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">[ADMIN] Grant {{ucfirst(__('awards.awards'))}} </span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <p>Note that granting {{__('awards.awards')}} does not check against any hold limits for {{__('lorekeeper.characters')}}.</p>
                <div class="form-group">
                {!! Form::open(['url' => 'admin/character/'.$character->slug.'/grant-awards']) !!}

                    {!! Form::label('award_ids',ucfirst(__('awards.award')).'(s)') !!} {!! add_help('Must have at least 1 '.__('awards.award').' and quantity must be at least 1.') !!}
                    <div id="awardList">
                        <div class="d-flex mb-2">
                            {!! Form::select('award_ids[]', $awardOptions, null, ['class' => 'form-control mr-2 default award-select', 'placeholder' => 'Select '.ucfirst(__('awards.award'))]) !!}
                            {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                            <a href="#" class="remove-award btn btn-danger mb-2 disabled">✖</a>
                        </div>
                    </div>
                    <div><a href="#" class="btn btn-primary" id="add-award">Add {{ucfirst(__('awards.award'))}}</a></div>
                    <div class="award-row hide mb-2">
                        {!! Form::select('award_ids[]', $awardOptions, null, ['class' => 'form-control mr-2 award-select', 'placeholder' => 'Select '.ucfirst(__('awards.award'))]) !!}
                        {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                        <a href="#" class="remove-award btn btn-danger mb-2">✖</a>
                    </div>

                    <h5 class="mt-2">Additional Data</h5>

                    <div class="form-group">
                        {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs and in the '.__('awards.award').'\'s description.') !!}
                        {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('notes', 'Notes (Optional)') !!} {!! add_help('Additional notes for the '.__('awards.award').'. This will appear in the '.__('awards.award').'\'s description, but not in the logs.') !!}
                        {!! Form::text('notes', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::checkbox('disallow_transfer', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                        {!! Form::label('disallow_transfer', ucfirst(__('lorekeeper.character')).'-bound', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, the '.__('lorekeeper.character').'\'s owner will not be able to transfer this '.__('awards.award').' to their '.__('awards.awardcase').'. '.ucfirst(__('awards.awards')).' that disallow transfers by default will still not be transferrable.') !!}
                    </div>

                    <div class="text-right">
                        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                    </div>

                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')

@include('widgets._awardcase_select_js', ['readOnly' => true])

<script>

$( document ).ready(function() {
    $('.awards-stack').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent();
        loadModal("{{ url(__('awards.awards')) }}/character/" + $parent.data('id'), $parent.data('name'));
    });

    $('.default.award-select').selectize();
        $('#add-award').on('click', function(e) {
            e.preventDefault();
            addAwardRow();
        });
        $('.remove-award').on('click', function(e) {
            e.preventDefault();
            removeAwardRow($(this));
        })
        function addAwardRow() {
            var $rows = $("#awardList > div")
            if($rows.length === 1) {
                $rows.find('.remove-award').removeClass('disabled')
            }
            var $clone = $('.award-row').clone();
            $('#awardList').append($clone);
            $clone.removeClass('hide award-row');
            $clone.addClass('d-flex');
            $clone.find('.remove-award').on('click', function(e) {
                e.preventDefault();
                removeAwardRow($(this));
            })
            $clone.find('.award-select').selectize();
        }
        function removeAwardRow($trigger) {
            $trigger.parent().remove();
            var $rows = $("#awardList > div")
            if($rows.length === 1) {
                $rows.find('.remove-award').addClass('disabled')
            }
        }
});

</script>
@endsection
