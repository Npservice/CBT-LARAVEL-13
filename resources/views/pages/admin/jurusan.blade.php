@extends('layouts.admin')

@section('title', 'Jurusan - Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Jurusan</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data jurusan</p>
        </div>
        <x-button onclick="openCreateModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah Jurusan</x-button>
    </div>

    <x-card padding="p-4">
        <input type="text" id="searchInput" placeholder="Cari jurusan..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </x-card>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Nama Jurusan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Kode</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                    <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">
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

<x-modal id="modal" title="Tambah Jurusan" size="md">
    <form id="form" class="space-y-4" onsubmit="saveData(event)">
        <input type="hidden" id="id" />
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Jurusan <span class="text-red-500">*</span></label>
            <input type="text" id="nama_jurusan" name="nama_jurusan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Jurusan <span class="text-red-500">*</span></label>
            <input type="text" id="kode_jurusan" name="kode_jurusan" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
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
        loadData(1);
        $('#searchInput').on('keyup', debounce(function() {
            currentPage = 1;
            loadData(1);
        }, 300));
    });

    function loadData(page) {
        currentPage = page || currentPage;
        $.ajax({
            url: '/api/v1/jurusan',
            data: { search: $('#searchInput').val(), page: currentPage, per_page: 10 },
            success: function(response) {
                const tbody = $('#tableBody');
                tbody.html('');
                if (response.data.length === 0) {
                    tbody.html('<tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>');
                    return;
                }
                response.data.forEach(item => {
                    tbody.append(`
                        <tr class="hover:bg-sky-50/80 transition-colors">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">${item.nama_jurusan}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">${item.kode_jurusan}</td>
                            <td class="px-6 py-4 text-sm text-right">
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
        $('#form')[0].reset();
        $('#modal [data-modal-title]').text('Tambah Jurusan');
        Modal.open('modal');
    }

    function editData(id) {
        $.ajax({
            url: `/api/v1/jurusan/${id}`,
            success: function(response) {
                editingId = id;
                $('#id').val(id);
                $('#nama_jurusan').val(response.data.nama_jurusan);
                $('#kode_jurusan').val(response.data.kode_jurusan);
                $('#modal [data-modal-title]').text('Edit Jurusan');
                Modal.open('modal');
            }
        });
    }

    function saveData(e) {
        e.preventDefault();
        const formData = new FormData($('#form')[0]);
        $.ajax({
            url: editingId ? `/api/v1/jurusan/${editingId}` : '/api/v1/jurusan',
            type: editingId ? 'PUT' : 'POST',
            processData: false, contentType: false,
            data: formData,
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
                url: `/api/v1/jurusan/${id}`,
                type: 'DELETE',
                success: function() {
                    Toast.success('Data dihapus');
                    loadData(1);
                },
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
