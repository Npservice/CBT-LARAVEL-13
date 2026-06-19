@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-base font-semibold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-400">Selamat datang di VEXTA Admin Console</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Users -->
            <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Total Users</p>
                        <p class="text-2xl font-semibold text-slate-900 mt-2" id="totalUsers">-</p>
                    </div>
                    <div class="w-12 h-12 bg-sky-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Admin Users -->
            <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Administrators</p>
                        <p class="text-2xl font-semibold text-slate-900 mt-2" id="adminCount">-</p>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Teachers -->
            <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Teachers</p>
                        <p class="text-2xl font-semibold text-slate-900 mt-2" id="guruCount">-</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 16.5S6.5 26.5 12 26.5s10-4.745 10-10.5S17.5 6.253 12 6.253z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Students</p>
                        <p class="text-2xl font-semibold text-slate-900 mt-2" id="siswaCount">-</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 16.5S6.5 26.5 12 26.5s10-4.745 10-10.5S17.5 6.253 12 6.253z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Recent Users</h2>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                    <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-6">Name</th>
                        <th class="py-3.5 px-6">Username</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6">Role</th>
                        <th class="py-3.5 px-6">Joined</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="recentUsersTable">
                    <tr class="hover:bg-slate-50/30 transition">
                        <td colspan="5" class="py-8 px-6 text-center text-slate-400 text-sm">Loading...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                loadDashboardStats();
            });

            function loadDashboardStats() {
                $.ajax({
                    url: '/api/v1/users',
                    type: 'GET',
                    data: { per_page: 100 },
                    success: function(response) {
                        const users = response.data;

                        // Calculate stats
                        const totalUsers = response.pagination.total;
                        const adminCount = users.filter(u => u.role === 'admin').length;
                        const guruCount = users.filter(u => u.role === 'guru').length;
                        const siswaCount = users.filter(u => u.role === 'siswa').length;

                        // Update stats
                        $('#totalUsers').text(totalUsers);
                        $('#adminCount').text(adminCount);
                        $('#guruCount').text(guruCount);
                        $('#siswaCount').text(siswaCount);

                        // Display recent users (limit to 5)
                        const recentUsers = users.slice(0, 5);
                        const tbody = $('#recentUsersTable');
                        tbody.html('');

                        recentUsers.forEach(user => {
                            const createdDate = new Date(user.created_at).toLocaleDateString('id-ID');
                            const row = $(`
                        <tr class="hover:bg-slate-50/30 transition">
                            <td class="py-3.5 px-6 text-slate-900 font-semibold">${user.name}</td>
                            <td class="py-3.5 px-6 text-slate-500 font-mono">${user.username}</td>
                            <td class="py-3.5 px-6 text-slate-500">${user.email}</td>
                            <td class="py-3.5 px-6">
                                <span class="bg-sky-50 text-sky-700 px-2 py-0.5 rounded font-medium text-[11px]">${user.role}</span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-500">${createdDate}</td>
                        </tr>
                    `);
                            tbody.append(row);
                        });
                    },
                    error: function() {
                        Toast.error('Failed to load dashboard stats');
                    }
                });
            }
        </script>
    @endpush
@endsection
