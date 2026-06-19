@props(['title' => '', 'subtitle' => '', 'padding' => 'p-6', 'shadow' => true, 'border' => true])

<div class="bg-white rounded-lg {{ $border ? 'border border-slate-200' : '' }} {{ $shadow ? 'shadow-sm' : '' }} {{ $padding }}">
    @if ($title)
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            @if ($subtitle)
                <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="mt-6 pt-4 border-t border-slate-200">
            {{ $footer }}
        </div>
    @endif
</div>
