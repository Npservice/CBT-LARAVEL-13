@extends('layouts.admin')

@section('title', 'Hasil Ujian - Admin')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-lg font-semibold text-slate-900">Hasil Ujian</h1>
        <p class="text-sm text-slate-500 mt-1">Rekap penyelesaian ujian per kelas</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-5 py-4 text-left text-xs font-bold text-slate-700 uppercase">Kelas</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-slate-700 uppercase">Penyelesaian</th>
                        <th class="px-5 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="3" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i class="fas fa-spinner fa-spin text-2xl"></i>
                                <span class="text-sm">Memuat data...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() { loadData(); });

    function loadData() {
        $.ajax({
            url: '/api/v1/hasil',
            success: function(res) {
                const tbody = $('#tableBody');
                if (!res.data || res.data.length === 0) {
                    tbody.html(`
                        <tr><td colspan="3" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i class="fas fa-inbox text-3xl"></i>
                                <p class="text-sm">Belum ada hasil ujian</p>
                            </div>
                        </td></tr>
                    `);
                    return;
                }

                // Group by jurusan
                const grouped = {};
                res.data.forEach(item => {
                    const key = item.jurusan || 'Tanpa Jurusan';
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(item);
                });

                let html = '';
                Object.entries(grouped).forEach(([jurusan, kelasList]) => {
                    html += `
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <td colspan="3" class="px-5 py-2.5">
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <i class="fas fa-layer-group text-slate-400 mr-1.5"></i>${jurusan}
                                </span>
                            </td>
                        </tr>
                    `;

                    kelasList.forEach(item => {
                        const pct  = item.total > 0 ? Math.round((item.selesai / item.total) * 100) : 0;
                        const done = pct >= 100;
                        const barColor  = done ? 'bg-emerald-500' : (pct >= 50 ? 'bg-amber-400' : 'bg-rose-400');
                        const textColor = done ? 'text-emerald-700' : (pct >= 50 ? 'text-amber-700' : 'text-rose-600');

                        html += `
                            <tr class="hover:bg-sky-50/60 transition-colors">
                                <td class="px-5 py-4 pl-9">
                                    <span class="font-semibold text-slate-900">${item.kelas}</span>
                                    <span class="ml-2 text-xs font-mono text-slate-400">${item.kode_kelas}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="text-sm font-bold ${textColor}">${pct}%</span>
                                        <div class="w-28 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full ${barColor} rounded-full transition-all" style="width:${pct}%"></div>
                                        </div>
                                        <span class="text-[11px] text-slate-400">${item.selesai} / ${item.total} siswa</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="/admin/hasil/${item.kelas_id}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold transition">
                                        <i class="fas fa-book-open text-[10px]"></i> Lihat Mapel
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                });

                tbody.html(html);
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="3" class="px-5 py-10 text-center text-rose-500 text-sm">Gagal memuat data</td></tr>');
            }
        });
    }
</script>
@endpush
