@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-semibold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-400" id="tanggalHari"></p>
        </div>
        <button onclick="refreshStats()" id="btnRefresh" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 px-3 py-1.5 rounded-lg transition bg-white">
            <i class="fas fa-rotate-right"></i> Refresh
        </button>
    </div>

    {{-- Aktivitas Hari Ini --}}
    <div>
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Aktivitas Hari Ini</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card padding="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Sesi Aktif</p>
                        <p class="text-3xl font-bold text-slate-900 mt-1.5" id="statSesiAktif">-</p>
                    </div>
                    <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-check text-sky-500"></i>
                    </div>
                </div>
            </x-card>

            <x-card padding="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Siswa Terlibat</p>
                        <p class="text-3xl font-bold text-slate-900 mt-1.5" id="statSiswaLogin">-</p>
                    </div>
                    <div class="w-11 h-11 bg-violet-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-violet-500"></i>
                    </div>
                </div>
            </x-card>

            <x-card padding="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Sedang Mengerjakan</p>
                        <p class="text-3xl font-bold text-amber-500 mt-1.5" id="statMengerjakan">-</p>
                    </div>
                    <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-pen-to-square text-amber-500"></i>
                    </div>
                </div>
            </x-card>

            <x-card padding="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Selesai Mengerjakan</p>
                        <p class="text-3xl font-bold text-emerald-500 mt-1.5" id="statSelesai">-</p>
                    </div>
                    <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-circle-check text-emerald-500"></i>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Master Data --}}
    <div>
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Master Data</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="/admin/guru" class="block group">
                <x-card padding="p-5" class="group-hover:border-rose-200 group-hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Total Guru</p>
                            <p class="text-3xl font-bold text-slate-900 mt-1.5" id="statGuru">-</p>
                        </div>
                        <div class="w-11 h-11 bg-rose-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-rose-100 transition">
                            <i class="fas fa-chalkboard-user text-rose-500"></i>
                        </div>
                    </div>
                </x-card>
            </a>

            <a href="/admin/siswa" class="block group">
                <x-card padding="p-5" class="group-hover:border-sky-200 group-hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Total Siswa</p>
                            <p class="text-3xl font-bold text-slate-900 mt-1.5" id="statSiswa">-</p>
                        </div>
                        <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-100 transition">
                            <i class="fas fa-graduation-cap text-sky-500"></i>
                        </div>
                    </div>
                </x-card>
            </a>

            <a href="/admin/mata-pelajaran" class="block group">
                <x-card padding="p-5" class="group-hover:border-indigo-200 group-hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Total Mapel</p>
                            <p class="text-3xl font-bold text-slate-900 mt-1.5" id="statMapel">-</p>
                        </div>
                        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-100 transition">
                            <i class="fas fa-book text-indigo-500"></i>
                        </div>
                    </div>
                </x-card>
            </a>

            <a href="/admin/guru-pengampu" class="block group">
                <x-card padding="p-5" class="group-hover:border-teal-200 group-hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Guru Pembuat Soal</p>
                            <p class="text-3xl font-bold text-slate-900 mt-1.5" id="statPembuatSoal">-</p>
                        </div>
                        <div class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-teal-100 transition">
                            <i class="fas fa-file-pen text-teal-500"></i>
                        </div>
                    </div>
                </x-card>
            </a>
        </div>
    </div>

    <p class="text-[11px] text-slate-400 text-right" id="cacheInfo"></p>
</div>

@push('scripts')
<script>
    document.getElementById('tanggalHari').textContent = new Date().toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });

    function loadStats() {
        $.ajax({
            url: '/api/v1/dashboard/stats',
            success: function(response) {
                const d = response.data;
                $('#statSesiAktif').text(d.sesi_aktif);
                $('#statSiswaLogin').text(d.siswa_login);
                $('#statMengerjakan').text(d.masih_mengerjakan);
                $('#statSelesai').text(d.sudah_selesai);
                $('#statGuru').text(d.total_guru);
                $('#statSiswa').text(d.total_siswa);
                $('#statMapel').text(d.total_mapel);
                $('#statPembuatSoal').text(d.total_pembuat_soal);
                $('#cacheInfo').text('Data diperbarui tiap 5 menit · terakhir pukul ' + d.cached_at);
            }
        });
    }

    function refreshStats() {
        const btn = $('#btnRefresh');
        btn.prop('disabled', true).find('i').addClass('fa-spin');
        $.ajax({
            url: '/api/v1/dashboard/refresh',
            type: 'POST',
            complete: function() {
                btn.prop('disabled', false).find('i').removeClass('fa-spin');
                loadStats();
            }
        });
    }

    $(document).ready(function() {
        loadStats();
    });
</script>
@endpush
@endsection
