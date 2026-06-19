@extends('layouts.admin')

@section('title', 'Sesi Ujian - Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Sesi Ujian</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola sesi ujian — satu paket soal dapat memiliki beberapa sesi untuk kelas berbeda</p>
        </div>
        <x-button onclick="openCreateModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah</x-button>
    </div>

    <x-card padding="p-4">
        <input type="text" id="searchInput" placeholder="Cari judul, kode paket, atau mata pelajaran..."
            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
    </x-card>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Judul / Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Paket Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Target Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Waktu</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                            <span>Loading...</span>
                        </div>
                    </td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginationContainer">
            <x-pagination :currentPage="1" :totalPages="1" :totalRecords="0" :recordsPerPage="10" onPageChange="loadData" />
        </div>
    </div>
</div>

<x-modal id="modal" title="Tambah Sesi Ujian" size="md">
    <form id="form" class="space-y-4" onsubmit="saveData(event)">
        <input type="hidden" id="id" />

        <div>
            <x-select id="paket_soal_id" name="paket_soal_id" label="Paket Soal" placeholder="-- Pilih Paket Soal --" required />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul Sesi</label>
            <input type="text" id="judul" name="judul" placeholder="Ujian Tengah Semester Matematika Kelas 10A" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>

        <div>
            <x-select id="kelas_id" name="kelas_id" label="Target Kelas (Opsional)" placeholder="-- Pilih Paket Soal dulu --" :clearable="true" disabled />
            <p class="text-xs text-slate-400 mt-1">Hanya menampilkan kelas yang terikat dengan mapel paket soal. Kosongkan jika berlaku untuk semua kelas.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Waktu Mulai</label>
            <input type="datetime-local" id="start" name="start" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Waktu Selesai</label>
            <input type="datetime-local" id="end" name="end" readonly
                class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed" />
            <p class="text-[11px] text-slate-400 mt-1">Dihitung otomatis: Waktu Mulai + Durasi Paket Soal</p>
        </div>

        {{-- Info sesi number (muncul saat edit) --}}
        <div id="sesiInfoBox" class="hidden">
            <div class="text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 flex items-center gap-1.5">
                <i class="fas fa-info-circle text-slate-400"></i>
                Ini adalah sesi ke-<span id="sesiNum" class="font-semibold text-slate-700">-</span>
                dari paket ini &middot; Kode: <span id="kodepaketInfo" class="font-mono font-semibold text-slate-700">-</span>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <x-button type="submit" class="flex-1"><i class="fas fa-save"></i> Simpan</x-button>
            <x-button type="button" variant="secondary" onclick="Modal.close('modal')" class="flex-1">Batal</x-button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>
<script src="{{ asset('assets/js/helpers/pagination.js') }}"></script>
<script>
    let currentPage = 1, editingId = null;
    let paketMeta = {}; // paket_soal_id → { durasi_menit, mapel_id }

    $(document).ready(function() {
        loadPaketSelect();
        loadData(1);
        $('#searchInput').on('keyup', debounce(function() { loadData(1); }, 300));
        $('#paket_soal_id').on('change', onPaketChange);
        $('#start').on('change', autoCalcEnd);
    });

    function reinitS2(selector, placeholder) {
        const $el = $(selector);
        if ($el.data('select2')) $el.select2('destroy');
        Select2Helper.init(selector, { placeholder, dropdownParent: $('#modal') });
    }

    function onPaketChange() {
        const paketId = $('#paket_soal_id').val();
        autoCalcEnd();
        if (paketId && paketMeta[paketId]?.mapel_id) {
            loadKelasByMapel(paketMeta[paketId].mapel_id);
        } else {
            resetKelasSelect();
        }
    }

    function autoCalcEnd() {
        const paketId = $('#paket_soal_id').val();
        const durasi  = paketMeta[paketId]?.durasi_menit || 0;
        const start   = $('#start').val();
        if (!start || !durasi) return;
        const endDate = new Date(new Date(start).getTime() + durasi * 60000);
        const pad = n => String(n).padStart(2, '0');
        $('#end').val(`${endDate.getFullYear()}-${pad(endDate.getMonth()+1)}-${pad(endDate.getDate())}T${pad(endDate.getHours())}:${pad(endDate.getMinutes())}`);
    }

    function resetKelasSelect(placeholder) {
        const $sel = $('#kelas_id');
        $sel.html('<option value=""></option>').prop('disabled', true);
        reinitS2('#kelas_id', placeholder || '-- Pilih Paket Soal dulu --');
    }

    function loadKelasByMapel(mapelId, preSelect) {
        const $sel = $('#kelas_id');
        $sel.prop('disabled', true);
        reinitS2('#kelas_id', 'Memuat kelas...');
        $.ajax({
            url: '/api/v1/kelas',
            data: { mapel_id: mapelId, per_page: 200 },
            success: function(r) {
                $sel.html('<option value=""></option>');
                r.data.forEach(item => {
                    const jurusan = item.jurusan ? ' · ' + item.jurusan.kode_jurusan : '';
                    $sel.append(`<option value="${item.id}">${item.kode_kelas}${jurusan} — ${item.kelas}</option>`);
                });
                $sel.prop('disabled', false);
                reinitS2('#kelas_id', '-- Semua Kelas --');
                if (preSelect) $sel.val(preSelect).trigger('change');
            },
            error: function() {
                resetKelasSelect('Gagal memuat kelas');
            }
        });
    }

    function loadPaketSelect() {
        $.ajax({
            url: '/api/v1/paket-soal',
            data: { per_page: 200 },
            success: function(r) {
                const $sel = $('#paket_soal_id');
                $sel.html('<option value=""></option>');
                r.data.forEach(item => {
                    paketMeta[item.id] = { durasi_menit: item.durasi_menit, mapel_id: item.mapel_id };
                    $sel.append(`<option value="${item.id}">${item.kode_soal} — ${item.mata_pelajaran} (${item.dibuat_oleh || '-'})</option>`);
                });
                reinitS2('#paket_soal_id', '-- Pilih Paket Soal --');
            }
        });
        resetKelasSelect();
    }

    function loadData(page) {
        currentPage = page || currentPage;
        $.ajax({
            url: '/api/v1/sesi-ujian',
            data: { search: $('#searchInput').val(), page: currentPage, per_page: 10 },
            success: function(response) {
                const tbody = $('#tableBody');
                tbody.html('');
                if (response.data.length === 0) {
                    tbody.html('<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>');
                    return;
                }
                response.data.forEach(item => {
                    const startFmt = new Date(item.start).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });
                    const endFmt   = new Date(item.end).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });

                    const kelasTag = item.kelas
                        ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 text-xs font-medium">
                               ${item.kelas.kode_kelas}${item.kelas.jurusan ? ' · ' + item.kelas.jurusan.kode_jurusan : ''}
                           </span>`
                        : `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs">Semua Kelas</span>`;

                    tbody.append(`
                        <tr class="hover:bg-sky-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">${item.judul}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">${item.kode_paket} &middot; Sesi ke-${item.sesi}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-semibold text-slate-700">${item.paket_soal?.kode_soal || '-'}</div>
                                <div class="text-xs text-slate-500 mt-0.5">${item.paket_soal?.mata_pelajaran || ''}</div>
                            </td>
                            <td class="px-6 py-4">${kelasTag}</td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-600"><span class="text-slate-400">Mulai</span> ${startFmt}</div>
                                <div class="text-xs text-slate-600 mt-0.5"><span class="text-slate-400">Selesai</span> ${endFmt}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="editData('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteData('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                updatePagination(response.pagination, 'loadData');
            },
            error: function() { Toast.error('Gagal memuat data'); }
        });
    }

    function openCreateModal() {
        editingId = null;
        $('#form')[0].reset();
        $('#id').val('');
        $('#end').val('');
        $('#paket_soal_id').prop('disabled', false).val(null).trigger('change');
        reinitS2('#paket_soal_id', '-- Pilih Paket Soal --');
        resetKelasSelect();
        $('#sesiInfoBox').addClass('hidden');
        $('#modal [data-modal-title]').text('Tambah Sesi Ujian');
        Modal.open('modal');
    }

    function toLocalInput(utcStr) {
        if (!utcStr) return '';
        const d = new Date(utcStr);
        const pad = n => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }

    function editData(id) {
        $.ajax({
            url: `/api/v1/sesi-ujian/${id}`,
            success: function(r) {
                editingId = id;
                const d = r.data;
                $('#id').val(id);
                $('#judul').val(d.judul);

                // Paket: lock setelah dibuat
                $('#paket_soal_id').prop('disabled', true).val(d.paket_soal_id);
                reinitS2('#paket_soal_id', '');

                // Kelas: load berdasarkan mapel paket, lalu pre-select
                const mapelId = d.paket_soal?.mapel_id;
                if (mapelId) {
                    loadKelasByMapel(mapelId, d.kelas_id || null);
                } else {
                    resetKelasSelect();
                }

                // Waktu
                $('#start').val(toLocalInput(d.start));
                $('#end').val(toLocalInput(d.end));

                // Info sesi
                $('#sesiNum').text(d.sesi);
                $('#kodepaketInfo').text(d.kode_paket);
                $('#sesiInfoBox').removeClass('hidden');

                $('#modal [data-modal-title]').text('Edit Sesi Ujian');
                Modal.open('modal');
            }
        });
    }

    function saveData(e) {
        e.preventDefault();
        const isEdit = !!editingId;
        const data = {
            judul:    $('#judul').val(),
            kelas_id: $('#kelas_id').val() || null,
            start:    $('#start').val(),
            end:      $('#end').val(),
        };
        if (!isEdit) {
            data.paket_soal_id = $('#paket_soal_id').val();
        }
        $.ajax({
            url: isEdit ? `/api/v1/sesi-ujian/${editingId}` : '/api/v1/sesi-ujian',
            type: isEdit ? 'PUT' : 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(r) {
                Toast.success(r.message);
                Modal.close('modal');
                loadData(1);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) Object.values(errors).forEach(msg => Toast.error(msg[0]));
                else Toast.error(xhr.responseJSON?.message || 'Gagal menyimpan');
            }
        });
    }

    function deleteData(id) {
        SwalHelper.confirmDelete('Hapus Sesi Ujian?', 'Data yang dihapus tidak dapat dikembalikan.', function() {
            $.ajax({
                url: `/api/v1/sesi-ujian/${id}`,
                type: 'DELETE',
                success: function() { Toast.success('Dihapus'); loadData(1); },
                error: function() { Toast.error('Gagal menghapus'); }
            });
        });
    }

    function updatePagination(pagination, onPageChange) {
        $('#paginationContainer').html(PaginationHelper.build(pagination, onPageChange));
    }

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    }
</script>
@endpush
@endsection
