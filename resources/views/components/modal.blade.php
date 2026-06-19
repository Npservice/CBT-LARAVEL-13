@props(['id' => 'modal', 'title' => '', 'size' => 'md', 'closeButton' => true])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

{{-- Modal Backdrop & Container --}}
<div id="{{ $id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" data-modal-id="{{ $id }}">
    {{-- Backdrop dengan animasi fade (full screen) --}}
    <div data-modal-backdrop
         class="fixed inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 -z-10"></div>

    {{-- Modal Content: flex column dengan max-height supaya body bisa scroll --}}
    <div data-modal-content
         class="relative bg-white rounded-lg shadow-xl {{ $sizeClass }} w-full max-h-[calc(100vh-2rem)] flex flex-col overflow-hidden scale-95 opacity-0 transition-all duration-300">
        {{-- Header (tetap di atas, tidak ikut scroll) --}}
        <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-lg font-semibold text-slate-900" data-modal-title>{{ $title }}</h2>
            @if($closeButton)
                <button type="button" onclick="Modal.close('{{ $id }}')"
                        class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Body (scrollable area) --}}
        <div class="px-6 py-4 overflow-y-auto flex-1 modal-scrollable">
            {{ $slot }}
        </div>

        {{-- Footer (tetap di bawah, tidak ikut scroll) --}}
        @if (isset($footer))
            <div class="border-t border-slate-200 px-6 py-3 bg-slate-50 flex-shrink-0">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

{{-- Initialize modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Modal.init('{{ $id }}');
    });
</script>
