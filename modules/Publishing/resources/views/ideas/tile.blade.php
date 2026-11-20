<a class="itile rv{{ ($big ?? false) ? ' big feat-top' : '' }}" href="{{ route('public-ideas.show', $idea) }}">
    @if($idea->cover_key || $idea->cover_path)
        <x-publishing::picture :image="$idea->cover_key" :upload="$idea->cover_path" sizes="(max-width: 760px) 100vw, 33vw" class="card-pic" />
    @else
        <div class="tile-ph">{{ $idea->areaName() }}</div>
    @endif
    <div class="itile-b">
        @if($idea->is_featured)<span class="tag gold">Featured</span>@elseif($idea->is_demo)<span class="sample">Sample</span>@endif
        <h3>{{ $idea->title }}</h3>
        <x-publishing::rail :stage="$idea->stage" :small="true" />
        <span class="meta-line">{{ $idea->areaName() }} · ♥ {{ (int) ($idea->votes_count ?? 0) }} supporters</span>
    </div>
</a>
