@extends('layouts.admin')

@section('title', 'Siswa Management - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Manajemen Siswa</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data siswa sistem VEXTA</p>
        </div>
        <x-button id="createSiswaBtn" size="sm" variant="primary" onclick="openCreateModal()" class="gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah Siswa</span>
        </x-button>
    </div>

    <!-- Search Bar -->
    <x-card padding="p-4">
        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama, nis atau nisn..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </x-card>

    <!-- Siswa Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                                <span class="text-sm">Loading data...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer">
            <x-pagination
                :currentPage="1"
                :totalPages="1"
                :totalRecords="0"
                :recordsPerPage="10"
                onPageChange="loadSiswa"
            />
        </div>
    </div>
</div>

<!-- Siswa Modal -->
<x-modal id="modal" title="Tambah Siswa Baru" size="xl">
    <form id="siswaForm" class="space-y-4">
        <input type="hidden" id="siswaId" name="id" />

        <!-- Row 0: User -->
        <div>
            <x-select
                id="user_id"
                name="user_id"
                label="User"
                placeholder="Pilih User"
                required
            />
        </div>

        <!-- Row 1: NIS & NISN -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">NISN (Nomor Induk Siswa Nasional)</label>
                <input type="text" name="nisn" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
        </div>

        <!-- Row 2: Nama & Jenis Kelamin -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" required />
            </div>
            <div>
                <x-select
                    id="jenis_kelamin"
                    name="jenis_kelamin"
                    label="Jenis Kelamin"
                    :options="['L' => 'Laki-laki', 'P' => 'Perempuan']"
                    placeholder="Pilih Jenis Kelamin"
                    required
                />
            </div>
        </div>

        <!-- Row 3: Kelas -->
        <div>
            <x-select
                id="kelas_id"
                name="kelas_id"
                label="Kelas"
                placeholder="-- Pilih Kelas --"
                required
            />
        </div>

        <!-- Row 4: Tempat Lahir (Single) -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
        </div>

        <!-- Row 5: Tanggal Lahir & No HP -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">No HP</label>
                <input type="text" name="no_hp" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
        </div>

        <!-- Row 6: Alamat (Full Width) -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
        </div>

        <div class="flex gap-3 pt-4">
            <x-button type="submit" size="md" variant="primary" class="flex-1">
                <i class="fas fa-save"></i> Simpan
            </x-button>
            <x-button type="button" size="md" variant="secondary" onclick="Modal.close('modal')" class="flex-1">
                Batal
            </x-button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>
<script src="{{ asset('assets/js/helpers/debounce.js') }}"></script>
<script src="{{ asset('assets/js/helpers/pagination.js') }}"></script>
<script src="{{ asset('assets/js/pages/admin-siswa.js') }}"></script>
@endpush
@endsection
