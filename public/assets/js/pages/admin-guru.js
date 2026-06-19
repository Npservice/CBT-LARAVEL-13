let currentPage = 1;

const debouncedSearch = DebounceHelper.debounce(function() {
    loadGuru(1);
}, 500);

$(document).ready(function() {
    loadUsers();
    $('#searchInput').val('');
    loadGuru(1);

    $('#searchInput').on('keyup', function() {
        debouncedSearch();
    });

    $('#guruForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
});

function loadUsers() {
    $.ajax({
        url: '/api/v1/users',
        type: 'GET',
        data: { per_page: 100 },
        success: function(response) {
            const select = $('#user_id');
            select.html('<option value="">-- Pilih User --</option>');
            response.data.forEach(user => {
                select.append(`<option value="${user.id}">${user.name}</option>`);
            });
        }
    });
}

function loadGuru(page = 1, perPage = null) {
    if (!perPage) {
        const selectedPerPage = $('#perPageSelect').val();
        perPage = selectedPerPage ? parseInt(selectedPerPage) : 10;
    }

    const search = $('#searchInput').val();
    currentPage = page;

    $.ajax({
        url: '/api/v1/guru',
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
            Toast.error('Gagal memuat data guru');
        }
    });
}

function renderTable(guru) {
    const tbody = $('#tableBody');
    tbody.empty();

    if (!guru || guru.length === 0) {
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

    guru.forEach((item, index) => {
        const bgClass = index % 2 === 0 ? '' : 'bg-slate-50/50';
        const statusBadge = item.is_aktif
            ? '<span class="inline-flex items-center gap-2 text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-xs font-semibold">Aktif</span></span>'
            : '<span class="inline-flex items-center gap-2 text-slate-700 bg-slate-50 px-3 py-1 rounded-full"><span class="w-2 h-2 rounded-full bg-slate-500"></span><span class="text-xs font-semibold">Nonaktif</span></span>';

        const row = `
            <tr class="${bgClass} hover:bg-sky-50/80 transition-colors">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">${item.nig}</td>
                <td class="px-6 py-4 text-sm text-slate-900">${item.nama}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${item.email || '-'}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${item.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                <td class="px-6 py-4 text-sm">${statusBadge}</td>
                <td class="px-6 py-4 text-sm text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick="editGuru('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteGuru('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus">
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
    const paginationHTML = PaginationHelper.build(pagination, 'loadGuru');
    $('#paginationContainer').html(paginationHTML);
}

function openCreateModal() {
    if (!hasPermission('create-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }
    $('div[data-modal-id="modal"] [data-modal-title]').text('Tambah Guru Baru');
    $('#guruForm')[0].reset();
    $('#guruId').val('');
    Modal.open('modal');
}

function editGuru(guruId) {
    if (!hasPermission('edit-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }

    $.ajax({
        url: `/api/v1/guru/${guruId}`,
        type: 'GET',
        success: function(response) {
            const guru = response.data;
            $('div[data-modal-id="modal"] [data-modal-title]').text('Edit Guru');
            $('#guruId').val(guru.id);
            $('#user_id').val(guru.user_id);
            $('input[name="nig"]').val(guru.nig);
            $('input[name="nama"]').val(guru.nama);
            $('input[name="email"]').val(guru.email);
            $('select[name="jenis_kelamin"]').val(guru.jenis_kelamin);
            $('input[name="tempat_lahir"]').val(guru.tempat_lahir);
            $('input[name="tanggal_lahir"]').val(guru.tanggal_lahir);
            $('input[name="no_hp"]').val(guru.no_hp);
            $('textarea[name="alamat"]').val(guru.alamat);
            Modal.open('modal');
        },
        error: function() {
            Toast.error('Gagal memuat data guru');
        }
    });
}

function deleteGuru(guruId) {
    if (!hasPermission('delete-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }

    SwalHelper.confirmDelete(
        'Hapus Guru?',
        'Aksi ini tidak dapat dibatalkan. Guru akan dihapus secara permanen dari sistem.',
        function() {
            SwalHelper.loading('Menghapus guru...');

            $.ajax({
                url: `/api/v1/guru/${guruId}`,
                type: 'DELETE',
                success: function() {
                    SwalHelper.loadingSuccess('Berhasil!', 'Guru berhasil dihapus dari sistem.');
                    setTimeout(() => loadGuru(currentPage), 1500);
                },
                error: function(error) {
                    const message = error.responseJSON?.message || 'Gagal menghapus guru';
                    SwalHelper.loadingError('Gagal!', message);
                }
            });
        }
    );
}

function submitForm() {
    const guruId = $('#guruId').val();
    const isCreate = !guruId;
    const url = isCreate ? '/api/v1/guru' : `/api/v1/guru/${guruId}`;
    const method = isCreate ? 'POST' : 'PUT';

    const data = {
        user_id: $('#user_id').val(),
        nig: $('input[name="nig"]').val(),
        nama: $('input[name="nama"]').val(),
        email: $('input[name="email"]').val(),
        jenis_kelamin: $('select[name="jenis_kelamin"]').val(),
        tempat_lahir: $('input[name="tempat_lahir"]').val(),
        tanggal_lahir: $('input[name="tanggal_lahir"]').val(),
        no_hp: $('input[name="no_hp"]').val(),
        alamat: $('textarea[name="alamat"]').val()
    };

    $.ajax({
        url: url,
        type: method,
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function() {
            Toast.success(isCreate ? 'Guru berhasil dibuat' : 'Guru berhasil diupdate');
            Modal.close('modal');
            loadGuru(currentPage);
        },
        error: function(error) {
            const message = error.responseJSON?.message || 'Gagal menyimpan data';
            Toast.error(message);
        }
    });
}
