/**
 * Table Component
 * Reusable table with search and filtering
 */

const Table = {
    /**
     * Initialize table search
     * @param {string} searchInputId - ID of search input
     * @param {string} tableBodyId - ID of table body
     */
    initSearch: function(searchInputId, tableBodyId) {
        $(`#${searchInputId}`).on('keyup', function() {
            const query = $(this).val().toLowerCase();
            $(`#${tableBodyId} tr`).each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(query) > -1);
            });
        });
    },

    /**
     * Show empty state
     * @param {string} tableBodyId - ID of table body
     * @param {string} message - Empty message
     * @param {number} colspan - Number of columns
     */
    showEmpty: function(tableBodyId, message = 'No data found', colspan = 6) {
        $(`#${tableBodyId}`).html(`
            <tr>
                <td colspan="${colspan}" class="py-8 px-6 text-center text-slate-400">
                    ${message}
                </td>
            </tr>
        `);
    },

    /**
     * Render table rows from data
     * @param {string} tableBodyId - ID of table body
     * @param {array} data - Array of row data
     * @param {function} renderRow - Function to render row
     */
    render: function(tableBodyId, data, renderRow) {
        const $tbody = $(`#${tableBodyId}`);
        $tbody.empty();

        if (!data || data.length === 0) {
            this.showEmpty(tableBodyId);
            return;
        }

        data.forEach((item, index) => {
            $tbody.append(renderRow(item, index));
        });
    },

    /**
     * Add row to table
     * @param {string} tableBodyId - ID of table body
     * @param {string} html - Row HTML
     */
    addRow: function(tableBodyId, html) {
        $(`#${tableBodyId}`).append(html);
    },

    /**
     * Delete row from table
     * @param {string} rowId - ID of row
     */
    deleteRow: function(rowId) {
        $(`#${rowId}`).fadeOut(300, function() {
            $(this).remove();
        });
    },

    /**
     * Update row in table
     * @param {string} rowId - ID of row
     * @param {string} html - New row HTML
     */
    updateRow: function(rowId, html) {
        $(`#${rowId}`).replaceWith(html);
    }
};
