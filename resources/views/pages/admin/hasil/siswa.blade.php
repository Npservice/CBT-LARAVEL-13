@extends('layouts.admin')

@section('title', 'Hasil Siswa - Admin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1.5">
                <a href="/admin/hasil" class="hover:text-sky-600 transition">Hasil Ujian</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <a id="breadcrumbKelas" href="/admin/hasil/{{ $kelasId }}" class="hover:text-sky-600 transition text-slate-600 font-medium">—</a>
                <i class="fas fa-chevron-right text-[9px]"></i>
                <span id="breadcrumbMapel" class="text-slate-600 font-medium">—</span>
            </div>
            <h1 class="text-lg font-semibold text-slate-900" id="pageTitle">Memuat...</h1>
        </div>
        <a id="btnKembali" href="/admin/hasil/{{ $kelasId }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 border border-slate-200 px-3 py-1.5 rounded-lg transition self-start">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-5 py-4 text-left text-xs font-bold text-slate-700 uppercase w-8">No</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-slate-700 uppercase">Nama Siswa</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-slate-700 uppercase">NIS</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-slate-700 uppercase">Sesi Ujian</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-slate-700 uppercase">Nilai PG</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-slate-700 uppercase">Nilai Essai</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-slate-700 uppercase">Total</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-slate-700 uppercase">Durasi</th>
                        <th class="px-5 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center">
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
    const MAPEL_ID = '{{ $mapelId }}';

    $(document).ready(function() { loadData(); });

    function loadData() {
        $.ajax({
            url: `/api/v1/hasil/${KELAS_ID}/${MAPEL_ID}/siswa`,
            success: function(res) {
                $('#breadcrumbKelas').text(res.kelas || '-');
                $('#breadcrumbMapel').text(res.mapel || '-');
                const title = [res.kelas, res.mapel].filter(Boolean).join(' — ');
                $('#pageTitle').text(title || '-');
                document.title = (res.mapel || 'Hasil') + ' - Admin';

                const tbody = $('#tableBody');
                if (!res.data || res.data.length === 0) {
                    tbody.html(`
                        <tr><td colspan="9" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <i class="fas fa-users-slash text-3xl"></i>
                                <p class="text-sm">Belum ada siswa yang menyelesaikan ujian ini</p>
                            </div>
                        </td></tr>
                    `);
                    return;
                }

                let html = '';
                res.data.forEach(function(item, idx) {
                    const bg     = idx % 2 === 0 ? '' : 'bg-slate-50/40';
                    const durasi = item.durasi_menit != null ? item.durasi_menit + ' mnt' : '—';

                    html += `
                        <tr class="${bg} hover:bg-sky-50/60 transition-colors">
                            <td class="px-5 py-4 text-xs text-slate-400 font-medium">${idx + 1}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">${item.nama_siswa || '-'}</td>
                            <td class="px-5 py-4 text-sm text-slate-500 font-mono">${item.nis || '-'}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">${item.judul_sesi || '-'}</td>
                            <td class="px-5 py-4 text-center text-sm font-semibold text-slate-700">${item.nilai_pg}</td>
                            <td class="px-5 py-4 text-center text-sm font-semibold text-violet-700">${item.nilai_essai}</td>
                            <td class="px-5 py-4 text-center text-sm font-bold text-sky-700">${item.total}</td>
                            <td class="px-5 py-4 text-center text-sm text-slate-500">${durasi}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="/admin/hasil/jawaban/${item.jawaban_id}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                    <i class="fas fa-eye text-[10px]"></i> Jawaban
                                </a>
                            </td>
                        </tr>
                    `;
                });
                tbody.html(html);
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="9" class="px-5 py-10 text-center text-rose-500 text-sm">Gagal memuat data</td></tr>');
            }
        });
    }
</script>
@endpush
