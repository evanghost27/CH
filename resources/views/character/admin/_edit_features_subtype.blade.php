{!! Form::label(ucfirst(__('lorekeeper.subtype')).' (Optional)') !!}
{!! Form::select('subtype_id', $subtypes, $image->subtype_id, ['class' => 'form-control', 'id' => 'subtype']) !!}
