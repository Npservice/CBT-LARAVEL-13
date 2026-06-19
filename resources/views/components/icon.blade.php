@props(['name' => 'info', 'size' => 'md', 'class' => '', 'style' => 'solid'])

@php
$sizes = [
    'xs' => 'text-xs',
    'sm' => 'text-sm',
    'md' => 'text-base',
    'lg' => 'text-lg',
    'xl' => 'text-xl',
    '2xl' => 'text-2xl',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
$prefix = $style === 'light' ? 'fal' : 'fas';
@endphp

<i class="{{ $prefix }} fa-{{ $name }} {{ $sizeClass }} {{ $class }}" {{ $attributes }}></i>
