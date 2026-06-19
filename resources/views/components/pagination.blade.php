@props([
    'currentPage' => 1,
    'totalPages' => 1,
    'totalRecords' => 0,
    'recordsPerPage' => 10,
    'onPageChange' => null,
    'class' => ''
])

@php
    $startRecord = ($currentPage - 1) * $recordsPerPage + 1;
    $endRecord = min($currentPage * $recordsPerPage, $totalRecords);
    $hasPrevious = $currentPage > 1;
    $hasNext = $currentPage < $totalPages;

    // Build the list of page numbers to render (with '...' for gaps)
    if ($totalPages <= 7) {
        $pages = range(1, $totalPages);
    } elseif ($currentPage <= 4) {
        $pages = [1, 2, 3, 4, 5, '...', $totalPages];
    } elseif ($currentPage >= $totalPages - 3) {
        $pages = [1, '...', $totalPages - 4, $totalPages - 3, $totalPages - 2, $totalPages - 1, $totalPages];
    } else {
        $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $totalPages];
    }
@endphp

<div class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6 {{ $class }}">
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Records Info --}}
        <p class="text-center text-xs text-slate-600 sm:text-left">
            @if ($totalRecords > 0)
                Showing
                <span class="font-semibold text-slate-900">{{ $startRecord }}</span>
                to
                <span class="font-semibold text-slate-900">{{ $endRecord }}</span>
                of
                <span class="font-semibold text-slate-900">{{ $totalRecords }}</span>
                records
            @else
                No records found
            @endif
        </p>

        <nav class="flex items-center justify-center gap-1" aria-label="Pagination">
            {{-- Previous --}}
            <button
                type="button"
                id="prevBtn"
                @if ($hasPrevious && $onPageChange)
                    onclick="{{ $onPageChange }}({{ $currentPage - 1 }})"
                @endif
                @disabled(!$hasPrevious)
                aria-label="Previous page"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-slate-500"
            >
                <i class="fas fa-chevron-left text-xs"></i>
            </button>

            {{-- Page numbers (desktop) --}}
            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($pages as $page)
                    @if ($page === '...')
                        <span class="inline-flex h-8 w-8 items-center justify-center text-sm text-slate-400">…</span>
                    @else
                        <button
                            type="button"
                            @if ($onPageChange && $page !== $currentPage)
                                onclick="{{ $onPageChange }}({{ $page }})"
                            @endif
                            @if ($page === $currentPage) aria-current="page" @endif
                            @class([
                                'inline-flex h-8 min-w-[2rem] items-center justify-center rounded-md px-2 text-xs font-medium transition',
                                'bg-sky-500 text-white shadow-sm' => $page === $currentPage,
                                'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900' => $page !== $currentPage,
                            ])
                        >
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            </div>

            {{-- Page indicator (mobile) --}}
            <span class="px-3 text-xs text-slate-600 sm:hidden">
                    Page <span class="font-semibold text-slate-900">{{ $currentPage }}</span> of {{ $totalPages }}
                </span>

            {{-- Next --}}
            <button
                type="button"
                id="nextBtn"
                @if ($hasNext && $onPageChange)
                    onclick="{{ $onPageChange }}({{ $currentPage + 1 }})"
                @endif
                @disabled(!$hasNext)
                aria-label="Next page"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-slate-500"
            >
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </nav>
    </div>
</div>
