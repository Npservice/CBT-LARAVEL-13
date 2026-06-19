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
        <button id="createUserBtn" onclick="openCreateModal()" class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition hidden">
            + Tambah User
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau email..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            <div class="text-sm">Loading data...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-slate-50 border-t border-slate-200 px-6 py-3 flex items-center justify-between">
            <span id="recordsInfo" class="text-xs text-slate-600">Showing 0 records</span>
            <div class="flex gap-2">
                <button id="prevBtn" onclick="loadUsers(currentPage - 1)" class="px-3 py-1 border border-slate-300 rounded text-sm text-slate-600 hover:bg-white transition disabled:opacity-50">
                    Sebelumnya
                </button>
                <button id="nextBtn" onclick="loadUsers(currentPage + 1)" class="px-3 py-1 border border-slate-300 rounded text-sm text-slate-600 hover:bg-white transition disabled:opacity-50">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>
</div>

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
            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
            <select name="role" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
            </select>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white py-2 rounded-lg text-sm font-medium transition">
                Simpan
            </button>
            <button type="button" onclick="Modal.close('modal')" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-800 py-2 rounded-lg text-sm font-medium transition">
                Batal
            </button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>

<script>
    let currentPage = 1;
    let totalPages = 1;

    // Show create button if user has permission
    $(window).on('userLoaded', function() {
        if (currentUser && hasPermission('create-users')) {
            $('#createUserBtn').show();
        }
    });

    // Load users on page load
    $(document).ready(function() {
        loadUsers(1);

        // Search handler
        $('#searchInput').on('keyup', function() {
            loadUsers(1);
        });

        // Form submit
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    });

    function loadUsers(page = 1) {
        const search = $('#searchInput').val();

        $.ajax({
            url: '/api/v1/users',
            type: 'GET',
            data: {
                search: search,
                page: page,
                per_page: 10
            },
            success: function(response) {
                renderTable(response.data);
                updatePagination(response.pagination);
                currentPage = response.pagination.current_page;
                totalPages = response.pagination.last_page;
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
            tbody.html('<tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Tidak ada data</td></tr>');
            return;
        }

        users.forEach(user => {
            const row = `
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-sm font-medium text-slate-900">${user.name}</td>
                    <td class="px-6 py-3 text-sm text-slate-600 font-mono">${user.username}</td>
                    <td class="px-6 py-3 text-sm text-slate-600">${user.email}</td>
                    <td class="px-6 py-3 text-sm">
                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm">
                        <span class="inline-flex items-center gap-1 text-green-600">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-right">
                        <button onclick="editUser('${user.id}')" class="text-sky-600 hover:text-sky-700 font-medium mr-3">Edit</button>
                        <button onclick="deleteUser('${user.id}')" class="text-red-600 hover:text-red-700 font-medium">Hapus</button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function updatePagination(pagination) {
        $('#recordsInfo').text(`Showing ${pagination.from} to ${pagination.to} of ${pagination.total} records`);
        $('#prevBtn').prop('disabled', pagination.current_page === 1);
        $('#nextBtn').prop('disabled', pagination.current_page === pagination.last_page);
    }

    function openCreateModal() {
        if (!hasPermission('create-users')) {
            Toast.error('Anda tidak memiliki izin');
            return;
        }
        $('div[data-modal-id="modal"] [data-modal-title]').text('Tambah User Baru');
        $('#userForm')[0].reset();
        $('#userId').val('');
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
                $('#modalTitle').text('Edit User');
                $('#userId').val(user.id);
                $('input[name="name"]').val(user.name);
                $('input[name="username"]').val(user.username);
                $('input[name="email"]').val(user.email);
                $('select[name="role"]').val(user.role);
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

        if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            return;
        }

        $.ajax({
            url: `/api/v1/users/${userId}`,
            type: 'DELETE',
            success: function() {
                Toast.success('User berhasil dihapus');
                loadUsers(currentPage);
            },
            error: function() {
                Toast.error('Gagal menghapus user');
            }
        });
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
                closeModal();
                loadUsers(currentPage);
            },
            error: function(error) {
                const message = error.responseJSON?.message || 'Gagal menyimpan data';
                Toast.error(message);
            }
        });
    }
</script>
@endpush
@endsection
