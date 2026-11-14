<x-layouts.public :title="$title" :noindex="true">
    <x-page-hero :eyebrow="__('newsletter::newsletter.eyebrow')" :title="$title" :intro="$message">
        <x-button :href="route('home')" variant="outline-light" icon-left="arrow-left">{{ __('newsletter::newsletter.back_home') }}</x-button>
    </x-page-hero>
</x-layouts.public>
