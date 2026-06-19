/**
 * Admin Page - User Management
 * Page-specific logic for admin dashboard
 */

const AdminPage = {
    init: function() {
        this.setupEventListeners();
        this.setupModalValidation();
        Table.initSearch('searchInput', 'tableBody');
        Modal.init('modal');
    },

    setupEventListeners: function() {
        const self = this;

        // Open create modal
        $(document).on('click', '[data-action="create"]', function() {
            Modal.open('modal', 'Create New User');
            $('#userForm')[0].reset();
        });

        // Close modal
        $(document).on('click', '[data-action="close-modal"]', function(e) {
            e.preventDefault();
            Modal.close('modal');
        });

        // Form submit
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            self.submitForm();
        });

        // Edit button
        $(document).on('click', '[data-action="edit"]', function() {
            const userId = $(this).data('user-id');
            self.editUser(userId);
        });

        // Delete button
        $(document).on('click', '[data-action="delete"]', function() {
            const userId = $(this).data('user-id');
            self.deleteUser(userId);
        });
    },

    setupModalValidation: function() {
        $('#userForm input[required]').on('invalid', function(e) {
            e.preventDefault();
            $(this).addClass('border-rose-500 ring-2 ring-rose-500');
        });

        $('#userForm input[required]').on('input', function() {
            $(this).removeClass('border-rose-500 ring-2 ring-rose-500');
        });
    },

    submitForm: function() {
        const formData = {
            name: $('[name="name"]').val(),
            username: $('[name="username"]').val(),
            email: $('[name="email"]').val(),
            password: $('[name="password"]').val(),
            role: $('[name="role"]').val()
        };

        if (!this.validateForm(formData)) {
            Toast.error('Please fill all fields correctly');
            return;
        }

        // Simulate API call
        console.log('Submitting user:', formData);

        Toast.success('User created successfully!');
        Modal.close('modal');
        $('#userForm')[0].reset();

        // In production, you would call API here:
        // this.callAPI('POST', '/api/users', formData);
    },

    validateForm: function(data) {
        if (!data.name || !data.username || !data.email || !data.password || !data.role) {
            return false;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            return false;
        }

        // Password validation
        if (data.password.length < 6) {
            return false;
        }

        return true;
    },

    editUser: function(userId) {
        // In production, fetch user data from API
        console.log('Edit user:', userId);
        Toast.info('Edit functionality coming soon');
    },

    deleteUser: function(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            console.log('Delete user:', userId);
            Toast.success('User deleted successfully!');
            // In production, call API to delete
            // this.callAPI('DELETE', `/api/users/${userId}`);
        }
    },

    /**
     * Call API endpoint
     * @param {string} method - HTTP method
     * @param {string} url - API endpoint
     * @param {object} data - Data to send
     */
    callAPI: function(method, url, data = null) {
        $.ajax({
            url: url,
            type: method,
            contentType: 'application/json',
            data: data ? JSON.stringify(data) : null,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Toast.success('Operation successful!');
            },
            error: function(error) {
                Toast.error('Operation failed! ' + (error.responseJSON?.message || ''));
            }
        });
    }
};

// Initialize when document ready
$(document).ready(function() {
    AdminPage.init();
});
