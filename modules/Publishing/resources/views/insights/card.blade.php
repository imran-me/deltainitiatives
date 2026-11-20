<a class="ins art rv{{ ($big ?? false) ? ' bento-big' : '' }}" href="{{ route('insights.show', $article) }}">
    <x-publishing::picture :image="$article->cover_key" :upload="$article->cover_path" sizes="(max-width: 760px) 100vw, 33vw" class="card-pic" />
    <span class="type">{{ $article->categoryName() }}</span>
    @if($article->status_label)<span @class(['tag', 'gold' => str_starts_with((string) $article->status_label, 'Published')])>{{ $article->status_label }}</span>@endif
    <h3>{{ $article->title }}</h3>
    <p>{{ \Illuminate\Support\Str::limit(implode(' ', $article->summaryLines()), 140) }}</p>
    <p class="meta-line">{{ $article->minutes() }} min read @if($article->is_demo)· <span class="sample">Sample</span>@endif</p>
</a>
