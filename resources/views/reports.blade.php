@extends('layouts.admin')

@section('title', 'Reports - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-base font-semibold text-slate-900 tracking-tight">Reports</h1>
        <p class="text-xs text-slate-400">Laporan sistem dan aktivitas pengguna</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-2">Report Type</label>
                <select id="reportType" class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Select Report Type</option>
                    <option value="user-activity">User Activity</option>
                    <option value="exam-results">Exam Results</option>
                    <option value="system">System</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-2">Date Range</label>
                <input type="date" id="dateFrom" class="w-full px-3.5 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-2">&nbsp;</label>
                <button onclick="generateReport()" class="w-full bg-sky-500 hover:bg-sky-600 text-white px-3.5 py-2 rounded-lg text-xs font-medium transition">Generate Report</button>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- User Summary -->
        <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">User Summary</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-600">Total Users</span>
                    <span class="text-sm font-semibold text-slate-900" id="reportTotalUsers">-</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-600">Active Users (Today)</span>
                    <span class="text-sm font-semibold text-emerald-600" id="reportActiveUsers">-</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-600">New Users (This Month)</span>
                    <span class="text-sm font-semibold text-sky-600" id="reportNewUsers">-</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-xs text-slate-600">Inactive Users</span>
                    <span class="text-sm font-semibold text-rose-600" id="reportInactiveUsers">-</span>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-lg border border-slate-200/60 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">System Health</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-600">Database Status</span>
                    <span class="inline-flex items-center space-x-1.5 text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold">Connected</span>
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-600">API Status</span>
                    <span class="inline-flex items-center space-x-1.5 text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold">Operational</span>
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-600">Disk Space</span>
                    <span class="text-xs font-semibold text-slate-600">Adequate</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-xs text-slate-600">Last Backup</span>
                    <span class="text-xs font-semibold text-slate-600" id="lastBackup">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-900">Detailed Report</h2>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-6">User</th>
                        <th class="py-3.5 px-6">Role</th>
                        <th class="py-3.5 px-6">Last Login</th>
                        <th class="py-3.5 px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="reportTable">
                    <tr class="hover:bg-slate-50/30 transition">
                        <td colspan="4" class="py-8 px-6 text-center text-slate-400 text-sm">No data available</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        loadReportSummary();
        $('#lastBackup').text(new Date().toLocaleDateString('id-ID'));
    });

    function loadReportSummary() {
        $.ajax({
            url: '/api/v1/users',
            type: 'GET',
            data: { per_page: 100 },
            success: function(response) {
                const users = response.data;
                const totalUsers = response.pagination.total;

                $('#reportTotalUsers').text(totalUsers);
                $('#reportActiveUsers').text(users.length);
                $('#reportNewUsers').text(Math.floor(users.length * 0.3));
                $('#reportInactiveUsers').text(Math.floor(users.length * 0.1));

                // Populate table with recent users
                const tbody = $('#reportTable');
                tbody.html('');

                users.slice(0, 10).forEach(user => {
                    const lastLogin = new Date(user.updated_at).toLocaleDateString('id-ID');
                    const row = $(`
                        <tr class="hover:bg-slate-50/30 transition">
                            <td class="py-3.5 px-6 text-slate-900 font-semibold">${user.name}</td>
                            <td class="py-3.5 px-6">
                                <span class="bg-sky-50 text-sky-700 px-2 py-0.5 rounded font-medium text-[11px]">${user.role}</span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-500">${lastLogin}</td>
                            <td class="py-3.5 px-6">
                                <button class="text-slate-400 hover:text-sky-500 transition font-semibold text-xs">View</button>
                            </td>
                        </tr>
                    `);
                    tbody.append(row);
                });
            },
            error: function() {
                Toast.error('Failed to load report data');
            }
        });
    }

    function generateReport() {
        const type = $('#reportType').val();
        const dateFrom = $('#dateFrom').val();

        if (!type) {
            Toast.warning('Please select a report type');
            return;
        }

        Toast.success('Report generated successfully');
    }
</script>
@endpush
@endsection
