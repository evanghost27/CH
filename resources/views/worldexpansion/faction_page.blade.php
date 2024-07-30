@extends('worldexpansion.layout')

@section('title') {{ $faction->name }} @endsection

@section('content')
@if(Auth::check() && Auth::user()->hasPower('manage_world'))
    <a data-toggle="tooltip" title="[ADMIN] Edit Faction" href="{{ url('admin/world/factions/edit/').'/'.$faction->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
@endif
{!! breadcrumbs(['World' => 'world', 'Factions' => 'world/factions', $faction->style => 'world/factions/'.$faction->id]) !!}
<h1 style="clear:both;"><img src="{{$faction->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $faction->style !!}</h1>
<h5 class="mb-0">{!! ucfirst($faction->type->displayName) !!} {!! $faction->parent ? 'inside '.$faction->parent->displayName : '' !!}</h5>

@if(($user_enabled && $faction->is_user_faction) || ($ch_enabled && $faction->is_character_faction))
    <p class="mb-0"><strong>
    Can be joined by
    {!! $faction->is_character_faction && $faction->is_user_faction ? 'both' : '' !!}
    {!! $user_enabled && $faction->is_user_faction ? 'users' : '' !!}{!! $faction->is_character_faction && $faction->is_user_faction ? ' and' : '' !!}{!! !$faction->is_character_faction && $faction->is_user_faction ? '.' : '' !!}
    {!! $ch_enabled && $faction->is_character_faction ? 'characters.' : '' !!}
    </strong></p>
@endif


@if($faction->image_extension)
    <div class="text-center"><img src="{{$faction->imageUrl}}" class="mw-100"/></div>
@endif

@isset($faction->summary)
<div class="world-entry-text px-3 text-center">{!! $faction->summary !!}</div>
@endisset

@isset($faction->parsed_description)
<div class="world-entry-text px-3">
    {!! $faction->parsed_description !!}
</div>
@endisset

<div class="row mx-0 px-0 mt-3">
    @if(count($faction->children))
    <div class="text-center col-md mb-3 fb-md-50"><div class="card h-100 py-3">
     <h5 class="mb-0">Contains the following</h5>

        <!-- <hr>
        <p class="mb-0">
            @foreach($faction->children as $key => $child)
                @if($child->thumb_extension)
                    <a href="{{ $child->url }}" data-toggle="tooltip" title="{{ $child->name }}"/><img src="{{$child->thumbUrl}}" class="m-1" style="max-width:100px"/> </a>
                @else
                    {!! $child->displayName !!}
                @endif
            @endforeach
        </p> -->

        <hr>
        @foreach($faction->children->groupBy('type_id') as $group => $children)
        <p class="mb-0">
            <strong>
                @if(count($children) == 1) {{ $loctypes->find($group)->name }}@else{{ $loctypes->find($group)->names }}@endif:
            </strong>
            @foreach($children as $key => $child) {!! $child->fullDisplayName !!}@if($key != count($children)-1), @endif @endforeach
        </p>
        @endforeach
    </div></div>
    @endif

    @if(count($faction->members))
    <div class="text-center col-md mb-3 fb-md-50"><div class="card h-100 py-3">
     <h5 class="mb-0">Member Figure{{ count($faction->members) == 1 ? '' : 's'}}</h5>

        <!-- <hr>
        <p class="mb-0">
            @foreach($faction->members as $key => $member)
                @if($member->thumb_extension)
                    <a href="{{ $member->url }}" data-toggle="tooltip" title="{{ $member->name }}"/><img src="{{$member->thumbUrl}}" class="m-1" style="max-width:100px"/> </a>
                @else
                    {!! $member->displayName !!}
                @endif
            @endforeach
        </p> -->

        <hr>
        @foreach($faction->members->groupBy('category_id') as $key => $members)
        <p class="mb-0">
            <strong>
                {{ $figure_categories->find($key) ? $figure_categories->find($key)->name : 'Miscellanous' }}:
            </strong>
            @foreach($members as $key => $member) <strong>{!! $member->displayName !!}</strong>@if($key != count($members)-1 && count($members)>2),@endif @if($key == count($members)-2) and @endif @endforeach
        </p>
        @endforeach
    </div></div>
    @endif



    @if($faction->ranks()->count())
    <div class="w-100"></div>
    <div class="text-center col-md-12"><div class="card h-100 py-3">
     <h5 class="mb-0">Faction Ranks</h5>

    <hr/>
    <div class="row">
        @if($faction->ranks()->where('is_open', 0)->count())
            <div class="col-md">
                <h4><small>Leadership</small></h4>
                @foreach($faction->ranks()->where('is_open', 0)->orderBy('sort')->get() as $rank)
                    <h6>{{ $rank->name }}{{ $rank->description ? ': '.$rank->description : '' }}</h6>
                    @if($rank->members()->count())
                        <p>
                            @foreach($rank->members as $member)
                                {!! $member->memberObject->displayName !!}{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </p>
                    @endif
                @endforeach
            </div>
        @endif
        @if($faction->ranks()->where('is_open', 1)->count())
            <div class="col-md">
                <h4><small>Member Ranks</small></h4>
                @foreach($faction->ranks()->where('is_open', 1)->orderBy('sort')->get() as $rank)
                    <h6>{{ $rank->name }}{{ $rank->description ? ': '.$rank->description : '' }}{!! $currency ? ' ('.$currency->display($rank->breakpoint).')' : ' ('.$rank->breakpoint.' Standing)' !!}</h6>
                @endforeach
            </div>
        @endif
    </div>
    <hr/>
    <a href="{{ url('world/factions/'.$faction->id.'/members') }}">
        <h4><small>Members: {{ $faction->factionMembers->count() }} ・ See All</small></h4>
    </a>
    </div></div>
    @endif
</div>

<div class="row justify-content-center mx-0 px-0 mt-3">

    @if(count(allAttachments($faction)))
        @foreach(allAttachments($faction) as $type => $attachments)
            <div class="text-center col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-header h3">
                        Associated {{ $type }}{{ count($attachments) == 1 ? '' : 's' }}
                    </div>
                    <div class="card-body">
                        @foreach($attachments as $attachment)
                        <p class="mb-0">
                            {!! $attachment->displayName !!}
                        </p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</div>
@endsection
