@extends('layouts.siswa')

@section('title', 'Dashboard - Siswa')

@section('content')
<div class="space-y-6">

    {{-- Greeting --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Selamat datang</p>
                <h1 class="text-lg font-bold text-slate-900 mt-0.5" id="siswaNama">—</h1>
                <p class="text-xs text-slate-400 mt-0.5" id="siswaInfo">Memuat data...</p>
            </div>
            <button onclick="siswaLogout()" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-rose-600 border border-slate-200 hover:border-rose-200 px-3 py-1.5 rounded-lg transition bg-white">
                <i class="fas fa-right-from-bracket"></i> Keluar
            </button>
        </div>
    </div>

    {{-- Ujian Tersedia --}}
    <div>
        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Ujian Aktif Saat Ini</p>
        <div id="ujianList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-full flex flex-col items-center gap-2 py-10 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
                <span class="text-sm">Memuat daftar ujian...</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script>
    $(document).ready(function() {
        loadDashboard();
    });

    function loadDashboard() {
        $.ajax({
            url: '/api/v1/ujian/tersedia',
            success: function(response) {
                const siswa = response.siswa;
                $('#siswaNama').text(siswa.nama);
                const infoArr = [];
                if (siswa.nis) infoArr.push('NIS: ' + siswa.nis);
                if (siswa.kelas) infoArr.push(siswa.kelas);
                if (siswa.jurusan) infoArr.push(siswa.jurusan);
                $('#siswaInfo').text(infoArr.join(' · ') || 'Siswa');

                if (response.no_profile) {
                    $('#ujianList').html(`
                        <div class="col-span-full flex flex-col items-center gap-3 py-12 text-slate-400">
                            <div class="w-14 h-14 bg-amber-50 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-xmark text-2xl text-amber-400"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-600">Profil siswa belum terdaftar</p>
                            <p class="text-xs text-center">Akun ini belum memiliki profil siswa.<br>Hubungi administrator untuk mendaftarkan profil Anda.</p>
                        </div>
                    `);
                    return;
                }

                renderUjian(response.data);
            },
            error: function() {
                $('#ujianList').html('<div class="col-span-full text-center py-10 text-rose-500 text-sm">Gagal memuat data. Coba refresh halaman.</div>');
            }
        });
    }

    function renderUjian(data) {
        clearInterval(window._countdownTimer);
        const container = $('#ujianList');
        if (!data || data.length === 0) {
            container.html(`
                <div class="col-span-full flex flex-col items-center gap-3 py-12 text-slate-400">
                    <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-xmark text-2xl text-slate-300"></i>
                    </div>
                    <p class="text-sm font-medium">Tidak ada ujian aktif saat ini</p>
                    <p class="text-xs">Ujian akan muncul di sini saat sudah waktunya.</p>
                </div>
            `);
            return;
        }

        const pad = n => String(n).padStart(2, '0');
        let html = '';
        data.forEach(sesi => {
            const startDate = new Date(sesi.start).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
            const endDate   = new Date(sesi.end).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
            const durasi    = sesi.durasi_menit ? sesi.durasi_menit + ' menit' : '-';
            const now       = new Date();
            const startTime = new Date(sesi.start);
            const belumMulai = sesi.status === 'belum' && now < startTime;

            let statusBadge = '';
            let actionBtn   = '';

            if (sesi.status === 'selesai') {
                statusBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                    <i class="fas fa-circle-check text-[9px]"></i> Selesai
                </span>`;
                actionBtn = `<button disabled class="w-full mt-4 py-2 rounded-lg text-xs font-semibold bg-slate-100 text-slate-400 cursor-not-allowed">Ujian Selesai</button>`;
            } else if (sesi.status === 'lanjut') {
                statusBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                    <i class="fas fa-clock text-[9px]"></i> Sedang Dikerjakan
                </span>`;
                actionBtn = `<button onclick="lanjutUjian('${sesi.jawaban_id}')" class="w-full mt-4 py-2 rounded-lg text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition">Lanjutkan Ujian →</button>`;
            } else if (belumMulai) {
                const startStr = `${pad(startTime.getHours())}:${pad(startTime.getMinutes())}`;
                statusBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-violet-700 bg-violet-50 border border-violet-200 px-2 py-0.5 rounded-full">
                    <i class="fas fa-hourglass-half text-[9px]"></i> Segera
                </span>`;
                actionBtn = `<button disabled
                    class="countdown-btn w-full mt-4 py-2 rounded-lg text-xs font-semibold bg-slate-100 text-slate-500 cursor-not-allowed flex items-center justify-center gap-2"
                    data-start="${sesi.start}" data-sesi-id="${sesi.id}">
                    <i class="fas fa-clock"></i> Dimulai pukul ${startStr}
                </button>`;
            } else {
                statusBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-sky-700 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded-full">
                    <i class="fas fa-circle text-[9px]"></i> Belum Dikerjakan
                </span>`;
                actionBtn = `<button onclick="mulaiUjian('${sesi.id}')" class="w-full mt-4 py-2 rounded-lg text-xs font-semibold bg-sky-500 hover:bg-sky-600 text-white transition">Mulai Ujian →</button>`;
            }

            html += `
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-col">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-bold text-slate-900 truncate">${sesi.judul || '-'}</p>
                            ${sesi.mata_pelajaran ? `<p class="text-xs text-sky-600 font-medium mt-0.5">${sesi.mata_pelajaran}</p>` : ''}
                        </div>
                        ${statusBadge}
                    </div>

                    <div class="space-y-2 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-hashtag w-4 text-slate-300"></i>
                            <span class="font-mono">${sesi.kode_paket || '-'}</span>
                        </div>
                        ${sesi.kode_kelas ? `
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users w-4 text-slate-300"></i>
                            <span><strong class="text-slate-700">${sesi.kode_kelas}</strong></span>
                        </div>` : ''}
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-day w-4 text-slate-300"></i>
                            <span>Mulai: <strong class="text-slate-700">${startDate}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-check w-4 text-slate-300"></i>
                            <span>Selesai: <strong class="text-slate-700">${endDate}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-stopwatch w-4 text-slate-300"></i>
                            <span>Durasi: <strong class="text-slate-700">${durasi}</strong></span>
                        </div>
                    </div>

                    ${actionBtn}
                </div>
            `;
        });

        container.html(html);

        if ($('.countdown-btn').length) startCountdowns();
    }

    function startCountdowns() {
        const pad = n => String(n).padStart(2, '0');
        window._countdownTimer = setInterval(function() {
            const now = new Date();
            $('.countdown-btn').each(function() {
                const btn      = $(this);
                const start    = new Date(btn.data('start'));
                const sesiId   = btn.data('sesi-id');
                const diff     = start - now;

                if (diff <= 0) {
                    // Aktifkan tombol
                    btn.prop('disabled', false)
                       .removeClass('countdown-btn bg-slate-100 text-slate-500 cursor-not-allowed')
                       .addClass('bg-sky-500 hover:bg-sky-600 text-white transition')
                       .attr('onclick', `mulaiUjian('${sesiId}')`)
                       .html('Mulai Ujian →');
                    if (!$('.countdown-btn').length) clearInterval(window._countdownTimer);
                    return;
                }

                const m = Math.floor(diff / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                btn.html(`<i class="fas fa-clock"></i> Dimulai dalam ${m}m ${pad(s)}d`);
            });
        }, 1000);
    }

    function mulaiUjian(sesiId) {
        SwalHelper.confirm(
            'Mulai Ujian?',
            'Waktu ujian langsung berjalan saat kamu konfirmasi. Jawaban tersimpan otomatis. Ujian yang sudah diselesaikan tidak dapat dikerjakan ulang. Pastikan koneksi internet kamu stabil.',
            function() {
                SwalHelper.loading('Mempersiapkan ujian...');
                $.ajax({
                    url: `/api/v1/ujian/${sesiId}/mulai`,
                    type: 'POST',
                    success: function(response) {
                        window.location.href = `/siswa/ujian/${response.data.jawaban_id}`;
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Gagal memulai ujian';
                        SwalHelper.loadingError('Gagal!', msg);
                    }
                });
            },
            'Saya Setuju, Mulai Ujian',
            'Batal'
        );
    }

    function lanjutUjian(jawabanId) {
        window.location.href = `/siswa/ujian/${jawabanId}`;
    }
</script>
@endpush
