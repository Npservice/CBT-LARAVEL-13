@extends('layouts.admin')

@section('title', 'Reports - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-base font-semibold text-slate-900 tracking-tight">Reports</h1>
        <p class="text-xs text-slate-400">Laporan sistem dan aktivitas pengguna</p>
    </div>

    <!-- Filter Card -->
    <x-card padding="p-6">
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
                <button onclick="generateReport()" class="w-full bg-sky-500 hover:bg-sky-600 text-white px-3.5 py-2 rounded-lg text-xs font-medium transition flex items-center justify-center gap-2">
                    <x-icon name="file-pdf" size="sm" /> Generate Report
                </button>
            </div>
        </div>
    </x-card>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- User Summary Card -->
        <x-card title="User Summary" padding="p-6">
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
        </x-card>

        <!-- System Health Card -->
        <x-card title="System Health" padding="p-6">
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
        </x-card>
    </div>

    <!-- Data Table Card -->
    <x-card title="Detailed Report">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="py-4 px-6 text-slate-700 font-bold uppercase tracking-wider text-[10px]">User</th>
                        <th class="py-4 px-6 text-slate-700 font-bold uppercase tracking-wider text-[10px]">Role</th>
                        <th class="py-4 px-6 text-slate-700 font-bold uppercase tracking-wider text-[10px]">Last Login</th>
                        <th class="py-4 px-6 text-slate-700 font-bold uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 [&_tr]:transition-colors" id="reportTable">
                    <tr class="hover:bg-sky-50/80">
                        <td colspan="4" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-inbox text-2xl text-slate-400"></i>
                                <span class="text-xs text-slate-500">No data available</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@push('scripts')
<script src="{{ asset('assets/js/pages/admin-reports.js') }}"></script>
@endpush
@endsection
