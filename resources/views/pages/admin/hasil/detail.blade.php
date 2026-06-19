@extends('layouts.admin')

@section('title', 'Detail Jawaban - Admin')

@section('content')
    <div class="space-y-6" id="pageContent">

        {{-- Breadcrumb --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1.5">
                    <a href="/admin/hasil" class="hover:text-sky-600 transition">Hasil Ujian</a>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <a href="#" id="breadcrumbSesi" class="hover:text-sky-600 transition">—</a>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <span id="breadcrumbSiswa" class="text-slate-600 font-medium">—</span>
                </div>
                <h1 class="text-lg font-semibold text-slate-900" id="pageJudul">Memuat...</h1>
                <p class="text-sm text-slate-500 mt-0.5" id="pageSubtitle">—</p>
            </div>
            <a href="#" id="btnBack"
               class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 border border-slate-200 px-3 py-1.5 rounded-lg transition self-start">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Loading --}}
        <div id="loadingState" class="flex flex-col items-center gap-3 py-16 text-slate-400">
            <i class="fas fa-spinner fa-spin text-3xl"></i>
            <p class="text-sm">Memuat jawaban...</p>
        </div>

        {{-- Content (hidden until loaded) --}}
        <div id="mainContent" class="hidden space-y-6">

            {{-- Info Card --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nilai PG</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1" id="infoPg">—</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nilai Essai</p>
                    <p class="text-2xl font-bold text-violet-700 mt-1" id="infoEssai">—</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Nilai</p>
                    <p class="text-2xl font-bold text-sky-600 mt-1" id="infoTotal">—</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Durasi</p>
                    <p class="text-2xl font-bold text-slate-700 mt-1" id="infoDurasi">—</p>
                </div>
            </div>

            {{-- Pilihan Ganda --}}
            <div id="pgSection" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <h2 class="text-sm font-bold text-slate-800">Pilihan Ganda</h2>
                    <span class="text-xs text-slate-400 font-medium ml-1" id="pgCount"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase w-10">No</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Pertanyaan</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jawaban Siswa
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jawaban Benar
                            </th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Hasil</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Nilai</th>
                        </tr>
                        </thead>
                        <tbody id="pgBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>

            {{-- Essai --}}
            <div id="essaiSection" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                    <h2 class="text-sm font-bold text-slate-800">Essai</h2>
                    <span class="text-xs text-slate-400 font-medium ml-1" id="essaiCount"></span>
                </div>
                <div id="essaiBody" class="divide-y divide-slate-100"></div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const JAWABAN_ID = '{{ $jawabanId }}';
        let sesiId = null;

        $(document).ready(function() {
            loadDetail();
        });

        function loadDetail() {
            $.ajax({
                url: `/api/v1/hasil/jawaban/${JAWABAN_ID}`,
                success: function(res) {
                    const d = res.data;
                    sesiId = d.sesi_id;


                    // Breadcrumb & header
                    $('#breadcrumbSesi').text(d.mata_pelajaran || '-').attr('href', `/admin/hasil/${d.kelas_id}/${d.mapel_id}`);
                    $('#breadcrumbSiswa').text(d.nama_siswa || '-');
                    $('#pageJudul').text(d.nama_siswa || '-');
                    const subtitle = [d.nis, d.mata_pelajaran, d.judul_ujian].filter(Boolean).join(' · ');
                    $('#pageSubtitle').text(subtitle);
                    $('#btnBack').attr('href', `/admin/hasil/${d.mapel_id}`);
                    document.title = (d.nama_siswa || 'Detail') + ' - Admin';

                    // Stat cards
                    const nilaiEssai = d.nilai_essai;
                    const total = d.nilai_pg + nilaiEssai;
                    $('#infoPg').text(d.nilai_pg);
                    $('#infoEssai').text(nilaiEssai);
                    $('#infoTotal').text(total);

                    if (d.start && d.end) {
                        const durasi = Math.round((new Date(d.end) - new Date(d.start)) / 60000);
                        $('#infoDurasi').text(durasi + ' mnt');
                    }

                    // PG
                    if (d.pg && d.pg.length > 0) {
                        $('#pgCount').text(`(${d.pg.length} soal)`);
                        let pgHtml = '';
                        d.pg.forEach(function(item, idx) {
                            const jawabanBenar = item.pilihan ? item.pilihan.find(p => p.benar) : null;
                            const hasilBadge = item.hasil === true
                                ? '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full"><i class="fas fa-check text-[9px]"></i>Benar</span>'
                                : item.hasil === false
                                    ? '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full"><i class="fas fa-xmark text-[9px]"></i>Salah</span>'
                                    : '<span class="text-[11px] text-slate-300">—</span>';

                            const jawabanSiswa = item.pilihan_dipilih
                                ? `<span class="${item.hasil ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium'}">${item.pilihan_dipilih}</span>`
                                : '<span class="text-slate-300 italic text-xs">Tidak dijawab</span>';

                            pgHtml += `
                            <tr class="${idx % 2 === 0 ? '' : 'bg-slate-50/40'} hover:bg-sky-50/40 transition-colors">
                                <td class="px-5 py-3.5 text-xs text-slate-400 font-medium">${idx + 1}</td>
                                <td class="px-5 py-3.5 text-sm text-slate-700 max-w-xs">${item.pertanyaan || '-'}</td>
                                <td class="px-5 py-3.5 text-sm">${jawabanSiswa}</td>
                                <td class="px-5 py-3.5 text-sm text-emerald-700 font-medium">${jawabanBenar ? jawabanBenar.pilihan : '—'}</td>
                                <td class="px-5 py-3.5 text-center">${hasilBadge}</td>
                                <td class="px-5 py-3.5 text-center text-sm font-bold text-slate-700">${item.nilai}</td>
                            </tr>
                        `;
                        });
                        $('#pgBody').html(pgHtml);
                        $('#pgSection').removeClass('hidden');
                    } else {
                        $('#pgSection').addClass('hidden');
                    }

                    // Essai
                    if (d.essai && d.essai.length > 0) {
                        $('#essaiCount').text(`(${d.essai.length} soal)`);
                        let essaiHtml = '';
                        d.essai.forEach(function(item, idx) {
                            essaiHtml += `
                            <div class="p-5 space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Soal ${idx + 1} · Bobot ${item.nilai_soal ?? '—'}</p>
                                        <p class="text-sm font-medium text-slate-800">${item.pertanyaan || '-'}</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Jawaban Siswa</p>
                                    <p class="text-sm text-slate-700 whitespace-pre-wrap">${item.jawaban ? escHtml(item.jawaban) : '<span class="italic text-slate-300">Tidak dijawab</span>'}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <label class="text-xs font-semibold text-slate-600">Nilai:</label>
                                    <input type="number" min="0" max="${item.nilai_soal ?? 100}"
                                        value="${item.nilai}"
                                        class="w-24 px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-semibold text-center focus:outline-none focus:ring-2 focus:ring-violet-400"
                                        onchange="saveNilaiEssai('${item.id}', this.value, this)"
                                        id="essaiNilai_${item.id}" />
                                    <span class="text-xs text-slate-400">/ ${item.nilai_soal ?? '—'}</span>
                                    <span class="text-xs text-emerald-600 hidden" id="savedMsg_${item.id}">
                                        <i class="fas fa-check"></i> Tersimpan
                                    </span>
                                </div>
                            </div>
                        `;
                        });
                        $('#essaiBody').html(essaiHtml);
                        $('#essaiSection').removeClass('hidden');
                    } else {
                        $('#essaiSection').addClass('hidden');
                    }

                    $('#loadingState').addClass('hidden');
                    $('#mainContent').removeClass('hidden');
                },
                error: function() {
                    $('#loadingState').html('<p class="text-rose-500 text-sm text-center py-10">Gagal memuat data jawaban.</p>');
                }
            });
        }

        function saveNilaiEssai(jawabEssaiId, nilai, inputEl) {
            $.ajax({
                url: `/api/v1/hasil/jawaban/${jawabEssaiId}/nilai-essai`,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ nilai: parseInt(nilai) || 0 }),
                success: function() {
                    const msg = $(`#savedMsg_${jawabEssaiId}`);
                    msg.removeClass('hidden');
                    setTimeout(() => msg.addClass('hidden'), 2000);
                    // Reload nilai totals after essai save
                    updateTotals();
                },
                error: function() {
                    Toast.error('Gagal menyimpan nilai essai');
                }
            });
        }

        function updateTotals() {
            $.ajax({
                url: `/api/v1/hasil/jawaban/${JAWABAN_ID}`,
                success: function(res) {
                    const d = res.data;
                    $('#infoEssai').text(d.nilai_essai);
                    $('#infoTotal').text(d.nilai_pg + d.nilai_essai);
                }
            });
        }

        function escHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
    </script>
@endpush
