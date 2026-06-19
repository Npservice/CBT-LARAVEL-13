/**
 * Select2 Helper Functions
 * Initialize Select2 with custom styling
 */

const Select2Helper = {
    /**
     * Initialize basic Select2 (static options)
     * @param {string} selector - jQuery selector
     * @param {object} options - Custom options
     */
    init: function(selector, options = {}) {
        const defaultOptions = {
            width: '100%',
            allowClear: true,
            placeholder: 'Pilih...',
            language: {
                noResults: () => 'Tidak ada hasil'
            }
        };

        $(selector).select2({
            ...defaultOptions,
            ...options
        });
    },

    /**
     * Initialize Select2 dengan API data (single)
     * @param {string} selector - jQuery selector
     * @param {string} apiUrl - API endpoint
     * @param {object} options - Custom options
     */
    initApi: function(selector, apiUrl, options = {}) {
        const textField = $(selector).data('api-text') || 'text';
        const valueField = $(selector).data('api-value') || 'id';

        const defaultOptions = {
            width: '100%',
            placeholder: 'Cari...',
            allowClear: true,
            ajax: {
                url: apiUrl,
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + this.getToken(),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: (data.data || []).map(item => ({
                            ...item,
                            id: item[valueField],
                            text: item[textField] || item.text || ''
                        })),
                        pagination: {
                            more: data.pagination && params.page < data.pagination.last_page
                        }
                    };
                }
            },
            templateResult: function(data) {
                if (!data.id) return data.text;
                return $(`<span>${data.text}</span>`);
            },
            templateSelection: function(data) {
                return data.text || 'Pilih...';
            },
            language: {
                noResults: () => 'Tidak ada hasil',
                searching: () => 'Mencari...'
            }
        };

        $(selector).select2({
            ...defaultOptions,
            ...options
        });
    },

    /**
     * Initialize Select2 multiple
     * @param {string} selector - jQuery selector
     * @param {object} options - Custom options
     */
    initMultiple: function(selector, options = {}) {
        const defaultOptions = {
            width: '100%',
            allowClear: true,
            placeholder: 'Pilih...',
            multiple: true,
            language: {
                noResults: () => 'Tidak ada hasil'
            }
        };

        $(selector).select2({
            ...defaultOptions,
            ...options
        });
    },

    /**
     * Initialize Select2 multiple dengan API
     * @param {string} selector - jQuery selector
     * @param {string} apiUrl - API endpoint
     * @param {object} options - Custom options
     */
    initMultipleApi: function(selector, apiUrl, options = {}) {
        const defaultOptions = {
            width: '100%',
            placeholder: 'Cari...',
            multiple: true,
            allowClear: true,
            ajax: {
                url: apiUrl,
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + this.getToken(),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.pagination && params.page < data.pagination.last_page
                        }
                    };
                }
            },
            language: {
                noResults: () => 'Tidak ada hasil',
                searching: () => 'Mencari...'
            }
        };

        $(selector).select2({
            ...defaultOptions,
            ...options
        });
    },

    /**
     * Get token from cookie
     */
    getToken: function() {
        const nameEQ = 'api_token=';
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let c = cookies[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return '';
    },

    /**
     * Get selected value(s)
     * @param {string} selector - jQuery selector
     */
    getValue: function(selector) {
        return $(selector).val();
    },

    /**
     * Set value(s)
     * @param {string} selector - jQuery selector
     * @param {string|array} value - Value to set
     */
    setValue: function(selector, value) {
        $(selector).val(value).trigger('change');
    },

    /**
     * Clear select
     * @param {string} selector - jQuery selector
     */
    clear: function(selector) {
        $(selector).val(null).trigger('change');
    },

    /**
     * Disable select
     * @param {string} selector - jQuery selector
     */
    disable: function(selector) {
        $(selector).prop('disabled', true).select2();
    },

    /**
     * Enable select
     * @param {string} selector - jQuery selector
     */
    enable: function(selector) {
        $(selector).prop('disabled', false).select2();
    }
};
