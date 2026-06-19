let currentPage = 1;

// Debounced search function
const debouncedSearch = DebounceHelper.debounce(function() {
    loadUsers(1);
}, 500);

$(document).ready(function() {
    // Clear search input on page load
    $('#searchInput').val('');

    loadUsers(1);

    // Use debounced search on input
    $('#searchInput').on('keyup', function() {
        debouncedSearch();
    });

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
});

function loadUsers(page = 1, perPage = null) {
    // Get current per_page from dropdown if not provided
    if (!perPage) {
        const selectedPerPage = $('#perPageSelect').val();
        perPage = selectedPerPage ? parseInt(selectedPerPage) : 10;
    }

    const search = $('#searchInput').val();
    currentPage = page;

    $.ajax({
        url: '/api/v1/users',
        type: 'GET',
        data: {
            search: search,
            page: page,
            per_page: perPage
        },
        success: function(response) {
            renderTable(response.data);
            updatePagination(response.pagination);
        },
        error: function() {
            Toast.error('Gagal memuat data user');
        }
    });
}

function renderTable(users) {
    const tbody = $('#tableBody');
    tbody.empty();

    if (!users || users.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-inbox text-3xl text-slate-400"></i>
                        <p class="text-sm text-slate-500">Tidak ada data</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    users.forEach((user, index) => {
        const roleColors = {
            'admin': 'bg-rose-100 text-rose-800',
            'guru': 'bg-emerald-100 text-emerald-800',
            'guru-pembuat-soal': 'bg-amber-100 text-amber-800',
            'siswa': 'bg-sky-100 text-sky-800'
        };
        const roleColor = roleColors[user.role] || 'bg-slate-100 text-slate-800';
        const bgClass = index % 2 === 0 ? '' : 'bg-slate-50/50';

        const row = `
            <tr class="${bgClass} hover:bg-sky-50/80 transition-colors">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">${user.name}</td>
                <td class="px-6 py-4 text-sm text-slate-600 font-mono bg-slate-50/40 px-3 py-1 rounded">${user.username}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${user.email}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="inline-block ${roleColor} px-3 py-1 rounded-full text-xs font-semibold capitalize">
                        ${user.role}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <span class="inline-flex items-center gap-2 text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-semibold">Aktif</span>
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick="editUser('${user.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser('${user.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function updatePagination(pagination) {
    const paginationHTML = PaginationHelper.build(pagination, 'loadUsers');
    $('#paginationContainer').html(paginationHTML);
}

function openCreateModal() {
    if (!hasPermission('create-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }
    $('div[data-modal-id="modal"] [data-modal-title]').text('Tambah User Baru');
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#roleSelect').val(null).trigger('change');
    $('#userForm')[0].querySelector('[name="password"]').required = true;
    Modal.open('modal');
}

function editUser(userId) {
    if (!hasPermission('edit-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }

    $.ajax({
        url: `/api/v1/users/${userId}`,
        type: 'GET',
        success: function(response) {
            const user = response.data;
            $('div[data-modal-id="modal"] [data-modal-title]').text('Edit User');
            $('#userId').val(user.id);
            $('input[name="name"]').val(user.name);
            $('input[name="username"]').val(user.username);
            $('input[name="email"]').val(user.email);
            $('#roleSelect').val(user.role).trigger('change');
            $('input[name="password"]').val('').prop('required', false);
            Modal.open('modal');
        },
        error: function() {
            Toast.error('Gagal memuat data user');
        }
    });
}

function deleteUser(userId) {
    if (!hasPermission('delete-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }

    SwalHelper.confirmDelete(
        'Hapus User?',
        'Aksi ini tidak dapat dibatalkan. User akan dihapus secara permanen dari sistem.',
        function() {
            SwalHelper.loading('Menghapus user...');

            $.ajax({
                url: `/api/v1/users/${userId}`,
                type: 'DELETE',
                success: function() {
                    SwalHelper.loadingSuccess('Berhasil!', 'User berhasil dihapus dari sistem.');
                    setTimeout(() => loadUsers(currentPage), 1500);
                },
                error: function(error) {
                    const message = error.responseJSON?.message || 'Gagal menghapus user';
                    SwalHelper.loadingError('Gagal!', message);
                }
            });
        }
    );
}

function submitForm() {
    const userId = $('#userId').val();
    const isCreate = !userId;
    const url = isCreate ? '/api/v1/users' : `/api/v1/users/${userId}`;
    const method = isCreate ? 'POST' : 'PUT';

    const data = {
        name: $('input[name="name"]').val(),
        username: $('input[name="username"]').val(),
        email: $('input[name="email"]').val(),
        role: $('select[name="role"]').val()
    };

    const password = $('input[name="password"]').val();
    if (password) {
        data.password = password;
    }

    $.ajax({
        url: url,
        type: method,
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function() {
            Toast.success(isCreate ? 'User berhasil dibuat' : 'User berhasil diupdate');
            Modal.close('modal');
            loadUsers(currentPage);
        },
        error: function(error) {
            const message = error.responseJSON?.message || 'Gagal menyimpan data';
            Toast.error(message);
        }
    });
}
