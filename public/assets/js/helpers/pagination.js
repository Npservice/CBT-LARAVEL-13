/**
 * Pagination Helper
 * Support hybrid pagination: offset-based (page) + cursor-based
 */

const PaginationHelper = {
    /**
     * Build pagination HTML - auto-detect mode from pagination data
     * @param {object} pagination - Pagination data from API response
     * @param {function} onPageChange - Callback function name
     * @returns {string} HTML string for pagination
     */
    build: function(pagination, onPageChange = 'loadData') {
        const mode = pagination.mode || (pagination.current_page ? 'offset' : 'cursor');

        if (mode === 'offset') {
            return this.buildOffset(pagination, onPageChange);
        } else {
            return this.buildCursor(pagination, onPageChange);
        }
    },

    /**
     * Shared markup for the "Rows per page" selector.
     * Reused by both offset and cursor builders so styling stays in sync.
     */
    buildPerPageSelect: function(perPage, onPageChange) {
        const current = Number(perPage);
        const options = [10, 25, 50, 100];
        const optionsHTML = options
            .map(n => `<option value="${n}" ${current === n ? 'selected' : ''}>${n}</option>`)
            .join('');

        return `
            <div class="flex items-center gap-2">
                <label for="perPageSelect" class="text-xs text-slate-600">Rows per page:</label>
                <div class="relative">
                    <select
                        id="perPageSelect"
                        onchange="${onPageChange}(null, this.value)"
                        class="h-8 cursor-pointer appearance-none rounded-md border border-slate-200 bg-white pl-3 pr-8 text-xs font-medium text-slate-700 transition hover:border-slate-300 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
                    >
                        ${optionsHTML}
                    </select>
                    <i class="fas fa-chevron-down pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-400"></i>
                </div>
            </div>
        `;
    },

    /**
     * Build offset-based pagination (page numbers)
     */
    buildOffset: function(pagination, onPageChange) {
        const current = pagination.current_page;
        const last = pagination.last_page;
        const total = pagination.total;
        const totalOriginal = pagination.total_original || total;
        const from = pagination.from;
        const to = pagination.to;
        const hasPrev = current > 1;
        const hasNext = current < last;
        const isFiltered = total !== totalOriginal;

        const pageNumbers = this.buildPageNumbers(current, last);
        const perPageSelect = this.buildPerPageSelect(pagination.per_page, onPageChange);

        let html = `
            <div class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-col items-center gap-3 sm:flex-row sm:gap-5">
                        <p class="text-xs text-slate-600">
                            Showing
                            <span class="font-semibold text-slate-900">${from || 0}</span>
                            to
                            <span class="font-semibold text-slate-900">${to || 0}</span>
                            of
                            <span class="font-semibold text-slate-900">${total}</span>
                            ${isFiltered ? `<span class="text-slate-500">(dari ${totalOriginal} total)</span>` : ''}
                            records
                        </p>
                        <span class="hidden h-3.5 w-px bg-slate-200 sm:block"></span>
                        ${perPageSelect}
                    </div>

                    <nav class="flex items-center justify-center gap-1" aria-label="Pagination">
                        <button
                            type="button"
                            ${!hasPrev ? 'disabled' : `onclick="${onPageChange}(${current - 1})"`}
                            aria-label="Previous page"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 ${!hasPrev ? 'cursor-not-allowed opacity-40 hover:bg-white hover:text-slate-500' : ''}"
                        >
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>

                        <div class="hidden items-center gap-1 sm:flex">
        `;

        pageNumbers.forEach(page => {
            if (page === '...') {
                html += `<span class="inline-flex h-8 w-8 items-center justify-center text-sm text-slate-400">…</span>`;
            } else {
                const isActive = page === current;
                html += `
                    <button
                        type="button"
                        ${page !== current ? `onclick="${onPageChange}(${page})"` : 'aria-current="page"'}
                        class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-md px-2 text-xs font-medium transition ${
                    isActive
                        ? 'bg-sky-500 text-white shadow-sm'
                        : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                }"
                    >
                        ${page}
                    </button>
                `;
            }
        });

        html += `
                        </div>

                        <span class="px-3 text-xs text-slate-600 sm:hidden">
                            Page <span class="font-semibold text-slate-900">${current}</span> of ${last}
                        </span>

                        <button
                            type="button"
                            ${!hasNext ? 'disabled' : `onclick="${onPageChange}(${current + 1})"`}
                            aria-label="Next page"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 ${!hasNext ? 'cursor-not-allowed opacity-40 hover:bg-white hover:text-slate-500' : ''}"
                        >
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </nav>
                </div>
            </div>
        `;

        return html;
    },

    /**
     * Build cursor-based pagination
     */
    buildCursor: function(pagination, onPageChange) {
        const hasNext = pagination.has_next;
        const prevCursor = pagination.prev_cursor;
        const perPage = pagination.per_page;
        const total = pagination.total;
        const totalOriginal = pagination.total_original || total;
        const isFiltered = total !== totalOriginal;

        const perPageSelect = this.buildPerPageSelect(perPage, onPageChange);

        let html = `
            <div class="border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-col items-center gap-3 sm:flex-row sm:gap-5">
                        <p class="text-xs text-slate-600">
                            Showing <span class="font-semibold text-slate-900">${perPage}</span> records
                            ${isFiltered ? `<span class="text-slate-500">(dari ${totalOriginal} total)</span>` : ''}
                        </p>
                        <span class="hidden h-3.5 w-px bg-slate-200 sm:block"></span>
                        ${perPageSelect}
                    </div>

                    <nav class="flex items-center justify-center gap-2" aria-label="Pagination">
                        <button
                            type="button"
                            ${!prevCursor ? 'disabled' : `onclick="${onPageChange}('prev')"`}
                            aria-label="Previous page"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 ${!prevCursor ? 'cursor-not-allowed opacity-40 hover:bg-white hover:text-slate-500' : ''}"
                        >
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>

                        <button
                            type="button"
                            ${!hasNext ? 'disabled' : `onclick="${onPageChange}('next')"`}
                            aria-label="Next page"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 ${!hasNext ? 'cursor-not-allowed opacity-40 hover:bg-white hover:text-slate-500' : ''}"
                        >
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </nav>
                </div>
            </div>
        `;

        return html;
    },

    /**
     * Build page numbers array with ellipsis
     */
    buildPageNumbers: function(current, last) {
        if (last <= 7) {
            return Array.from({ length: last }, (_, i) => i + 1);
        } else if (current <= 4) {
            return [1, 2, 3, 4, 5, '...', last];
        } else if (current >= last - 3) {
            return [1, '...', last - 4, last - 3, last - 2, last - 1, last];
        } else {
            return [1, '...', current - 1, current, current + 1, '...', last];
        }
    }
};
