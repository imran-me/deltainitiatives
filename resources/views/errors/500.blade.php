@php(view()->share('errors', $errors ?? new \Illuminate\Support\ViewErrorBag))
<x-layouts.public title="Something went wrong" :noindex="true">
@include('errors.partials.body', ['code' => '500', 'headline' => 'We hit a snag on our side.', 'lead' => 'It is not you. Try again in a moment, or search the site and pick up from one of these.'])
</x-layouts.public>
