<div class="row world-entry">
    @if($imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $imageUrl }}" data-lightbox="entry" data-title="{{ $name }}"><img src="{{ $imageUrl }}" class="world-entry-image" /></a></div>
    @endif
    <div class="{{ $imageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>
            {!! $name !!}
            @if(Auth::check() && Auth::user()->hasPower('edit_data')) <a data-toggle="tooltip" title="[ADMIN] Edit Category" href="{{ url('admin/data/award-categories/edit/').'/'.$category->id }}" class="float-right small ml-2"><i class="fas fa-crown"></i></a>@endif
            @if(isset($searchUrl) && $searchUrl) <a href="{{ $searchUrl }}" class="world-entry-search text-muted float-right small"><i class="fas fa-search"></i></a>  @endif
        </h3>

        @if($category->is_character_owned == 1)
        <div><strong>Characters can own {{ $category->character_limit != 0 ? $category->character_limit : '' }} {{__('awards.awards')}} in this category!</strong></div>
        @endif
        <div class="world-entry-text">
            {!! $description !!}
        </div>
    </div>
</div>
