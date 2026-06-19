@extends('layouts.admin')

@section('title', 'Hasil per Mapel - Admin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1.5">
                <a href="/admin/hasil" class="hover:text-sky-600 transition">Hasil Ujian</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span id="breadcrumbKelas" class="text-slate-600 font-medium">—</span>
            </div>
            <h1 class="text-lg font-semibold text-slate-900" id="pageTitle">Memuat...</h1>
        </div>
        <a href="/admin/hasil" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 border border-slate-200 px-3 py-1.5 rounded-lg transition self-start">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-5 py-4 text-left text-xs font-bold text-slate-700 uppercase">Mata Pelajaran</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-slate-700 uppercase">Penyelesaian Siswa</th>
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
    const KELAS_ID = '{{ $kelasId }}';

    $(document).ready(function() { loadData(); });

    function loadData() {
        $.ajax({
            url: `/api/v1/hasil/${KELAS_ID}/mapel`,
            success: function(res) {
                const label = [res.kelas, res.jurusan].filter(Boolean).join(' · ');
                $('#breadcrumbKelas').text(res.kelas || '-');
                $('#pageTitle').text(label || '-');
                document.title = (res.kelas || 'Hasil') + ' - Admin';

                const tbody = $('#tableBody');
                if (!res.data || res.data.length === 0) {
                    tbody.html(`
                        <tr><td colspan="3" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i class="fas fa-book-open text-3xl"></i>
                                <p class="text-sm">Belum ada mata pelajaran dengan hasil ujian</p>
                            </div>
                        </td></tr>
                    `);
                    return;
                }

                let html = '';
                res.data.forEach(function(item, idx) {
                    const bg   = idx % 2 === 0 ? '' : 'bg-slate-50/40';
                    const pct  = item.total > 0 ? Math.round((item.selesai / item.total) * 100) : 0;
                    const done = pct >= 100;
                    const barColor  = done ? 'bg-emerald-500' : (pct >= 50 ? 'bg-amber-400' : 'bg-rose-400');
                    const textColor = done ? 'text-emerald-700' : (pct >= 50 ? 'text-amber-700' : 'text-rose-600');
                    const badge     = done
                        ? `<i class="fas fa-circle-check text-emerald-500 ml-1.5"></i>`
                        : '';

                    html += `
                        <tr class="${bg} hover:bg-sky-50/60 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">${item.mapel}${badge}</div>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">${item.kode_mapel}</div>
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
                                <a href="/admin/hasil/${KELAS_ID}/${item.mapel_id}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-xs font-semibold transition">
                                    <i class="fas fa-users text-[10px]"></i> Lihat Siswa
                                </a>
                            </td>
                        </tr>
                    `;
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
