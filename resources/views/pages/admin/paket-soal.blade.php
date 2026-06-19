@extends('layouts.admin')

@section('title', 'Paket Soal - Admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Paket Soal</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola paket soal dan soal ujian</p>
            </div>
            <x-button onclick="openCreatePaketModal()" class="gap-2"><i class="fas fa-plus"></i> Tambah</x-button>
        </div>

        <x-card padding="p-4">
            <input type="text" id="searchPaket" placeholder="Cari paket soal..."
                   class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500" />
        </x-card>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Kode Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Pembuat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase">Durasi</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase">Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="tablePaket" class="divide-y divide-slate-200 [&_tr]:transition-colors">
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                                <span>Loading...</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="paginationPaket">
                <x-pagination :currentPage="1" :totalPages="1" :totalRecords="0" :recordsPerPage="10"
                              onPageChange="loadPaketData" />
            </div>
        </div>
    </div>

    {{-- MODAL: Paket Soal --}}
    <x-modal id="modalPaket" title="Tambah Paket Soal" size="md">
        <form id="formPaket" class="space-y-4" onsubmit="savePaketData(event)">
            <input type="hidden" id="paketId" />

            <x-select id="mapel_id" name="mapel_id" label="Mata Pelajaran" placeholder="-- Pilih Mapel --" required />

            {{-- PS guru: read-only display, muncul setelah mapel dipilih (admin only) --}}
            <div id="pembuatSoalBox" class="hidden">
                <label class="block text-sm font-medium text-slate-700 mb-1">Pembuat Soal</label>
                <div class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 flex items-center gap-2">
                    <i class="fas fa-user-tie text-slate-400 text-xs"></i>
                    <span id="pembuatSoalName">-</span>
                </div>
            </div>
            <div id="pembuatSoalEmptyBox" class="hidden">
                <div class="px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                    <span>Mapel ini belum memiliki pembuat soal</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Soal</label>
                <input type="text" id="kode_soal" name="kode_soal" placeholder="KD-2024-001" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Durasi (Menit)</label>
                <input type="number" id="durasi_menit" name="durasi_menit" placeholder="60" min="1" required
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" />
            </div>
            <div class="flex gap-3 pt-4">
                <x-button type="submit" class="flex-1"><i class="fas fa-save"></i> Simpan</x-button>
                <x-button type="button" variant="secondary" onclick="Modal.close('modalPaket')" class="flex-1">Batal</x-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script src="{{ asset('assets/js/components/modal.js') }}"></script>
        <script src="{{ asset('assets/js/helpers/pagination.js') }}"></script>
        <script>
            let currentPage = 1;
            let editingPaketId = null;
            let psLookup = {}; // mata_pelajaran_id → nama guru PS

            window.addEventListener('userLoaded', function () {
                loadSelectData();
                loadPaketData(1);
            });

            $(document).ready(function() {
                $('#searchPaket').on('keyup', debounce(() => loadPaketData(1), 300));
                $('#mapel_id').on('change', onMapelChange);
            });

            function isGuruPembuatSoal() {
                return currentUser && currentUser.role === 'guru-pembuat-soal';
            }

            function reinitMapelS2(placeholder) {
                const $el = $('#mapel_id');
                if ($el.data('select2')) $el.select2('destroy');
                Select2Helper.init('#mapel_id', { placeholder, dropdownParent: $('#modalPaket') });
            }

            function loadSelectData() {
                if (isGuruPembuatSoal()) {
                    // Hanya mapel yang dia jadi PS-nya
                    $.ajax({
                        url: '/api/v1/guru-pengampu/saya',
                        success: r => {
                            const seen = new Set();
                            const $sel = $('#mapel_id');
                            $sel.html('<option value=""></option>');
                            r.data.filter(i => i.pembuat_soal).forEach(i => {
                                if (!seen.has(i.mata_pelajaran_id)) {
                                    seen.add(i.mata_pelajaran_id);
                                    $sel.append(`<option value="${i.mata_pelajaran_id}">${i.mapel} - ${i.kode_mapel}</option>`);
                                }
                            });
                            reinitMapelS2('-- Pilih Mapel --');
                        }
                    });
                } else {
                    // Admin: semua mapel + build psLookup dari guru_pengampu
                    $.ajax({
                        url: '/api/v1/mata-pelajaran',
                        data: { per_page: 300 },
                        success: r => {
                            const $sel = $('#mapel_id');
                            $sel.html('<option value=""></option>');
                            r.data.forEach(m => {
                                $sel.append(`<option value="${m.id}">${m.mapel} - ${m.kode_mapel}</option>`);
                            });
                            reinitMapelS2('-- Pilih Mapel --');
                        }
                    });
                    $.ajax({
                        url: '/api/v1/guru-pengampu',
                        data: { per_page: 500 },
                        success: r => {
                            psLookup = {};
                            r.data.filter(gp => gp.pembuat_soal).forEach(gp => {
                                if (!psLookup[gp.mata_pelajaran_id]) {
                                    psLookup[gp.mata_pelajaran_id] = gp.guru?.nama || '-';
                                }
                            });
                        }
                    });
                }
            }

            function onMapelChange() {
                const mapelId = $('#mapel_id').val();

                // Reset display
                $('#pembuatSoalBox').addClass('hidden');
                $('#pembuatSoalEmptyBox').addClass('hidden');

                if (!mapelId || isGuruPembuatSoal()) return;

                const namaPS = psLookup[mapelId];
                if (namaPS) {
                    $('#pembuatSoalName').text(namaPS);
                    $('#pembuatSoalBox').removeClass('hidden');
                } else {
                    $('#pembuatSoalEmptyBox').removeClass('hidden');
                }
            }

            function loadPaketData(page) {
                currentPage = page || currentPage;
                $.ajax({
                    url: '/api/v1/paket-soal',
                    data: { search: $('#searchPaket').val(), page: currentPage, per_page: 10 },
                    success: function(response) {
                        const tbody = $('#tablePaket');
                        tbody.html('');
                        if (response.data.length === 0) {
                            tbody.html('<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada data</td></tr>');
                            return;
                        }
                        response.data.forEach(item => {
                            tbody.append(`
                                <tr class="hover:bg-sky-50/80 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-900">${item.kode_soal}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">${item.mata_pelajaran}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">${item.dibuat_oleh || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600">${item.durasi_menit} menit</td>
                                    <td class="px-6 py-4 text-sm text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="/admin/paket-soal/${item.id}/soal" class="inline-flex items-center justify-center h-7 w-7 rounded bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition-colors text-xs" title="Kelola Soal"><i class="fas fa-book"></i></a>
                                            <button onclick="editPaket('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button onclick="deletePaket('${item.id}')" class="inline-flex items-center justify-center h-7 w-7 rounded bg-red-100 text-red-600 hover:bg-red-200 transition-colors text-xs" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });
                        updatePagination(response.pagination, 'loadPaketData');
                    }
                });
            }

            function openCreatePaketModal() {
                editingPaketId = null;
                $('#formPaket')[0].reset();
                $('#mapel_id').val(null).trigger('change');
                $('#pembuatSoalBox').addClass('hidden');
                $('#pembuatSoalEmptyBox').addClass('hidden');
                $('#modalPaket [data-modal-title]').text('Tambah Paket Soal');
                Modal.open('modalPaket');
            }

            function editPaket(id) {
                $.ajax({
                    url: `/api/v1/paket-soal/${id}`,
                    success: r => {
                        editingPaketId = id;
                        $('#paketId').val(id);
                        $('#mapel_id').val(r.data.mapel_id).trigger('change');
                        $('#kode_soal').val(r.data.kode_soal);
                        $('#durasi_menit').val(r.data.durasi_menit);
                        $('#modalPaket [data-modal-title]').text('Edit Paket Soal');
                        Modal.open('modalPaket');
                    }
                });
            }

            function savePaketData(e) {
                e.preventDefault();
                const data = {
                    mapel_id:     $('#mapel_id').val(),
                    kode_soal:    $('#kode_soal').val(),
                    durasi_menit: parseInt($('#durasi_menit').val()),
                };
                $.ajax({
                    url: editingPaketId ? `/api/v1/paket-soal/${editingPaketId}` : '/api/v1/paket-soal',
                    type: editingPaketId ? 'PUT' : 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: r => {
                        Toast.success(r.message);
                        Modal.close('modalPaket');
                        loadPaketData(1);
                    },
                    error: e => {
                        const errors = e.responseJSON?.errors;
                        if (errors) Object.values(errors).forEach(m => Toast.error(m[0]));
                        else Toast.error(e.responseJSON?.message || 'Gagal menyimpan');
                    }
                });
            }

            function deletePaket(id) {
                SwalHelper.confirmDelete('Hapus Paket Soal?', 'Semua soal di dalamnya juga akan terhapus dan tidak dapat dikembalikan.', function() {
                    $.ajax({
                        url: `/api/v1/paket-soal/${id}`,
                        type: 'DELETE',
                        success: () => { Toast.success('Dihapus'); loadPaketData(1); },
                        error: () => Toast.error('Gagal menghapus')
                    });
                });
            }

            function updatePagination(pagination, onPageChange) {
                $('#paginationPaket').html(PaginationHelper.build(pagination, onPageChange));
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
