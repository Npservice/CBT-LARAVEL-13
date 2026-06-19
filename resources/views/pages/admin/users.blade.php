@extends('layouts.admin')

@section('title', 'User Management - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">User Management</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola pengguna sistem VEXTA</p>
        </div>
        <x-button id="createUserBtn" size="sm" variant="primary" onclick="openCreateModal()" class="gap-2">
            <i class="fas fa-plus"></i>
            <span>Tambah User</span>
        </x-button>
    </div>

    <!-- Search Bar -->
    <x-card padding="p-4">
        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau email..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </x-card>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
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
                onPageChange="loadUsers"
            />
        </div>
    </div>
</div>

<!-- User Modal -->
<x-modal id="modal" title="Tambah User Baru" size="md">
    <form id="userForm" class="space-y-4">
        <input type="hidden" id="userId" name="id" />

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" required />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <input type="text" name="username" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" required />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" required />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            <p class="text-xs text-slate-500 mt-1">Kosongkan untuk keep password saat update</p>
        </div>

        <div>
            <x-select
                id="roleSelect"
                name="role"
                label="Role"
                :options="['admin' => 'Admin', 'guru' => 'Guru', 'guru-pembuat-soal' => 'Guru Pembuat Soal', 'siswa' => 'Siswa']"
                placeholder="Pilih Role"
                required
            />
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
<script src="{{ asset('assets/js/pages/admin-users.js') }}"></script>
@endpush
@endsection
