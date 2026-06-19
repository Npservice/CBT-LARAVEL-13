@props(['id' => 'table', 'striped' => true, 'hover' => true, 'bordered' => false])

<div class="space-y-4">
    <!-- Search Input -->
    @if (isset($search))
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
        <input type="text" id="{{ $id }}-search" placeholder="Search..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </div>
    @endif

    <!-- Table Container -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <!-- Header -->
                @if (isset($head))
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        {{ $head }}
                    </tr>
                </thead>
                @endif

                <!-- Body -->
                <tbody id="{{ $id }}-body" class="divide-y divide-slate-200 {{ $striped ? 'odd:[&_tr]:bg-slate-50' : '' }} {{ $hover ? 'hover:[&_tr]:bg-slate-50' : '' }}">
                    <tr>
                        <td colspan="100%" class="px-6 py-8 text-center text-slate-500">
                            <div class="text-sm">Loading data...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer with Pagination -->
        @if (isset($footer))
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-3 flex items-center justify-between">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>

<!-- Initialize Search -->
@if (isset($search))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Table.initSearch('{{ $id }}-search', '{{ $id }}-body');
    });
</script>
@endif

<!-- Slot for additional content -->
@if ($slot->isNotEmpty())
{{ $slot }}
@endif
