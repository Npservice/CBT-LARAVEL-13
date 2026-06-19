/**
 * SweetAlert2 Helper Functions
 * Modern & Clean custom styling with smooth animations
 */

const SwalHelper = {
    defaultConfig: {
        allowOutsideClick: false,
        allowEscapeKey: true,
        buttonsStyling: false
    },

    /**
     * Confirmation dialog untuk delete
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {function} onConfirm - Callback saat dikonfirmasi
     */
    confirmDelete: function(title, message, onConfirm) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Hapus?',
            html: `<p>${message || 'Aksi ini tidak dapat dibatalkan.'}</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-delete'
            }
        }).then((result) => {
            if (result.isConfirmed && onConfirm) {
                onConfirm();
            }
        });
    },

    /**
     * Success dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {number} timer - Auto close timer (ms)
     */
    success: function(title, message, timer = 2000) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Berhasil!',
            text: message || 'Operasi berhasil dilakukan.',
            icon: 'success',
            customClass: {
                popup: 'swal-success'
            },
            timer: timer,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: true
        });
    },

    /**
     * Error dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     */
    error: function(title, message) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Gagal!',
            text: message || 'Terjadi kesalahan. Silakan coba lagi.',
            icon: 'error',
            customClass: {
                popup: 'swal-error'
            },
            confirmButtonText: 'Tutup'
        });
    },

    /**
     * Warning dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     */
    warning: function(title, message) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Peringatan!',
            text: message || 'Perhatikan peringatan ini.',
            icon: 'warning',
            confirmButtonText: 'OK',
            allowOutsideClick: true
        });
    },

    /**
     * Info dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     */
    info: function(title, message) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Informasi',
            text: message || 'Silakan perhatikan informasi ini.',
            icon: 'info',
            confirmButtonText: 'OK',
            allowOutsideClick: true
        });
    },

    /**
     * Loading dialog dengan custom content
     * @param {string} message - Loading message
     */
    loading: function(message = 'Memproses...') {
        Swal.fire({
            ...this.defaultConfig,
            title: message,
            html: '<div class="flex justify-center"><i class="fas fa-spinner fa-spin text-4xl text-sky-500"></i></div>',
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    /**
     * Close loading dialog dan replace dengan success
     * @param {string} title - Success title
     * @param {string} message - Success message
     * @param {number} timer - Auto close timer
     */
    loadingSuccess: function(title, message, timer = 2000) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Berhasil!',
            text: message || 'Operasi berhasil dilakukan.',
            icon: 'success',
            customClass: {
                popup: 'swal-success'
            },
            timer: timer,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: true,
            allowEscapeKey: true
        });
    },

    /**
     * Close loading dialog dan replace dengan error
     * @param {string} title - Error title
     * @param {string} message - Error message
     */
    loadingError: function(title, message) {
        Swal.fire({
            ...this.defaultConfig,
            title: title || 'Gagal!',
            text: message || 'Terjadi kesalahan.',
            icon: 'error',
            customClass: {
                popup: 'swal-error'
            },
            confirmButtonText: 'Tutup'
        });
    },

    /**
     * Confirm dialog (generic)
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {function} onConfirm - Callback saat dikonfirmasi
     * @param {string} confirmText - Confirm button text
     * @param {string} cancelText - Cancel button text
     */
    confirm: function(title, message, onConfirm, confirmText = 'Konfirmasi', cancelText = 'Batal') {
        Swal.fire({
            ...this.defaultConfig,
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText
        }).then((result) => {
            if (result.isConfirmed && onConfirm) {
                onConfirm();
            }
        });
    }
};
