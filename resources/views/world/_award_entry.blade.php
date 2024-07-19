<div class="row world-entry align-items-center">
    @if($imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $imageUrl }}" data-lightbox="entry" data-title="{{ $name }}"><img src="{{ $imageUrl }}" class="world-entry-image img-fluid" /></a></div>
    @endif
    <div class="{{ $imageUrl ? 'col-md-9' : 'col-12' }}">
        <div class="card mb-2">
            <div class="card-header d-flex flex-wrap no-gutters">
                <h1 class="col-12">{!! $name !!}
                    <div class="float-md-right small">
                        @if($award->is_character_owned)<i class="fas fa-paw mx-2 small" data-toggle="tooltip" title="This {{ __('awards.award') }} can be held by characters."></i>@endif
                        @if($award->is_user_owned)<i class="fas fa-user mx-2 small" data-toggle="tooltip" title="This {{ __('awards.award') }} can be held by users."></i>@endif
                    </div>
                </h1>
                @if(isset($award->category) && $award->category)
                    <div class="col">
                        <strong>Category:</strong> {{ $award->category->name }}
                    </div>
                @endif
                @if(isset($award->rarity) && $award->rarity)
                    <div class="col">
                        <strong>Rarity:</strong> {{ $award->rarity }}
                    </div>
                @endif
            </div>
            <div class="card-body">
                {!! $description !!}
            </div>
            @if(isset($award->source) && $award->source || isset($award->data['prompts']) && $award->data['prompts'])
                <div class="card-header h5">Availability</div>
                <div class="card-body">
                    @if(isset($award->data['release']) && $award->data['release'])
                        <div><strong>Source:</strong> {!! $award->data['release'] !!}</div>
                    @endif
                    @if(isset($award->data['prompts']) && $award->data['prompts'])
                        <div class="no-gutters d-flex flex-wrap justify-content-center">
                            @foreach($award->prompts as $prompt)<a href="{{ $prompt->url }}" class="btn btn-outline-primary btn-sm mx-1">{{ $prompt->name }}</a> @endforeach
                        </div>
                    @endif
                </div>
            @endif
            @if(isset($award->credits) && $award->credits)
                <div class="card-header h5">Credits</div>
                <div class="card-body d-flex flex-wrap justify-content-center">
                    @foreach($award->prettyCredits as $credit)
                        <span class="btn btn-outline-primary btn-sm mx-1">{!! $credit !!}</span>
                    @endforeach
                </div>
            @endif
            {{-- progression --}}
            @if(count($award->progressions) > 0)
                <div class="card-header h5">{{ ucfirst(__('awards.award')) }} Progress ({{count($award->progressions)}}/{{count($award->progressions)}})</div>
                <div class="card-body d-flex flex-wrap justify-content-center">
                    <p>Note that this looks fully completed, since you are viewing it as an admin. Progression is only visible on the {{__('awards.awards')}} page or user inventory.</p>
                    <div class="row col-12">
                        @foreach($award->progressions as $progression)
                            <div class="col-md-2">
                                {!! $progression->unlocked(null, true) !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
