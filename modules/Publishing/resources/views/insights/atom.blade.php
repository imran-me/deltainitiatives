{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<feed xmlns="http://www.w3.org/2005/Atom">
    <title>{{ setting('site_name', 'Delta Initiatives') }} Insights</title>
    <link href="{{ route('insights.feed') }}" rel="self" type="application/atom+xml"/>
    <link href="{{ route('insights.index') }}" rel="alternate" type="text/html"/>
    <id>{{ route('insights.feed') }}</id>
    <updated>{{ ($articles->first()?->updated_at ?? now())->toAtomString() }}</updated>
@foreach($articles as $a)
    <entry>
        <title>{{ $a->title }}</title>
        <link href="{{ route('insights.show', $a->slug) }}" rel="alternate" type="text/html"/>
        <id>{{ route('insights.show', $a->slug) }}</id>
        <published>{{ ($a->published_at ?? $a->created_at)->toAtomString() }}</published>
        <updated>{{ ($a->updated_at ?? $a->published_at)->toAtomString() }}</updated>
        <author><name>{{ $a->author_name ?: setting('site_name', 'Delta Initiatives') }}</name></author>
        <summary>{{ implode(' ', $a->summaryLines()) }}</summary>
    </entry>
@endforeach
</feed>
