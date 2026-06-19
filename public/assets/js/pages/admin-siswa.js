let currentPage = 1;

const debouncedSearch = DebounceHelper.debounce(function() {
    loadSiswa(1);
}, 500);

$(document).ready(function() {
    $('#searchInput').val('');
    loadUserOptions();
    loadKelasOptions();
    loadSiswa(1);

    $('#searchInput').on('keyup', function() {
        debouncedSearch();
    });

    $('#siswaForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
});

function loadUserOptions() {
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

function loadKelasOptions() {
    $.ajax({
        url: '/api/v1/kelas',
        type: 'GET',
        data: { per_page: 100 },
        success: function(response) {
            const select = $('#kelas_id');
            select.html('<option value="">-- Pilih Kelas --</option>');
            response.data.forEach(k => {
                const jurusan = k.jurusan ? ' - ' + k.jurusan.kode_jurusan : '';
                select.append(`<option value="${k.id}">${k.kode_kelas}${jurusan}</option>`);
            });
            select.trigger('change');
        }
    });
}

function loadSiswa(page = 1, perPage = null) {
    if (!perPage) {
        const selectedPerPage = $('#perPageSelect').val();
        perPage = selectedPerPage ? parseInt(selectedPerPage) : 10;
    }

    const search = $('#searchInput').val();
    currentPage = page;

    $.ajax({
        url: '/api/v1/siswa',
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
            Toast.error('Gagal memuat data siswa');
        }
    });
}

function renderTable(siswa) {
    const tbody = $('#tableBody');
    tbody.empty();

    if (!siswa || siswa.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-inbox text-3xl text-slate-400"></i>
                        <p class="text-sm text-slate-500">Tidak ada data</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    siswa.forEach((item, index) => {
        const bgClass = index % 2 === 0 ? '' : 'bg-slate-50/50';
        const statusBadge = item.is_aktif
            ? '<span class="inline-flex items-center gap-2 text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span><span class="text-xs font-semibold">Aktif</span></span>'
            : '<span class="inline-flex items-center gap-2 text-slate-700 bg-slate-50 px-3 py-1 rounded-full"><span class="w-2 h-2 rounded-full bg-slate-500"></span><span class="text-xs font-semibold">Nonaktif</span></span>';

        const kelasLabel = item.kelas
            ? item.kelas.kode_kelas + (item.kelas.jurusan ? ' - ' + item.kelas.jurusan.kode_jurusan : '')
            : '-';

        const row = `
            <tr class="${bgClass} hover:bg-sky-50/80 transition-colors">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">${item.nis}</td>
                <td class="px-6 py-4 text-sm text-slate-900">${item.nama}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${kelasLabel}</td>
                <td class="px-6 py-4 text-sm">${statusBadge}</td>
                <td class="px-6 py-4 text-sm text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick="editSiswa('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteSiswa('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus">
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
    const paginationHTML = PaginationHelper.build(pagination, 'loadSiswa');
    $('#paginationContainer').html(paginationHTML);
}

function openCreateModal() {
    if (!hasPermission('create-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }
    $('div[data-modal-id="modal"] [data-modal-title]').text('Tambah Siswa Baru');
    $('#siswaForm')[0].reset();
    $('#siswaId').val('');
    $('#kelas_id').val(null).trigger('change');
    Modal.open('modal');
}

function editSiswa(siswaId) {
    if (!hasPermission('edit-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }

    $.ajax({
        url: `/api/v1/siswa/${siswaId}`,
        type: 'GET',
        success: function(response) {
            const siswa = response.data;
            $('div[data-modal-id="modal"] [data-modal-title]').text('Edit Siswa');
            $('#siswaId').val(siswa.id);
            $('#user_id').val(siswa.user_id);
            $('input[name="nis"]').val(siswa.nis);
            $('input[name="nisn"]').val(siswa.nisn);
            $('input[name="nama"]').val(siswa.nama);
            $('input[name="email"]').val(siswa.email);
            $('select[name="jenis_kelamin"]').val(siswa.jenis_kelamin);
            $('#kelas_id').val(siswa.kelas_id).trigger('change');
            $('input[name="tempat_lahir"]').val(siswa.tempat_lahir);
            $('input[name="tanggal_lahir"]').val(siswa.tanggal_lahir);
            $('input[name="no_hp"]').val(siswa.no_hp);
            $('textarea[name="alamat"]').val(siswa.alamat);
            Modal.open('modal');
        },
        error: function() {
            Toast.error('Gagal memuat data siswa');
        }
    });
}

function deleteSiswa(siswaId) {
    if (!hasPermission('delete-users')) {
        Toast.error('Anda tidak memiliki izin');
        return;
    }

    SwalHelper.confirmDelete(
        'Hapus Siswa?',
        'Aksi ini tidak dapat dibatalkan. Siswa akan dihapus secara permanen dari sistem.',
        function() {
            SwalHelper.loading('Menghapus siswa...');

            $.ajax({
                url: `/api/v1/siswa/${siswaId}`,
                type: 'DELETE',
                success: function() {
                    SwalHelper.loadingSuccess('Berhasil!', 'Siswa berhasil dihapus dari sistem.');
                    setTimeout(() => loadSiswa(currentPage), 1500);
                },
                error: function(error) {
                    const message = error.responseJSON?.message || 'Gagal menghapus siswa';
                    SwalHelper.loadingError('Gagal!', message);
                }
            });
        }
    );
}

function submitForm() {
    const siswaId = $('#siswaId').val();
    const isCreate = !siswaId;
    const url = isCreate ? '/api/v1/siswa' : `/api/v1/siswa/${siswaId}`;
    const method = isCreate ? 'POST' : 'PUT';

    const data = {
        user_id: $('#user_id').val(),
        nis: $('input[name="nis"]').val(),
        nisn: $('input[name="nisn"]').val() || null,
        nama: $('input[name="nama"]').val(),
        jenis_kelamin: $('select[name="jenis_kelamin"]').val(),
        kelas_id: $('select[name="kelas_id"]').val(),
        tempat_lahir: $('input[name="tempat_lahir"]').val() || null,
        tanggal_lahir: $('input[name="tanggal_lahir"]').val() || null,
        no_hp: $('input[name="no_hp"]').val() || null,
        alamat: $('textarea[name="alamat"]').val() || null,
    };

    $.ajax({
        url: url,
        type: method,
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function() {
            Toast.success(isCreate ? 'Siswa berhasil dibuat' : 'Siswa berhasil diupdate');
            Modal.close('modal');
            loadSiswa(currentPage);
        },
        error: function(error) {
            const errors = error.responseJSON?.errors;
            if (errors) {
                Object.values(errors).forEach(msg => Toast.error(msg[0]));
            } else {
                const message = error.responseJSON?.message || 'Gagal menyimpan data';
                Toast.error(message);
            }
        }
    });
}
