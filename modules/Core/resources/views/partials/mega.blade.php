{{-- One mega-menu: $id, $menu = [label, active patterns, items [route, title, blurb], footer links [route, label]], $current. --}}
<div class="has-mega">
    <button type="button" @class(['mega-btn', 'cur' => $current]) aria-expanded="false" aria-controls="{{ $id }}">{{ $menu[0] }}</button>
    <div class="mega" id="{{ $id }}">
        @foreach($menu[2] as [$route, $title, $blurb])
            <a href="{{ localized_route($route) }}"><b>{{ $title }}</b><span>{{ $blurb }}</span></a>
        @endforeach
        <div class="mega-foot">
            @foreach($menu[3] as [$route, $label])
                <a href="{{ route($route) }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>
</div>
