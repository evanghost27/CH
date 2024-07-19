<h3>Your {{ ucfirst(__('awards.awardcase')) }} <a class="small awardcase-collapse-toggle collapse-toggle" href="#userAwardcase" data-toggle="collapse">Show</a></h3>
<div class="card mb-3 collapse show" id="userAwardCase">
    <div class="card-body">
        <div class="text-right mb-3">
            <div class="d-inline-block">
                {!! Form::label('award_category_id', 'Filter:', ['class' => 'mr-2']) !!}
                <select class="form-control d-inline-block w-auto" id="userAwardCategory">
                    <option value="all">All Categories</option>
                    <option value="selected">Selected {{ ucfirst(__('awards.awards')) }}</option>
                    <option disabled>&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;&#9472;</option>
                    <option value="0">Miscellaneous</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-inline-block">
                {!! Form::label('award_category_id', 'Action:', ['class' => 'ml-2 mr-2']) !!}
                <a href="#" class="btn btn-primary awardcase-select-all">Select All Visible</a>
                <a href="#" class="btn btn-primary awardcase-clear-selection">Clear Visible Selection</a>
            </div>
        </div>
        <div id="userAwards" class="user-awards">
            <table class="table table-sm">
                <thead class="thead-light">
                    <tr class="d-flex">
                        <th class="col-1"><input id="toggle-checks" type="checkbox"></th>
                        <th class="col-2">{{ ucfirst(__('awards.award')) }}</th>
                        <th class="col-4">Source</th>
                        <th class="col-3">Notes</th>
                        <th class="col-2">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($awardcase as $awardRow)
                        <tr id ="awardRow{{ $awardRow->id }}" class="d-flex {{ $awardRow->isTransferrable ? '' : 'accountbound' }} user-award select-award-row category-all category-{{ $awardRow->award->award_category_id ? : 0 }} {{ isset($selected) && in_array($awardRow->id, array_keys($selected)) ? 'category-selected' : '' }}">
                            <td class="col-1">{!! Form::checkbox((isset($fieldName) && $fieldName ? $fieldName : 'stack_id[]'), $awardRow->id, isset($selected) && in_array($awardRow->id, array_keys($selected)) ? true : false, ['class' => 'awardcase-checkbox']) !!}</td>
                            <td class="col-2">@if(isset($awardRow->award->image_url)) <img class="small-icon" src="{{ $awardRow->award->image_url }}"> @endif {!! $awardRow->award->name !!}
                            <td class="col-4">{!! array_key_exists('data', $awardRow->data) ? ($awardRow->data['data'] ? $awardRow->data['data'] : 'N/A') : 'N/A' !!}</td>
                            <td class="col-3">{!! array_key_exists('notes', $awardRow->data) ? ($awardRow->data['notes'] ? $awardRow->data['notes'] : 'N/A') : 'N/A' !!}</td>
                            @if($awardRow->availableQuantity || in_array($awardRow->id, array_keys($selected)))
                                @if(in_array($awardRow->id, array_keys($selected)))
                                    <td class="col-2">{!! Form::selectRange('stack_quantity[]', $selected[$awardRow->id], $awardRow->getAvailableContextQuantity($selected[$awardRow->id]), 1, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;']) !!} /{{ $awardRow->getAvailableContextQuantity($selected[$awardRow->id]) }} @if($page == 'trade') @if($awardRow->getOthers($selected[$awardRow->id], 0)) {{ $awardRow->getOthers($selected[$awardRow->id], 0) }} @endif @else @if($awardRow->getOthers(0, $selected[$awardRow->id])) {{ $awardRow->getOthers(0, $selected[$awardRow->id]) }} @endif @endif</td>
                                @else
                                    <td class="col-2">{!! Form::selectRange('', 1, $awardRow->availableQuantity, 1, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;']) !!} /{{ $awardRow->availableQuantity }} @if($awardRow->getOthers()) {{ $awardRow->getOthers() }} @endif</td>
                                @endif
                            @else
                                <td class="col-2">{!! Form::selectRange('', 0, 0, 0, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;', 'disabled']) !!} /{{ $awardRow->availableQuantity }} @if($awardRow->getOthers()) {{ $awardRow->getOthers() }} @endif</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
