@php(view()->share('errors', $errors ?? new \Illuminate\Support\ViewErrorBag))
<x-layouts.public title="Page not found" :noindex="true">
@include('errors.partials.body', ['code' => '404', 'headline' => 'This page drifted downstream.', 'lead' => 'The link may be old or mistyped. Search the site, or start from one of these.'])
</x-layouts.public>
