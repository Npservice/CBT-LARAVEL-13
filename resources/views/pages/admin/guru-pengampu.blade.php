@extends('layouts.admin')

@section('title', 'Guru Pengampu - Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Guru Pengampu</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola penugasan guru, mata pelajaran, dan kelas</p>
        </div>
        <x-button onclick="openCreateModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah</x-button>
    </div>

    <x-card padding="p-4">
        <input type="text" id="searchInput" placeholder="Cari guru atau mapel..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </x-card>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase w-48">Guru</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Mapel &amp; Kelas</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase w-40">Pembuat Soal</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200">
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                            <span>Loading...</span>
                        </div>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah / Tambah Mapel --}}
<x-modal id="modal" title="Tambah Penugasan" size="md">
    <form id="form" class="space-y-4" onsubmit="saveData(event)">
        <input type="hidden" id="id" />

        <x-select id="guru_id" name="guru_id" label="Guru" placeholder="-- Pilih Guru --" required />

        <x-select id="kelas_id" name="kelas_id" label="Kelas" placeholder="-- Pilih Kelas --" required />

        <div>
            <x-select id="mata_pelajaran_id" name="mata_pelajaran_id" label="Mata Pelajaran" placeholder="-- Pilih Kelas dulu --" required disabled />
            <p id="mapelHint" class="text-xs text-slate-500 mt-1 hidden">Hanya menampilkan mapel yang terdaftar untuk kelas ini</p>
        </div>

        <div class="flex items-center gap-3 pt-1">
            <input type="checkbox" id="pembuat_soal" name="pembuat_soal" value="1"
                class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
            <label for="pembuat_soal" class="text-sm text-slate-700">
                Jadikan Pembuat Soal
                <span class="text-xs text-slate-500">(1 guru per mapel)</span>
            </label>
        </div>

        <div class="flex gap-3 pt-4">
            <x-button type="submit" class="flex-1"><i class="fas fa-save"></i> Simpan</x-button>
            <x-button type="button" variant="secondary" onclick="Modal.close('modal')" class="flex-1">Batal</x-button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>
<script>
    let allData = [];
    let suppressKelasChange = false;

    function reinitS2(selector, placeholder) {
        const $el = $(selector);
        if ($el.data('select2')) $el.select2('destroy');
        Select2Helper.init(selector, { placeholder, dropdownParent: $('#modal') });
    }

    $(document).ready(function() {
        loadMasterData();
        loadData();
        $('#searchInput').on('keyup', debounce(renderTable, 300));
        $('#kelas_id').on('change', function() {
            if (!suppressKelasChange) loadMapelByKelas($(this).val());
        });
    });

    function loadMasterData() {
        $.ajax({ url: '/api/v1/guru', data: { per_page: 200 }, success: r => {
            const sel = $('#guru_id');
            sel.html('<option value=""></option>');
            r.data.forEach(g => sel.append(`<option value="${g.id}">${g.nama}</option>`));
            reinitS2('#guru_id', '-- Pilih Guru --');
        }});
        $.ajax({ url: '/api/v1/kelas', data: { per_page: 200 }, success: r => {
            const sel = $('#kelas_id');
            sel.html('<option value=""></option>');
            r.data.forEach(k => {
                const label = k.jurusan ? `${k.kode_kelas} · ${k.jurusan.kode_jurusan}` : k.kode_kelas;
                sel.append(`<option value="${k.id}">${label} — ${k.kelas}</option>`);
            });
            reinitS2('#kelas_id', '-- Pilih Kelas --');
        }});
        reinitS2('#mata_pelajaran_id', '-- Pilih Kelas dulu --');
    }

    function loadMapelByKelas(kelasId, preSelect = null) {
        const sel = $('#mata_pelajaran_id');
        if (!kelasId) {
            sel.html('<option value=""></option>').prop('disabled', true);
            reinitS2('#mata_pelajaran_id', '-- Pilih Kelas dulu --');
            $('#mapelHint').addClass('hidden');
            return;
        }
        sel.prop('disabled', true);
        reinitS2('#mata_pelajaran_id', 'Memuat mapel...');
        $.ajax({
            url: '/api/v1/mata-pelajaran',
            data: { kelas_id: kelasId, per_page: 200 },
            success: r => {
                sel.html('<option value=""></option>');
                r.data.forEach(m => sel.append(`<option value="${m.id}">${m.kode_mapel} — ${m.mapel}</option>`));
                sel.prop('disabled', false);
                reinitS2('#mata_pelajaran_id', '-- Pilih Mapel --');
                if (preSelect) sel.val(preSelect).trigger('change');
                $('#mapelHint').removeClass('hidden');
            },
            error: () => {
                sel.html('<option value=""></option>').prop('disabled', true);
                reinitS2('#mata_pelajaran_id', 'Gagal memuat mapel');
            }
        });
    }

    function loadData() {
        $.ajax({
            url: '/api/v1/guru-pengampu',
            data: { per_page: 500 },
            success: r => { allData = r.data; renderTable(); },
            error: () => Toast.error('Gagal memuat data')
        });
    }

    function renderTable() {
        const search = $('#searchInput').val().toLowerCase();
        const tbody  = $('#tableBody');
        tbody.html('');

        // Group by guru_id
        const grouped = {};
        allData.forEach(item => {
            const namaGuru = item.guru?.nama || '-';
            if (search && !namaGuru.toLowerCase().includes(search) &&
                !(item.mata_pelajaran?.mapel || '').toLowerCase().includes(search) &&
                !(item.mata_pelajaran?.kode_mapel || '').toLowerCase().includes(search)) return;

            const key = item.guru_id;
            if (!grouped[key]) grouped[key] = { guru: item.guru, items: [] };
            grouped[key].items.push(item);
        });

        const groups = Object.values(grouped);
        if (!groups.length) {
            tbody.html('<tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>');
            return;
        }

        groups.forEach(g => {
            // Group items by mata_pelajaran_id → kelas sebagai array
            const byMapel = {};
            g.items.forEach(item => {
                const mId = item.mata_pelajaran_id;
                if (!byMapel[mId]) byMapel[mId] = { mapel: item.mata_pelajaran, isPS: false, kelas: [] };
                if (item.pembuat_soal) byMapel[mId].isPS = true;
                byMapel[mId].kelas.push(item);
            });

            // Kolom Mapel & Kelas: satu baris per mapel, kelas sebagai tags di dalamnya
            const mapelRows = Object.entries(byMapel).map(([mId, info]) => {
                const kodeMapel = info.mapel?.kode_mapel || '?';
                const namaMapel = info.mapel?.mapel || '';
                const isPembuat = info.isPS;

                const kelasTags = info.kelas.map(item => {
                    const kodeKelas  = item.kelas?.kode_kelas || '?';
                    const jurusanKd  = item.kelas?.jurusan?.kode_jurusan || '';
                    const kelasLabel = jurusanKd ? `${kodeKelas} · ${jurusanKd}` : kodeKelas;
                    const namaKelas  = item.kelas?.kelas || '';
                    return `<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-white border border-slate-200 text-xs text-slate-600" title="${namaKelas}">
                        ${kelasLabel}
                        <button onclick="deleteData('${item.id}')"
                            class="text-slate-300 hover:text-red-500 transition-colors ml-0.5" title="Hapus assignment ini">
                            <i class="fas fa-times text-[9px]"></i>
                        </button>
                    </span>`;
                }).join('');

                return `<div class="flex items-start gap-2 py-0.5">
                    <span class="inline-flex items-center gap-1 text-xs font-mono font-semibold whitespace-nowrap pt-0.5
                        ${isPembuat ? 'text-emerald-700' : 'text-slate-700'}">
                        ${isPembuat ? '<i class="fas fa-star text-emerald-500 text-[9px]"></i>' : '<i class="far fa-star text-slate-300 text-[9px]"></i>'}
                        ${kodeMapel}
                    </span>
                    <span class="text-slate-300 text-xs pt-0.5">—</span>
                    <div class="flex flex-wrap gap-1">${kelasTags}</div>
                </div>`;
            }).join('');

            // PS column: toggle per unique mapel
            const psBadges = Object.entries(byMapel).map(([mId, info]) => {
                const kode = info.mapel?.kode_mapel || '?';
                if (info.isPS) {
                    return `<button onclick="togglePS('${g.guru?.id}', '${mId}', false)"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors" title="Klik untuk cabut">
                        <i class="fas fa-star text-[10px]"></i> ${kode}
                    </button>`;
                } else {
                    return `<button onclick="togglePS('${g.guru?.id}', '${mId}', true)"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 hover:bg-sky-100 hover:text-sky-700 transition-colors" title="Jadikan pembuat soal">
                        <i class="far fa-star text-[10px]"></i> ${kode}
                    </button>`;
                }
            }).join('');

            tbody.append(`
                <tr class="hover:bg-sky-50/40 transition-colors">
                    <td class="px-6 py-4 text-sm font-semibold text-slate-900 align-top whitespace-nowrap">
                        ${g.guru?.nama || '-'}
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="space-y-0.5">${mapelRows}</div>
                    </td>
                    <td class="px-6 py-4 text-center align-top">
                        <div class="flex flex-col items-center gap-1">${psBadges}</div>
                    </td>
                    <td class="px-6 py-4 text-right align-top whitespace-nowrap">
                        <button onclick="openAddModal('${g.guru?.id}')"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 text-xs font-semibold transition-colors">
                            <i class="fas fa-plus text-[10px]"></i> Tambah
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function openCreateModal() {
        $('#id').val('');
        $('#guru_id').val(null).trigger('change');
        $('#guru_id').prop('disabled', false);
        reinitS2('#guru_id', '-- Pilih Guru --');
        $('#kelas_id').val(null).trigger('change');
        $('#pembuat_soal').prop('checked', false);
        $('#modal [data-modal-title]').text('Tambah Penugasan');
        Modal.open('modal');
    }

    function openAddModal(guruId) {
        $('#id').val('');
        // Kunci guru, pre-pilih
        $('#guru_id').val(guruId).trigger('change');
        $('#guru_id').prop('disabled', true);
        // Reset kelas + mapel
        suppressKelasChange = true;
        $('#kelas_id').val(null).trigger('change');
        suppressKelasChange = false;
        loadMapelByKelas(null);
        $('#pembuat_soal').prop('checked', false);
        $('#modal [data-modal-title]').text('Tambah Mapel / Kelas');
        Modal.open('modal');
    }

    function togglePS(guruId, mapelId, status) {
        $.ajax({
            url: '/api/v1/guru-pengampu/set-pembuat-soal',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ guru_id: guruId, mata_pelajaran_id: mapelId, pembuat_soal: status ? 1 : 0 }),
            success: r => { Toast.success(r.message); loadData(); },
            error: xhr => Toast.error(xhr.responseJSON?.message || 'Gagal memperbarui')
        });
    }

    function saveData(e) {
        e.preventDefault();
        const payload = {
            guru_id:           $('#guru_id').val(),
            mata_pelajaran_id: $('#mata_pelajaran_id').val(),
            kelas_id:          $('#kelas_id').val(),
            pembuat_soal:      $('#pembuat_soal').is(':checked') ? 1 : 0,
        };
        $.ajax({
            url: '/api/v1/guru-pengampu',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: r => { Toast.success(r.message); Modal.close('modal'); loadData(); },
            error: xhr => {
                const errors = xhr.responseJSON?.errors;
                if (errors) Object.values(errors).forEach(msg => Toast.error(msg[0]));
                else Toast.error(xhr.responseJSON?.message || 'Gagal menyimpan');
            }
        });
    }

    function deleteData(id) {
        SwalHelper.confirmDelete('Hapus Penugasan?', 'Data yang dihapus tidak dapat dikembalikan.', function() {
            $.ajax({
                url: `/api/v1/guru-pengampu/${id}`,
                type: 'DELETE',
                success: () => { Toast.success('Dihapus'); loadData(); },
                error: () => Toast.error('Gagal menghapus')
            });
        });
    }

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }
</script>
@endpush
@endsection
