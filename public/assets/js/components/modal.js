/**
 * Modal Component
 * Reusable modal dialog component with animations
 */

const Modal = {
    /**
     * Open modal with animation
     * @param {string} modalId - ID of modal element
     */
    open: function(modalId) {
        const $modal = $(`#${modalId}`);
        const $backdrop = $modal.find('[data-modal-backdrop]');
        const $content = $modal.find('[data-modal-content]');

        $modal.removeClass('hidden');

        // Trigger animation
        setTimeout(() => {
            $backdrop.addClass('opacity-100');
            $content.addClass('scale-100 opacity-100');
        }, 10);
    },

    /**
     * Close modal with animation
     * @param {string} modalId - ID of modal element
     */
    close: function(modalId) {
        const $modal = $(`#${modalId}`);
        const $backdrop = $modal.find('[data-modal-backdrop]');
        const $content = $modal.find('[data-modal-content]');

        $backdrop.removeClass('opacity-100');
        $content.removeClass('scale-100 opacity-100');

        setTimeout(() => {
            $modal.addClass('hidden');
        }, 300);
    },

    /**
     * Toggle modal
     * @param {string} modalId - ID of modal element
     */
    toggle: function(modalId) {
        const $modal = $(`#${modalId}`);
        if ($modal.hasClass('hidden')) {
            this.open(modalId);
        } else {
            this.close(modalId);
        }
    },

    /**
     * Clear form inside modal
     * @param {string} modalId - ID of modal element
     */
    clearForm: function(modalId) {
        const form = $(`#${modalId}`).find('form')[0];
        if (form) form.reset();
    },

    /**
     * Initialize modal with backdrop click close
     * @param {string} modalId - ID of modal element
     */
    init: function(modalId) {
        const self = this;
        const $modal = $(`#${modalId}`);

        $modal.on('click', function(e) {
            if (e.target === this || $(e.target).attr('data-modal-backdrop') !== undefined) {
                self.close(modalId);
            }
        });
    }
};
