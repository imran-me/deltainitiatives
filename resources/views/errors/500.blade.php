@php
    $css = [];
    try {
        $m = json_decode((string) @file_get_contents(public_path('build/manifest.json')), true) ?: [];
        foreach (['resources/css/app.css', 'resources/css/site.css'] as $k) {
            if (isset($m[$k]['file'])) { $css[] = '/build/'.$m[$k]['file']; }
        }
    } catch (\Throwable $e) {}
@endphp
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Something went wrong | Delta Initiatives</title>
@foreach($css as $href)
<link rel="stylesheet" href="{{ $href }}">
@endforeach
</head>
<body>
<main class="phead nf"><div class="wrap phead-in"><div>
<div class="eyebrow">Error 500</div>
<h1>We hit a snag on our side.</h1>
<p class="lead">It is not you. Try again in a moment, or go back to the home page.</p>
<div class="btns phead-btns"><a class="btn pri" href="/">Go home</a><a class="btn ghost" href="/submit-an-idea">Submit an idea</a></div>
</div></div></main>
</body>
</html>
