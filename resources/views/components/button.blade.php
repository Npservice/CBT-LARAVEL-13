@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'class' => '',
    'onclick' => null,
    'iconOnly' => false
])

@php
$sizes = [
    'xs' => 'px-2 py-0.5 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$iconSizes = [
    'xs' => 'h-4 w-4',
    'sm' => 'h-5 w-5',
    'md' => 'h-5 w-5',
    'lg' => 'h-6 w-6',
];

$variants = [
    'primary' => 'bg-sky-500 hover:bg-sky-600 text-white',
    'secondary' => 'bg-slate-200 hover:bg-slate-300 text-slate-800',
    'danger' => 'bg-red-500 hover:bg-red-600 text-white',
    'success' => 'bg-emerald-500 hover:bg-emerald-600 text-white',
    'warning' => 'bg-amber-500 hover:bg-amber-600 text-white',
    'info' => 'bg-blue-500 hover:bg-blue-600 text-white',
    'outline' => 'border border-slate-300 text-slate-700 hover:bg-slate-50',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$iconSize = $iconSizes[$size] ?? $iconSizes['md'];
$variantClass = $variants[$variant] ?? $variants['primary'];
$disabledClass = $disabled ? 'opacity-50 cursor-not-allowed' : 'transition-colors';

if ($iconOnly) {
    $buttonClass = "inline-flex items-center justify-center font-medium rounded-lg h-9 w-9 $variantClass $disabledClass $class";
} else {
    $buttonClass = "inline-flex items-center justify-center gap-2 font-medium rounded-lg $sizeClass $variantClass $disabledClass $class";
}
@endphp

<button
    type="{{ $type }}"
    class="{{ $buttonClass }}"
    @if ($disabled) disabled @endif
    @if ($onclick) onclick="{{ $onclick }}" @endif
>
    @if ($loading)
        <i class="fas fa-spinner fa-spin"></i>
    @else
        {{ $slot }}
    @endif
</button>
