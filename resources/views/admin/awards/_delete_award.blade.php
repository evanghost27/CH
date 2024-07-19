@if($award)
    {!! Form::open(['url' => 'admin/data/awards/delete/'.$award->id]) !!}

    <p>You are about to delete the {{__('awards.award')}} <strong>{{ $award->name }}</strong>. This is not reversible. If this {{__('awards.award')}} exists in at least one user or {{__('lorekeeper.character')}}'s possession, you will not be able to delete this {{__('awards.award')}}.</p>
    <p>Are you sure you want to delete <strong>{{ $award->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete '.ucfirst(__('awards.award')), ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid {{__('awards.award')}} selected.
@endif
