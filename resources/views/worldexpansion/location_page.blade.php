@extends('worldexpansion.layout')

@section('title') {{ $location->style }} @endsection

@section('content')
@if(Auth::check() && Auth::user()->hasPower('manage_world'))
    <a data-toggle="tooltip" title="[ADMIN] Edit Location" href="{{ url('admin/world/locations/edit/').'/'.$location->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
@endif
{!! breadcrumbs(['World' => 'world', 'Locations' => 'world/locations', $location->style => 'world/locations/'.$location->id]) !!}
<h1 style="clear:both;"><img src="{{$location->thumbUrl}}" style="max-height:25px;vertical-align:inherit;"/>{!! $location->style !!}</h1>
<h5 class="mb-0">{!! ucfirst($location->type->displayName) !!} {!! $location->parent ? 'inside '.$location->parent->displayName : '' !!}</h5>

@if(($user_enabled && $location->is_user_home) || ($ch_enabled && $location->is_character_home))
    <p class="mb-0"><strong>
    Can be home to
    {!! $location->is_character_home && $location->is_user_home ? 'both' : '' !!}
    {!! $user_enabled && $location->is_user_home ? 'users' : '' !!}{!! $location->is_character_home && $location->is_user_home ? ' and' : '' !!}{!! !$location->is_character_home && $location->is_user_home ? '.' : '' !!}
    {!! $ch_enabled && $location->is_character_home ? 'characters.' : '' !!}
    </strong></p>
@endif


@if($location->image_extension)
    <div class="text-center"><img src="{{$location->imageUrl}}" class="mw-100"/></div>
@endif

@isset($location->summary)
<div class="world-entry-text px-3 text-center">{!! $location->summary !!}</div>
@endisset

@isset($location->parsed_description)
<div class="world-entry-text px-3">
    {!! $location->parsed_description !!}
</div>
@endisset

<div class="row justify-content-center ">
    @if(count($location->children))
    <div class="text-center col-md mb-3 fb-md-50"><div class="card h-100 py-3">
    <h5 class="mb-0">Contains the following</h5>

        <!-- <hr>
        <p class="mb-0">
            @foreach($location->children as $key => $child)
                @if($child->thumb_extension)
                    <a href="{{ $child->url }}" data-toggle="tooltip" title="{{ $child->name }}"/><img src="{{$child->thumbUrl}}" class="m-1" style="max-width:100px"/> </a>
                @else
                    {!! $child->displayName !!}
                @endif
            @endforeach
        </p> -->

        <hr>
        @foreach($location->children->groupBy('type_id') as $group => $children)
        <p class="mb-0">
            <strong>
                @if(count($children) == 1) {{ $loctypes->find($group)->name }}@else{{ $loctypes->find($group)->names }}@endif:
            </strong>
            @foreach($children as $key => $child) {!! $child->fullDisplayName !!}@if($key != count($children)-1), @endif @endforeach
        </p>
        @endforeach
    </div></div>
    @endif


    @if(count(allAttachments($location)))
        @foreach(allAttachments($location) as $type => $attachments)
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

    @if(count($location->gallerysubmissions))
        <div class="text-center col-md mb-3 fb-md-50"><div class="card h-100 py-3">
            <h5 class="mb-0">Associated Gallery Submission{{ count($location->gallerysubmissions) == 1 ? '' : 's'}}</h5><hr>

        <div class="d-flex align-content-around flex-wrap mb-2">
            @foreach($location->gallerysubmissions->sortByDesc('created_at')->take(3) as $submission)
                @include('galleries._thumb', ['submission' => $submission, 'gallery' => true])
            @endforeach
        </div>
        @if(count($location->gallerysubmissions) > 2)
            <p class="text-right mr-4 mb-0"><a href="{{ url('world/locations/').'/'.$location->id.'/submissions'}}">See All {{count($location->gallerysubmissions)}}</p>
        @endif
    @endif





@endsection
