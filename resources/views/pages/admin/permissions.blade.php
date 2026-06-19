@extends('layouts.admin')

@section('title', 'Permissions - Admin')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Daftar Permission</h1>
            <p class="text-sm text-slate-500 mt-1">Semua permission yang tersedia di sistem</p>
        </div>
        <a href="{{ route('admin.roles') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 border border-slate-200 px-3 py-1.5 rounded-lg transition">
            <i class="fas fa-shield-halved"></i> Kelola Role
        </a>
    </div>

    <div id="permissionContent" class="space-y-4">
        <div class="flex items-center gap-2 text-slate-400 py-8 justify-center">
            <i class="fas fa-spinner fa-spin"></i> Memuat...
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const GROUP_LABELS = {
        'user-management': 'User Management',
        'role-management': 'Role & Permission',
        'siswa-management': 'Siswa',
        'guru-management': 'Guru',
        'guru-pengampu-management': 'Guru Pengampu',
        'master-data': 'Master Data',
        'paket-soal': 'Paket Soal',
        'soal': 'Soal',
        'sesi-ujian': 'Sesi Ujian',
        'hasil': 'Hasil Ujian',
    };

    $(document).ready(function () {
        $.ajax({
            url: '/api/v1/permissions',
            success: function (res) {
                let html = '';
                res.data.forEach(function (group) {
                    const label = GROUP_LABELS[group.group] || group.group;
                    html += `
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/60">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">${label}</h3>
                            </div>
                            <div class="divide-y divide-slate-100">
                    `;
                    group.permissions.forEach(function (perm) {
                        html += `
                            <div class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">${perm.display_permission}</p>
                                    <code class="text-[11px] text-slate-400">${perm.nama_permission}</code>
                                </div>
                                <span class="text-[11px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono">${perm.nama_permission}</span>
                            </div>
                        `;
                    });
                    html += `</div></div>`;
                });
                $('#permissionContent').html(html);
            },
            error: function () {
                $('#permissionContent').html('<p class="text-rose-500 text-sm text-center py-10">Gagal memuat data.</p>');
            }
        });
    });
</script>
@endpush
