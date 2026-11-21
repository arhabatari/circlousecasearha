<div class="item-art art-{{ strtolower($catalog->category) }} {{ $artClass ?? '' }}" aria-hidden="true">
<svg viewBox="0 0 220 150" fill="none">
    <ellipse cx="110" cy="129" rx="73" ry="8" fill="currentColor" opacity=".09"/>
    @switch($catalog->category)
        @case('Books')
            <path d="M54 33 99 24l64 20v82l-64-18-45 10V33Z" fill="#386ee7"/><path d="m99 31 57 18v68l-57-16-38 9V41l38-10Z" fill="#fbfdff"/><path d="M99 31v70M110 51l34 10M110 65l34 10M110 79l25 8M70 51l20-5M70 66l20-5M70 81l20-5" stroke="#b6c9f0" stroke-width="4"/>
            @break
        @case('Equipment')
            <path d="m31 125 77-102 80 102H31Z" fill="#5c91df"/><path d="m108 23 15 102H31L108 23Z" fill="#3d6fbb"/><path d="m108 60-31 65h46l-15-65Z" fill="#1a344e"/><path d="m22 126 177 1M108 18v12" stroke="#1a344e" stroke-width="4" stroke-linecap="round"/>
            @break
        @case('Fashion')
            <path d="m75 39 20-17h30l20 17 29 21-18 32-18-12v45H82V80L64 92 46 60l29-21Z" fill="#7c9cdb"/><path d="m95 22 15 29 15-29M94 93h32v20H94V93Z" stroke="#dce7ff" stroke-width="4"/><path d="M106 51v22m9-22v22" stroke="#fff" stroke-width="3"/>
            @break
        @case('Sports')
            <circle cx="57" cy="99" r="29" stroke="#406cb5" stroke-width="7"/><circle cx="164" cy="99" r="29" stroke="#406cb5" stroke-width="7"/><path d="m57 99 29-47 32 47H57l44-31h48l15 31-24-65h18M76 50h24" stroke="#1a344e" stroke-width="5" stroke-linejoin="round" stroke-linecap="round"/>
            @break
        @default
            <rect x="48" y="27" width="124" height="84" rx="8" fill="#1c3b63"/><rect x="55" y="34" width="110" height="68" rx="3" fill="#9ebcfa"/><path d="m58 102 37-38 25 22 45-42v58H58Z" fill="#618de5"/><path d="M32 112h156l-10 12H42l-10-12Z" fill="#5778ab"/><path d="M91 112h38l-5 5H96l-5-5Z" fill="#c0d1ed"/>
    @endswitch
</svg>
</div>
