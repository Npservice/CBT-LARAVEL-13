@extends('layouts.admin')

@section('title', 'Mata Pelajaran - Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Mata Pelajaran</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data mata pelajaran</p>
        </div>
        <x-button onclick="openCreateModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah</x-button>
    </div>

    <x-card padding="p-4">
        <input type="text" id="searchInput" placeholder="Cari mata pelajaran..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </x-card>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Kelas</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                            <span>Loading...</span>
                        </div>
                    </td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginationContainer">
            <x-pagination :currentPage="1" :totalPages="1" :totalRecords="0" :recordsPerPage="10" onPageChange="loadData" />
        </div>
    </div>
</div>

<x-modal id="modal" title="Tambah Mata Pelajaran" size="md">
    <form id="form" class="space-y-4" onsubmit="saveData(event)">
        <input type="hidden" id="id" />
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" id="mapel" name="mapel" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode <span class="text-red-500">*</span></label>
            <input type="text" id="kode_mapel" name="kode_mapel" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <x-select id="kelas_ids" name="kelas_ids[]" label="Kelas" placeholder="-- Pilih Kelas --"
            :multiple="true" :required="true" />
        <div class="flex gap-3 pt-4">
            <x-button type="submit" class="flex-1"><i class="fas fa-save"></i> Simpan</x-button>
            <x-button type="button" variant="secondary" onclick="Modal.close('modal')" class="flex-1">Batal</x-button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>
<script src="{{ asset('assets/js/helpers/pagination.js') }}"></script>
<script>
    let currentPage = 1, editingId = null;

    $(document).ready(function() {
        loadKelas();
        loadData(1);
        $('#searchInput').on('keyup', debounce(function() {
            currentPage = 1;
            loadData(1);
        }, 300));
    });

    function loadKelas() {
        $.ajax({
            url: '/api/v1/kelas',
            data: { per_page: 200 },
            success: function(r) {
                r.data.forEach(k => {
                    const label = k.jurusan
                        ? `${k.kode_kelas} - ${k.jurusan.kode_jurusan}`
                        : k.kode_kelas;
                    $('#kelas_ids').append(`<option value="${k.id}">${label}</option>`);
                });
                $('#kelas_ids').trigger('change');
            }
        });
    }

    function loadData(page) {
        currentPage = page || currentPage;
        $.ajax({
            url: '/api/v1/mata-pelajaran',
            data: { search: $('#searchInput').val(), page: currentPage, per_page: 10 },
            success: function(response) {
                const tbody = $('#tableBody');
                tbody.html('');
                if (response.data.length === 0) {
                    tbody.html('<tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>');
                    return;
                }
                response.data.forEach(item => {
                    const kelasBadges = item.kelas.length
                        ? item.kelas.map(k => {
                            const label = k.jurusan
                                ? `${k.kode_kelas} - ${k.jurusan.kode_jurusan}`
                                : k.kode_kelas;
                            return `<span class="inline-block px-1.5 py-0.5 rounded text-[11px] font-medium bg-sky-50 text-sky-700">${label}</span>`;
                          }).join(' ')
                        : '<span class="text-slate-400">-</span>';

                    tbody.append(`
                        <tr class="hover:bg-sky-50/80 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">${item.mapel}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">${item.kode_mapel}</td>
                            <td class="px-6 py-4"><div class="flex flex-wrap gap-1">${kelasBadges}</div></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="editData('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteData('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                updatePagination(response.pagination, 'loadData');
            },
            error: function() { Toast.error('Gagal memuat'); }
        });
    }

    function openCreateModal() {
        editingId = null;
        $('#mapel').val('');
        $('#kode_mapel').val('');
        $('#kelas_ids').val([]).trigger('change');
        $('#modal [data-modal-title]').text('Tambah Mata Pelajaran');
        Modal.open('modal');
    }

    function editData(id) {
        $.ajax({
            url: `/api/v1/mata-pelajaran/${id}`,
            success: function(response) {
                editingId = id;
                $('#id').val(id);
                $('#mapel').val(response.data.mapel);
                $('#kode_mapel').val(response.data.kode_mapel);
                $('#kelas_ids').val(response.data.kelas.map(k => k.id)).trigger('change');
                $('#modal [data-modal-title]').text('Edit Mata Pelajaran');
                Modal.open('modal');
            }
        });
    }

    function saveData(e) {
        e.preventDefault();
        const payload = {
            mapel:     $('#mapel').val(),
            kode_mapel: $('#kode_mapel').val(),
            kelas_ids: $('#kelas_ids').val() || [],
        };
        $.ajax({
            url: editingId ? `/api/v1/mata-pelajaran/${editingId}` : '/api/v1/mata-pelajaran',
            type: editingId ? 'PUT' : 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function(response) {
                Toast.success(response.message);
                Modal.close('modal');
                loadData(1);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) Object.values(errors).forEach(msg => Toast.error(msg[0]));
            }
        });
    }

    function deleteData(id) {
        SwalHelper.confirmDelete('Hapus Data?', 'Data yang dihapus tidak dapat dikembalikan.', function() {
            $.ajax({
                url: `/api/v1/mata-pelajaran/${id}`,
                type: 'DELETE',
                success: function() { Toast.success('Dihapus'); loadData(1); },
                error: function() { Toast.error('Gagal menghapus'); }
            });
        });
    }

    function updatePagination(pagination, onPageChange) {
        $('#paginationContainer').html(PaginationHelper.build(pagination, onPageChange));
    }

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    }
</script>
@endpush
@endsection
