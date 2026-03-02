@props(['icon', 'label', 'value', 'color' => 'blue', 'sub' => null, 'href' => null])

@php
$colors = [
    'blue'   => ['bg'=>'bg-blue-50',   'icon'=>'bg-blue-500',   'text'=>'text-blue-700'],
    'green'  => ['bg'=>'bg-green-50',  'icon'=>'bg-green-500',  'text'=>'text-green-700'],
    'yellow' => ['bg'=>'bg-yellow-50', 'icon'=>'bg-yellow-500', 'text'=>'text-yellow-700'],
    'red'    => ['bg'=>'bg-red-50',    'icon'=>'bg-red-500',    'text'=>'text-red-700'],
    'indigo' => ['bg'=>'bg-indigo-50', 'icon'=>'bg-indigo-500', 'text'=>'text-indigo-700'],
    'purple' => ['bg'=>'bg-purple-50', 'icon'=>'bg-purple-500', 'text'=>'text-purple-700'],
];
$c = $colors[$color] ?? $colors['blue'];
$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="{{ $c['bg'] }} rounded-2xl p-5 flex items-center gap-4 {{ $href ? 'hover:shadow-md transition-shadow cursor-pointer' : '' }}">
    <div class="w-12 h-12 {{ $c['icon'] }} rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
        <i class="{{ $icon }} text-white text-xl"></i>
    </div>
    <div>
        <p class="text-2xl font-bold {{ $c['text'] }}">{{ $value }}</p>
        <p class="text-sm text-gray-600 font-medium">{{ $label }}</p>
        @if($sub)
        <p class="text-xs text-gray-400 mt-0.5">{{ $sub }}</p>
        @endif
    </div>
</{{ $tag }}>
