@extends('layouts.siswa')

@section('title', 'Ujian - VEXTA CBT')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

{{-- Sticky exam info bar (tepat di bawah header layout) --}}
<div class="sticky top-14 z-40 -mx-6 -mt-6 bg-white border-b border-slate-100 px-6 py-3 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
            <div class="flex items-center gap-1.5">
                <span class="font-medium text-slate-700">Mata Pelajaran:</span>
                <span class="bg-sky-50 text-sky-700 px-2 py-0.5 rounded font-medium" id="subHeaderMapel">—</span>
            </div>
            <div>Progress: <strong class="text-slate-800" id="progressText">0/0 Soal</strong></div>
        </div>
        <div class="bg-slate-50 border border-slate-200 px-3 py-1 rounded-md flex items-center gap-2 self-start sm:self-auto">
            <i class="fas fa-stopwatch text-slate-400 text-xs"></i>
            <span class="text-xs font-mono font-bold tracking-wider text-slate-700" id="timer">--:--:--</span>
        </div>
    </div>
</div>

{{-- Loading state --}}
<div id="loadingState" class="flex flex-col items-center gap-3 py-20 text-slate-400">
    <i class="fas fa-spinner fa-spin text-3xl"></i>
    <p class="text-sm">Memuat soal ujian...</p>
</div>

{{-- Selesai state --}}
<div id="selesaiState" class="hidden flex-col items-center gap-4 py-20 text-center">
    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
        <i class="fas fa-circle-check text-3xl text-emerald-500"></i>
    </div>
    <h2 class="text-xl font-bold text-slate-900">Ujian Selesai!</h2>
    <p class="text-sm text-slate-500">Jawaban Anda telah berhasil dikumpulkan.</p>
    <a href="/siswa" class="mt-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold transition">
        Kembali ke Dashboard
    </a>
</div>

{{-- Exam content --}}
<div id="examContent" class="hidden grid-cols-1 lg:grid-cols-4 gap-6 items-start">

    {{-- Question area --}}
    <div class="lg:col-span-3 space-y-4">
        <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Soal <span id="qNumber">01</span></span>
                        <span class="text-[11px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded">Bobot: <span id="qBobot">—</span> Poin</span>
                        <span id="qTypeBadge" class="text-[11px] font-medium px-2 py-0.5 rounded"></span>
                    </div>
                    <button onclick="Ujian.toggleMark()" id="btnMark"
                        class="flex items-center space-x-1.5 px-2.5 py-1 rounded transition text-[11px] font-medium bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100">
                        <i class="fas fa-bookmark text-[10px]"></i>
                        <span id="markText">Tandai</span>
                    </button>
                </div>

                <p class="text-sm sm:text-base font-medium text-slate-900 mb-8 leading-relaxed" id="qText"></p>

                <div id="answerArea"></div>
            </div>

            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 sm:px-8 sm:py-5 flex items-center justify-between">
                <button onclick="Ujian.prev()"
                    class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm">
                    ← Sebelumnya
                </button>
                <button onclick="Ujian.next()"
                    class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                    Selanjutnya →
                </button>
            </div>
        </div>
    </div>

    {{-- Question navigator --}}
    <div class="lg:col-span-1">
        <div class="bg-white p-4 rounded-xl border border-slate-200/60 shadow-sm sticky top-40 max-h-[calc(100vh-200px)] overflow-y-auto custom-scrollbar space-y-4">

            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Navigasi Soal</h3>

            {{-- Pilihan Ganda --}}
            <div id="pgSection">
                <p class="text-[10px] font-bold text-sky-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400 inline-block"></span> Pilihan Ganda
                </p>
                <div class="grid grid-cols-5 gap-1.5" id="gridPg"></div>
            </div>

            {{-- Essai --}}
            <div id="essaiSection">
                <p class="text-[10px] font-bold text-violet-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400 inline-block"></span> Essai
                </p>
                <div class="grid grid-cols-5 gap-1.5" id="gridEssai"></div>
            </div>

            {{-- Legend --}}
            <div class="pt-3 border-t border-slate-100 space-y-2 text-[11px] text-slate-400 font-medium">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded bg-emerald-500 flex-shrink-0"></span>
                    <span>Terjawab (<span id="answeredCount">0</span>)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded bg-indigo-400 flex-shrink-0"></span>
                    <span>Ditandai (<span id="markedCount">0</span>)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded border border-slate-200 bg-white flex-shrink-0"></span>
                    <span>Belum Dijawab</span>
                </div>
            </div>

            {{-- Tombol Selesai --}}
            <button onclick="Ujian.showSelesaiConfirm()"
                class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5">
                <i class="fas fa-paper-plane"></i> Selesai Ujian
            </button>
        </div>
    </div>

</div>

{{-- Confirm overlay --}}
<div id="confirmOverlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6 space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-triangle-exclamation text-amber-500"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Selesaikan Ujian?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pastikan semua soal sudah dijawab. Jawaban tidak dapat diubah setelah dikumpulkan.</p>
            </div>
        </div>
        <div class="text-xs text-slate-500 bg-slate-50 rounded-lg p-3">
            <span id="confirmSummary"></span>
        </div>
        <div class="flex gap-3">
            <button onclick="Ujian.submitSelesai()"
                class="flex-1 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition">
                Ya, Kumpulkan
            </button>
            <button onclick="document.getElementById('confirmOverlay').classList.add('hidden')"
                class="flex-1 py-2.5 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                Periksa Lagi
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const JAWABAN_ID = '{{ $jawabanId }}';

    const Ujian = {
        questions: [],
        current: 0,
        marked: new Set(),
        timerInterval: null,

        init: function() {
            $.ajax({
                url: '/api/v1/ujian/' + JAWABAN_ID + '/soal',
                success: function(res) {
                    const d = res.data;

                    if (d.sudah_selesai) {
                        Ujian.showSelesai();
                        return;
                    }

                    const pgList = (d.pg || []).map(q => ({ ...q, type: 'pg' }));
                    const essaiList = (d.essai || []).map(q => ({ ...q, type: 'essai' }));
                    Ujian.questions = [...pgList, ...essaiList];

                    $('#subHeaderMapel').text(d.mata_pelajaran || '—');

                    Ujian.startTimer(d.sisa_detik);
                    Ujian.renderGrid();
                    Ujian.renderQuestion(0);

                    $('#loadingState').addClass('hidden');
                    $('#examContent').removeClass('hidden').addClass('grid');
                },
                error: function() {
                    $('#loadingState').html('<p class="text-rose-500 text-sm text-center">Gagal memuat soal. Coba refresh halaman.</p>');
                }
            });
        },

        startTimer: function(sisaDetik) {
            let secs = sisaDetik;
            const update = () => {
                if (secs <= 0) {
                    clearInterval(Ujian.timerInterval);
                    $('#timer').text('00:00:00').addClass('text-rose-500');
                    Ujian.submitSelesai();
                    return;
                }
                const h = Math.floor(secs / 3600);
                const m = Math.floor((secs % 3600) / 60);
                const s = secs % 60;
                $('#timer').text(
                    String(h).padStart(2, '0') + ':' +
                    String(m).padStart(2, '0') + ':' +
                    String(s).padStart(2, '0')
                );
                if (secs <= 300) $('#timer').addClass('text-rose-500');
                secs--;
            };
            update();
            Ujian.timerInterval = setInterval(update, 1000);
        },

        isAnswered: function(item) {
            if (item.type === 'pg') return !!item.pilihan_id;
            return !!(item.jawaban && item.jawaban.trim() !== '');
        },

        renderGrid: function() {
            const q = Ujian.questions;
            let pgHtml = '', essaiHtml = '';
            let pgNum = 0, essaiNum = 0;

            q.forEach((item, i) => {
                const isActive   = i === Ujian.current;
                const isMarked   = Ujian.marked.has(i);
                const isAnswered = Ujian.isAnswered(item);
                const isEssai    = item.type === 'essai';

                let cls = 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50';
                if (isActive)        cls = 'bg-sky-500 text-white border-sky-500';
                else if (isMarked)   cls = 'bg-indigo-50 border-indigo-400 text-indigo-700';
                else if (isAnswered) cls = 'bg-emerald-50 border-emerald-500 text-emerald-700';

                const btn = `<button onclick="Ujian.goTo(${i})" title="Soal ${i + 1}" class="aspect-square flex items-center justify-center text-[11px] font-semibold rounded-md border-2 transition ${cls}">${String(i + 1).padStart(2, '0')}</button>`;

                if (isEssai) { essaiNum++; essaiHtml += btn; }
                else         { pgNum++;    pgHtml    += btn; }
            });

            $('#gridPg').html(pgHtml || '<span class="text-[11px] text-slate-300">—</span>');
            $('#gridEssai').html(essaiHtml || '<span class="text-[11px] text-slate-300">—</span>');
            $('#pgSection').toggle(pgNum > 0);
            $('#essaiSection').toggle(essaiNum > 0);

            const answered = q.filter(item => Ujian.isAnswered(item)).length;
            $('#answeredCount').text(answered);
            $('#markedCount').text(Ujian.marked.size);
            $('#progressText').text(answered + '/' + q.length + ' Soal');
        },

        renderQuestion: function(idx) {
            Ujian.current = idx;
            const item = Ujian.questions[idx];
            if (!item) return;

            $('#qNumber').text(String(idx + 1).padStart(2, '0'));
            $('#qBobot').text(item.nilai_soal ?? '—');
            $('#qText').text(item.pertanyaan || '—');

            if (item.type === 'pg') {
                $('#qTypeBadge').text('Pilihan Ganda').attr('class', 'text-[11px] font-medium px-2 py-0.5 rounded bg-sky-50 text-sky-600');

                let html = '<div class="space-y-2.5">';
                (item.pilihan || []).forEach(p => {
                    const checked = item.pilihan_id === p.id ? 'checked' : '';
                    const active = item.pilihan_id === p.id ? 'border-sky-300 bg-sky-50' : '';
                    html += `
                        <label class="flex items-center p-3.5 rounded-lg border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer group ${active}">
                            <input type="radio" name="answer_pg" value="${p.id}" ${checked} class="w-4 h-4 text-sky-500 border-slate-300 flex-shrink-0">
                            <span class="ml-3 text-xs sm:text-sm font-medium text-slate-600 group-hover:text-slate-900">${p.pilihan}</span>
                        </label>
                    `;
                });
                html += '</div>';
                $('#answerArea').html(html);

                $('input[name="answer_pg"]').on('change', function() {
                    const pilihanId = $(this).val();
                    Ujian.questions[idx].pilihan_id = pilihanId;
                    Ujian.savePg(item.jawab_id, pilihanId);
                });

            } else {
                $('#qTypeBadge').text('Essai').attr('class', 'text-[11px] font-medium px-2 py-0.5 rounded bg-violet-50 text-violet-600');
                $('#answerArea').html(`
                    <div>
                        <textarea id="essaiInput" rows="6" placeholder="Tulis jawaban Anda di sini..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none">${item.jawaban || ''}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1.5">Jawaban disimpan otomatis saat Anda berhenti mengetik.</p>
                    </div>
                `);

                let essaiTimer = null;
                $('#essaiInput').on('input', function() {
                    clearTimeout(essaiTimer);
                    const val = $(this).val();
                    essaiTimer = setTimeout(() => {
                        Ujian.questions[idx].jawaban = val;
                        Ujian.saveEssai(item.jawab_id, val);
                    }, 800);
                });
            }

            if (Ujian.marked.has(idx)) {
                $('#markText').text('Tandai (✓)');
                $('#btnMark').addClass('bg-indigo-50 border-indigo-300 text-indigo-600').removeClass('bg-slate-50 border-slate-200 text-slate-600');
            } else {
                $('#markText').text('Tandai');
                $('#btnMark').removeClass('bg-indigo-50 border-indigo-300 text-indigo-600').addClass('bg-slate-50 border-slate-200 text-slate-600');
            }

            Ujian.renderGrid();
        },

        savePg: function(jawabId, pilihanId) {
            $.ajax({
                url: '/api/v1/ujian/' + JAWABAN_ID + '/jawab-pg',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ jawab_id: jawabId, pilihan_id: pilihanId }),
                success: function() { Ujian.renderGrid(); }
            });
        },

        saveEssai: function(jawabId, jawaban) {
            $.ajax({
                url: '/api/v1/ujian/' + JAWABAN_ID + '/jawab-essai',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ jawab_id: jawabId, jawaban: jawaban }),
                success: function() { Ujian.renderGrid(); }
            });
        },

        toggleMark: function() {
            const idx = Ujian.current;
            if (Ujian.marked.has(idx)) Ujian.marked.delete(idx);
            else Ujian.marked.add(idx);
            Ujian.renderQuestion(idx);
        },

        goTo: function(idx) {
            if (idx >= 0 && idx < Ujian.questions.length) Ujian.renderQuestion(idx);
        },

        prev: function() { if (Ujian.current > 0) Ujian.goTo(Ujian.current - 1); },
        next: function() { if (Ujian.current < Ujian.questions.length - 1) Ujian.goTo(Ujian.current + 1); },

        showSelesaiConfirm: function() {
            const total = Ujian.questions.length;
            const answered = Ujian.questions.filter(item => Ujian.isAnswered(item)).length;
            const belum = total - answered;
            let summary = `Total soal: <strong>${total}</strong> &nbsp;·&nbsp; Terjawab: <strong class="text-emerald-600">${answered}</strong>`;
            if (belum > 0) summary += ` &nbsp;·&nbsp; Belum dijawab: <strong class="text-rose-500">${belum}</strong>`;
            $('#confirmSummary').html(summary);
            $('#confirmOverlay').removeClass('hidden');
        },

        submitSelesai: function() {
            $('#confirmOverlay').addClass('hidden');
            clearInterval(Ujian.timerInterval);
            $.ajax({
                url: '/api/v1/ujian/' + JAWABAN_ID + '/selesai',
                type: 'POST',
                success: function() { Ujian.showSelesai(); },
                error: function(xhr) {
                    Toast.error(xhr.responseJSON?.message || 'Gagal menyelesaikan ujian');
                }
            });
        },

        showSelesai: function() {
            $('#loadingState').addClass('hidden');
            $('#examContent').addClass('hidden').removeClass('grid');
            $('#selesaiState').removeClass('hidden').addClass('flex');
        }
    };

    $(document).ready(function() {
        Ujian.init();
    });
</script>
@endpush
