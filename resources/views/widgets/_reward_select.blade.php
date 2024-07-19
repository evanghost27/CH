<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addReward">Add Reward</a>
</div>
<table class="table table-sm" id="rewardTable">
    <thead>
        <tr>
            <th width="35%">Reward Type</th>
            <th width="35%">Reward</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="rewardTableBody">
        @if($loots)
            @foreach($loots as $loot)
                <tr class="reward-row">
                    <td>{!! Form::select('award_type[]', ['Item' => 'Item', 'Currency' => 'Currency'] + ($showLootTables ? ['LootTable' => 'Loot Table'] : []) + ($showRaffles ? ['Raffle' => 'Raffle Ticket'] : []), $loot->type, ['class' => 'form-control award-type', 'placeholder' => 'Select Reward Type']) !!}</td>
                    <td class="reward-row-select">
                        @if($loot->type == 'Item')
                            {!! Form::select('award_id[]', $items, $loot->type_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($loot->type == 'Currency')
                            {!! Form::select('award_id[]', $currencies, $loot->type_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @elseif($loot->type == 'Award')
                            {!! Form::select('award_id[]', $awards, $loot->type_id, ['class' => 'form-control award-select selectize', 'placeholder' => 'Select '.ucfirst(__('awards.award')) ]) !!}
                        @elseif($showLootTables && $loot->type == 'LootTable')
                            {!! Form::select('award_id[]', $tables, $loot->type_id, ['class' => 'form-control table-select selectize', 'placeholder' => 'Select Loot Table']) !!}
                        @elseif($showRaffles && $loot->type == 'Raffle')
                            {!! Form::select('award_id[]', $raffles, $loot->type_id, ['class' => 'form-control raffle-select selectize', 'placeholder' => 'Select Raffle']) !!}
                    @endif
                    </td>
                    <td>{!! Form::text('award_quantity[]', $loot->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-reward-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
