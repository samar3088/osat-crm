@php
$icons = [
    'trending-up' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
    'dollar-sign' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    'landmark'    => '<line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/>',
    'percent'     => '<line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
    'bar-chart'   => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
    'activity'    => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
    'arrow-up'    => '<line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>',
];

$colorMap = [
    'blue'   => 'text-blue-500',
    'green'  => 'text-green-500',
    'purple' => 'text-purple-500',
    'orange' => 'text-orange-500',
    'teal'   => 'text-teal-500',
    'red'    => 'text-red-500',
    'yellow' => 'text-yellow-500',
];
@endphp

<svg class="w-6 h-6 stroke-current fill-none stroke-2 {{ $colorMap[$color] ?? 'text-primary' }}"
     viewBox="0 0 24 24">
    {!! $icons[$icon] ?? '' !!}
</svg>