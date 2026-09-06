@props(['active', 'badge' => null])

@php
$classes = ($active ?? false)
            ? 'group flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-xl bg-teal-600 text-white shadow-xs shadow-teal-600/20 transition-all duration-150'
            : 'group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl text-slate-600 hover:text-teal-700 hover:bg-teal-50/80 transition-all duration-150';

$iconClasses = ($active ?? false)
            ? 'w-5 h-5 text-white shrink-0'
            : 'w-5 h-5 text-slate-400 group-hover:text-teal-600 shrink-0 transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <div class="{{ $iconClasses }}">
            {{ $icon }}
        </div>
    @endif
    <span class="flex-1 truncate">{{ $slot }}</span>
    @if($badge)
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ ($active ?? false) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700 group-hover:bg-teal-100 group-hover:text-teal-800' }}">
            {{ $badge }}
        </span>
    @endif
</a>
