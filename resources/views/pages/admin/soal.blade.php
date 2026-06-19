@extends('layouts.admin')

@section('title', 'Kelola Soal - Admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/admin/paket-soal" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Kelola Soal</h1>
                <p class="text-sm text-slate-500 mt-0.5" id="paketInfo">Loading...</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-slate-200">
        <div class="flex gap-0">
            <button onclick="switchTab('pg')" id="tabPG"
                class="px-5 py-3 border-b-2 border-sky-500 text-sky-600 font-medium text-sm transition-colors">
                <i class="fas fa-list-ol mr-1.5"></i> Soal Pilihan Ganda
            </button>
            <button onclick="switchTab('essai')" id="tabEssai"
                class="px-5 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium text-sm transition-colors">
                <i class="fas fa-pen-alt mr-1.5"></i> Soal Essai
            </button>
        </div>
    </div>

    {{-- Tab: Soal Pilihan Ganda --}}
    <div id="tabPGContent">
        <div class="flex items-center justify-between mb-4">
            <input type="text" id="searchPG" placeholder="Cari soal PG..."
                class="w-72 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            <x-button onclick="openCreatePGModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah Soal PG</x-button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase w-8">#</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Pertanyaan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Nilai</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Opsi</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tablePG" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                                <span>Loading...</span>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
            <div id="paginationPG">
                <x-pagination :currentPage="1" :totalPages="1" :totalRecords="0" :recordsPerPage="10" onPageChange="loadPGData" />
            </div>
        </div>
    </div>

    {{-- Tab: Soal Essai --}}
    <div id="tabEssaiContent" class="hidden">
        <div class="flex items-center justify-between mb-4">
            <input type="text" id="searchEssai" placeholder="Cari soal essai..."
                class="w-72 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
            <x-button onclick="openCreateEssaiModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah Soal Essai</x-button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase w-8">#</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Pertanyaan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Nilai</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableEssai" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                                <span>Loading...</span>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
            <div id="paginationEssai">
                <x-pagination :currentPage="1" :totalPages="1" :totalRecords="0" :recordsPerPage="10" onPageChange="loadEssaiData" />
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Soal Pilihan Ganda --}}
<x-modal id="modalPG" title="Tambah Soal Pilihan Ganda" size="lg">
    <form id="formPG" class="space-y-4" onsubmit="savePGData(event)">
        <input type="hidden" id="pgId" />
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
            <textarea id="pertanyaanPG" name="pertanyaan" rows="3" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nilai <span class="text-red-500">*</span></label>
            <input type="number" id="nilaiPG" name="nilai" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>

        <div class="border-t pt-4">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban <span class="text-red-500">*</span></label>
                <button type="button" onclick="addPilihanRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 text-sky-600 hover:bg-sky-100 rounded-lg text-xs font-medium transition-colors">
                    <i class="fas fa-plus"></i> Tambah Opsi
                </button>
            </div>
            <div id="pilihanContainer" class="space-y-2"></div>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <x-button type="submit" class="flex-1"><i class="fas fa-save"></i> Simpan</x-button>
            <x-button type="button" variant="secondary" onclick="Modal.close('modalPG')" class="flex-1">Batal</x-button>
        </div>
    </form>
</x-modal>

{{-- MODAL: Soal Essai --}}
<x-modal id="modalEssai" title="Tambah Soal Essai" size="md">
    <form id="formEssai" class="space-y-4" onsubmit="saveEssaiData(event)">
        <input type="hidden" id="essaiId" />
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
            <textarea id="pertanyaanEssai" name="pertanyaan" rows="4" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nilai <span class="text-red-500">*</span></label>
            <input type="number" id="nilaiEssai" name="nilai" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </div>
        <div class="flex gap-3 pt-4 border-t">
            <x-button type="submit" class="flex-1"><i class="fas fa-save"></i> Simpan</x-button>
            <x-button type="button" variant="secondary" onclick="Modal.close('modalEssai')" class="flex-1">Batal</x-button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>
<script src="{{ asset('assets/js/helpers/pagination.js') }}"></script>
<script>
    const paketId = '{{ $paketId }}';
    let currentTab = 'pg';
    let pgPage = 1, essaiPage = 1;
    let editingPGId = null, editingEssaiId = null;

    $(document).ready(function() {
        loadPaketInfo();
        loadPGData(1);
        $('#searchPG').on('keyup', debounce(() => loadPGData(1), 300));
        $('#searchEssai').on('keyup', debounce(() => loadEssaiData(1), 300));
    });

    function loadPaketInfo() {
        $.ajax({
            url: `/api/v1/paket-soal/${paketId}`,
            success: function(r) {
                $('#paketInfo').text(`${r.data.kode_soal} — ${r.data.mata_pelajaran} | Kelas ${r.data.kode_kelas}`);
            }
        });
    }

    function switchTab(tab) {
        currentTab = tab;
        if (tab === 'pg') {
            $('#tabPG').addClass('border-sky-500 text-sky-600').removeClass('border-transparent text-slate-500 hover:text-slate-700');
            $('#tabEssai').addClass('border-transparent text-slate-500 hover:text-slate-700').removeClass('border-sky-500 text-sky-600');
            $('#tabPGContent').removeClass('hidden');
            $('#tabEssaiContent').addClass('hidden');
            loadPGData(pgPage);
        } else {
            $('#tabEssai').addClass('border-sky-500 text-sky-600').removeClass('border-transparent text-slate-500 hover:text-slate-700');
            $('#tabPG').addClass('border-transparent text-slate-500 hover:text-slate-700').removeClass('border-sky-500 text-sky-600');
            $('#tabEssaiContent').removeClass('hidden');
            $('#tabPGContent').addClass('hidden');
            loadEssaiData(essaiPage);
        }
    }

    // ---- SOAL PILIHAN GANDA ----

    function loadPGData(page) {
        pgPage = page || pgPage;
        $.ajax({
            url: '/api/v1/soal-pilihan-ganda',
            data: { paket_soal_id: paketId, search: $('#searchPG').val(), page: pgPage, per_page: 10 },
            success: function(r) {
                const tbody = $('#tablePG');
                tbody.html('');
                if (r.data.length === 0) {
                    tbody.html('<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada soal pilihan ganda</td></tr>');
                    return;
                }
                r.data.forEach((item, idx) => {
                    const no = (pgPage - 1) * 10 + idx + 1;
                    const opsiCount = item.pilihan?.length || 0;
                    tbody.append(`
                        <tr class="hover:bg-sky-50/80 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-500">${no}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">${item.pertanyaan.length > 80 ? item.pertanyaan.substring(0, 80) + '...' : item.pertanyaan}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">${item.nilai}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">${opsiCount} opsi</td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="editPG('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="deletePG('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                updatePagination(r.pagination, 'loadPGData', 'paginationPG');
            },
            error: function() { Toast.error('Gagal memuat soal PG'); }
        });
    }

    function openCreatePGModal() {
        editingPGId = null;
        $('#formPG')[0].reset();
        resetPilihanContainer();
        $('#modalPG [data-modal-title]').text('Tambah Soal Pilihan Ganda');
        Modal.open('modalPG');
    }

    function editPG(id) {
        $.ajax({
            url: `/api/v1/soal-pilihan-ganda/${id}`,
            success: function(r) {
                editingPGId = id;
                $('#pgId').val(id);
                $('#pertanyaanPG').val(r.data.pertanyaan);
                $('#nilaiPG').val(r.data.nilai);
                $('#pilihanContainer').html('');
                if (r.data.pilihan && r.data.pilihan.length > 0) {
                    r.data.pilihan.forEach((p, idx) => appendPilihanRow(idx, p.pilihan, p.benar));
                } else {
                    resetPilihanContainer();
                }
                $('#modalPG [data-modal-title]').text('Edit Soal Pilihan Ganda');
                Modal.open('modalPG');
            }
        });
    }

    function savePGData(e) {
        e.preventDefault();
        const pertanyaan = $('#pertanyaanPG').val();
        const nilai = $('#nilaiPG').val();
        const jawabanBenarIdx = $('input[name="jawaban_benar"]:checked').val();
        const pilihan = [];

        $('#pilihanContainer .pilihan-row').each(function(idx) {
            const text = $(this).find('input[type="text"]').val().trim();
            if (text) pilihan.push({ pilihan: text, benar: String(idx) === String(jawabanBenarIdx) });
        });

        if (pilihan.length < 2) { Toast.error('Minimal 2 opsi'); return; }

        $.ajax({
            url: editingPGId ? `/api/v1/soal-pilihan-ganda/${editingPGId}` : '/api/v1/soal-pilihan-ganda',
            type: editingPGId ? 'PUT' : 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ paket_soal_id: paketId, pertanyaan, nilai }),
            success: function(r) {
                if (!editingPGId) {
                    const soalId = r.data.id;
                    pilihan.forEach(p => {
                        $.ajax({
                            url: '/api/v1/pilihan',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({ soal_pilihan_ganda_id: soalId, pilihan: p.pilihan, benar: p.benar })
                        });
                    });
                }
                Toast.success(r.message);
                Modal.close('modalPG');
                loadPGData(pgPage);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) Object.values(errors).forEach(m => Toast.error(m[0]));
            }
        });
    }

    function deletePG(id) {
        SwalHelper.confirmDelete('Hapus Soal?', 'Soal beserta semua pilihan jawabannya akan dihapus.', function() {
            $.ajax({
                url: `/api/v1/soal-pilihan-ganda/${id}`,
                type: 'DELETE',
                success: function() { Toast.success('Soal dihapus'); loadPGData(pgPage); },
                error: function() { Toast.error('Gagal menghapus'); }
            });
        });
    }

    function addPilihanRow() {
        const idx = $('#pilihanContainer .pilihan-row').length;
        appendPilihanRow(idx, '', false);
    }

    function appendPilihanRow(idx, value, isBenar) {
        $('#pilihanContainer').append(`
            <div class="pilihan-row flex gap-2 items-center">
                <input type="text" class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" placeholder="Isi pilihan jawaban" value="${value}" />
                <label class="flex-shrink-0 flex items-center gap-1.5 text-sm text-slate-600 cursor-pointer">
                    <input type="radio" name="jawaban_benar" value="${idx}" ${isBenar ? 'checked' : ''} class="w-4 h-4 accent-emerald-500" />
                    <span class="text-xs">Benar</span>
                </label>
                <button type="button" onclick="removePilihanRow(this)" class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded bg-red-50 text-red-500 hover:bg-red-100 transition-colors text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);
    }

    function removePilihanRow(btn) {
        if ($('#pilihanContainer .pilihan-row').length <= 2) {
            Toast.error('Minimal 2 opsi');
            return;
        }
        $(btn).closest('.pilihan-row').remove();
        // Re-index radio values after removal
        $('#pilihanContainer .pilihan-row').each(function(idx) {
            $(this).find('input[type="radio"]').val(idx);
        });
    }

    function resetPilihanContainer() {
        $('#pilihanContainer').html('');
        appendPilihanRow(0, '', false);
        appendPilihanRow(1, '', false);
    }

    // ---- SOAL ESSAI ----

    function loadEssaiData(page) {
        essaiPage = page || essaiPage;
        $.ajax({
            url: '/api/v1/soal-essai',
            data: { paket_soal_id: paketId, search: $('#searchEssai').val(), page: essaiPage, per_page: 10 },
            success: function(r) {
                const tbody = $('#tableEssai');
                tbody.html('');
                if (r.data.length === 0) {
                    tbody.html('<tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada soal essai</td></tr>');
                    return;
                }
                r.data.forEach((item, idx) => {
                    const no = (essaiPage - 1) * 10 + idx + 1;
                    tbody.append(`
                        <tr class="hover:bg-sky-50/80 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-500">${no}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">${item.pertanyaan.length > 100 ? item.pertanyaan.substring(0, 100) + '...' : item.pertanyaan}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">${item.nilai}</td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="editEssai('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteEssai('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                updatePagination(r.pagination, 'loadEssaiData', 'paginationEssai');
            },
            error: function() { Toast.error('Gagal memuat soal essai'); }
        });
    }

    function openCreateEssaiModal() {
        editingEssaiId = null;
        $('#formEssai')[0].reset();
        $('#modalEssai [data-modal-title]').text('Tambah Soal Essai');
        Modal.open('modalEssai');
    }

    function editEssai(id) {
        $.ajax({
            url: `/api/v1/soal-essai/${id}`,
            success: function(r) {
                editingEssaiId = id;
                $('#essaiId').val(id);
                $('#pertanyaanEssai').val(r.data.pertanyaan);
                $('#nilaiEssai').val(r.data.nilai);
                $('#modalEssai [data-modal-title]').text('Edit Soal Essai');
                Modal.open('modalEssai');
            }
        });
    }

    function saveEssaiData(e) {
        e.preventDefault();
        const formData = new FormData($('#formEssai')[0]);
        formData.append('paket_soal_id', paketId);
        $.ajax({
            url: editingEssaiId ? `/api/v1/soal-essai/${editingEssaiId}` : '/api/v1/soal-essai',
            type: editingEssaiId ? 'PUT' : 'POST',
            processData: false, contentType: false,
            data: formData,
            success: function(r) {
                Toast.success(r.message);
                Modal.close('modalEssai');
                loadEssaiData(essaiPage);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) Object.values(errors).forEach(m => Toast.error(m[0]));
            }
        });
    }

    function deleteEssai(id) {
        SwalHelper.confirmDelete('Hapus Soal?', 'Soal yang dihapus tidak dapat dikembalikan.', function() {
            $.ajax({
                url: `/api/v1/soal-essai/${id}`,
                type: 'DELETE',
                success: function() { Toast.success('Soal dihapus'); loadEssaiData(essaiPage); },
                error: function() { Toast.error('Gagal menghapus'); }
            });
        });
    }

    function updatePagination(pagination, onPageChange, containerId) {
        const container = $('#' + (containerId || 'paginationPG'));
        container.html(PaginationHelper.build(pagination, onPageChange));
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
