/**
 * Toast Component
 * Simple notification toast component
 */

const Toast = {
    /**
     * Show toast notification
     * @param {string} message - Toast message
     * @param {string} type - Type: success, error, warning, info
     * @param {number} duration - Duration in milliseconds (default: 5000)
     */
    show: function(message, type = 'info', duration = 5000) {
        const toastTypes = {
            'success': {
                bg: 'bg-emerald-50',
                border: 'border-emerald-200',
                text: 'text-emerald-800',
                icon: '✓'
            },
            'error': {
                bg: 'bg-rose-50',
                border: 'border-rose-200',
                text: 'text-rose-800',
                icon: '✕'
            },
            'warning': {
                bg: 'bg-amber-50',
                border: 'border-amber-200',
                text: 'text-amber-800',
                icon: '⚠'
            },
            'info': {
                bg: 'bg-sky-50',
                border: 'border-sky-200',
                text: 'text-sky-800',
                icon: 'ℹ'
            }
        };

        const config = toastTypes[type] || toastTypes['info'];

        const $toast = $(`
            <div class="fixed top-4 right-4 z-50 rounded-lg shadow-lg p-4 border ${config.bg} ${config.border} animate-in fade-in slide-in-from-right-10">
                <div class="flex items-center space-x-3">
                    <span class="text-lg font-bold ${config.text}">${config.icon}</span>
                    <p class="text-sm font-medium ${config.text}">${message}</p>
                </div>
            </div>
        `);

        $('body').append($toast);

        setTimeout(() => {
            $toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, duration);
    },

    /**
     * Show success toast
     */
    success: function(message, duration = 5000) {
        this.show(message, 'success', duration);
    },

    /**
     * Show error toast
     */
    error: function(message, duration = 5000) {
        this.show(message, 'error', duration);
    },

    /**
     * Show warning toast
     */
    warning: function(message, duration = 5000) {
        this.show(message, 'warning', duration);
    },

    /**
     * Show info toast
     */
    info: function(message, duration = 5000) {
        this.show(message, 'info', duration);
    }
};
