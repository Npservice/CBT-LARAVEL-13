@props(['type' => 'info', 'title' => '', 'dismissible' => false])

@php
$types = [
    'success' => [
        'bg' => 'bg-emerald-50',
        'border' => 'border-emerald-200',
        'text' => 'text-emerald-800',
        'icon' => '✓'
    ],
    'error' => [
        'bg' => 'bg-rose-50',
        'border' => 'border-rose-200',
        'text' => 'text-rose-800',
        'icon' => '✕'
    ],
    'warning' => [
        'bg' => 'bg-amber-50',
        'border' => 'border-amber-200',
        'text' => 'text-amber-800',
        'icon' => '⚠'
    ],
    'info' => [
        'bg' => 'bg-sky-50',
        'border' => 'border-sky-200',
        'text' => 'text-sky-800',
        'icon' => 'ℹ'
    ]
];
$config = $types[$type] ?? $types['info'];
@endphp

<div class="rounded-lg border {{ $config['border'] }} {{ $config['bg'] }} p-4 flex items-start gap-3">
    <span class="text-lg font-bold {{ $config['text'] }} flex-shrink-0 mt-0.5">{{ $config['icon'] }}</span>
    <div class="flex-1">
        @if ($title)
            <h3 class="font-semibold {{ $config['text'] }}">{{ $title }}</h3>
            <p class="text-sm {{ $config['text'] }} opacity-90 mt-1">{{ $slot }}</p>
        @else
            <p class="text-sm font-medium {{ $config['text'] }}">{{ $slot }}</p>
        @endif
    </div>
    @if ($dismissible)
        <button type="button" onclick="this.parentElement.remove()" class="{{ $config['text'] }} hover:opacity-70 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</div>
