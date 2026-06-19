@extends('layouts.admin')

@section('title', 'Role & Permission - Admin')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-lg font-semibold text-slate-900">Role & Permission</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola hak akses setiap role pengguna</p>
    </div>

    {{-- Role Cards --}}
    <div id="roleCards" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex items-center justify-center text-slate-400">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat...
        </div>
    </div>

</div>

{{-- Modal: Kelola Permission --}}
<x-modal id="modalPermission" title="Kelola Permission" size="2xl">
    <x-slot:footer>
        <div class="flex items-center justify-between gap-3 w-full">
            <p class="text-xs text-slate-400" id="permSummary"></p>
            <div class="flex gap-2">
                <button onclick="Modal.close('modalPermission')"
                        class="px-4 py-2 text-sm text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button onclick="savePermissions()" id="btnSavePerms"
                        class="px-4 py-2 text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 rounded-lg transition">
                    Simpan
                </button>
            </div>
        </div>
    </x-slot:footer>

    {{-- Role info --}}
    <div class="mb-4 pb-4 border-b border-slate-100">
        <p class="text-xs text-slate-400" id="modalRoleSlug"></p>
    </div>

    {{-- Permission Groups --}}
    <div id="permissionGroups" class="space-y-5">
        <div class="flex items-center justify-center py-8 text-slate-400">
            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat...
        </div>
    </div>
</x-modal>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/components/modal.js') }}"></script>
<script>
    let allPermissions = [];
    let currentRoleId = null;

    $(document).ready(function () {
        loadRoles();
        loadPermissions();
    });

    function loadPermissions() {
        $.ajax({
            url: '/api/v1/permissions',
            success: function (res) {
                allPermissions = res.data;
            }
        });
    }

    function loadRoles() {
        $.ajax({
            url: '/api/v1/roles',
            success: function (res) {
                renderRoles(res.data);
            },
            error: function () {
                $('#roleCards').html('<p class="text-rose-500 text-sm text-center col-span-3 py-10">Gagal memuat data role.</p>');
            }
        });
    }

    const ROLE_COLORS = {
        'admin':             { badge: 'bg-violet-100 text-violet-700', icon: 'fa-crown',              ring: 'border-violet-200' },
        'guru-pembuat-soal': { badge: 'bg-amber-100 text-amber-700',   icon: 'fa-pen-to-square',      ring: 'border-amber-200' },
        'guru':              { badge: 'bg-sky-100 text-sky-700',        icon: 'fa-chalkboard-teacher', ring: 'border-sky-200' },
        'siswa':             { badge: 'bg-emerald-100 text-emerald-700',icon: 'fa-user-graduate',      ring: 'border-emerald-200' },
    };

    function renderRoles(roles) {
        let html = '';
        roles.forEach(function (role) {
            const color = ROLE_COLORS[role.nama_role] || { badge: 'bg-slate-100 text-slate-600', icon: 'fa-user', ring: 'border-slate-200' };
            const permCount = role.permissions ? role.permissions.length : 0;
            const isAdmin = role.nama_role === 'admin';
            const isSiswa = role.nama_role === 'siswa';

            html += `
                <div class="bg-white rounded-xl border ${color.ring} shadow-sm p-5 space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center ${color.badge} text-lg">
                                <i class="fas ${color.icon}"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">${role.display_role}</h3>
                                <code class="text-[11px] text-slate-400">${role.nama_role}</code>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold ${color.badge} px-2 py-0.5 rounded-full">
                            ${isAdmin ? 'Semua Permission' : permCount + ' permission'}
                        </span>
                    </div>

                    ${isAdmin
                        ? '<p class="text-xs text-slate-400">Administrator memiliki semua permission secara otomatis.</p>'
                        : isSiswa
                            ? '<p class="text-xs text-slate-400">Siswa menggunakan role-based access, tidak perlu permission.</p>'
                            : `<div class="flex flex-wrap gap-1.5">${renderPermBadges(role.permissions)}</div>`
                    }

                    ${(!isAdmin && !isSiswa) ? `
                    <button onclick="openPermModal('${role.id}', '${role.display_role}', '${role.nama_role}')"
                        class="w-full text-xs font-semibold text-sky-600 border border-sky-200 hover:bg-sky-50 rounded-lg py-2 transition">
                        <i class="fas fa-shield-halved mr-1"></i> Kelola Permission
                    </button>
                    ` : ''}
                </div>
            `;
        });
        $('#roleCards').html(html);
    }

    function renderPermBadges(permissions) {
        if (!permissions || permissions.length === 0) {
            return '<span class="text-xs text-slate-300 italic">Belum ada permission</span>';
        }
        return permissions.slice(0, 6).map(p =>
            `<span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-medium">${p}</span>`
        ).join('') + (permissions.length > 6
            ? `<span class="text-[10px] text-slate-400 font-medium">+${permissions.length - 6} lagi</span>`
            : '');
    }

    function openPermModal(roleId, displayName, namaRole) {
        currentRoleId = roleId;

        $('#modalPermission [data-modal-title]').text(displayName);
        $('#modalRoleSlug').text(namaRole);
        $('#permSummary').text('');
        $('#permissionGroups').html('<div class="flex items-center justify-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat...</div>');

        Modal.open('modalPermission');

        $.ajax({
            url: '/api/v1/roles',
            success: function (res) {
                const role = res.data.find(r => r.id === roleId);
                const checkedPerms = role ? (role.permissions || []) : [];
                renderPermissionGroups(checkedPerms);
            }
        });
    }

    const GROUP_LABELS = {
        'user-management':        'User Management',
        'role-management':        'Role & Permission',
        'siswa-management':       'Siswa',
        'guru-management':        'Guru',
        'guru-pengampu-management':'Guru Pengampu',
        'master-data':            'Master Data',
        'paket-soal':             'Paket Soal',
        'soal':                   'Soal',
        'sesi-ujian':             'Sesi Ujian',
        'hasil':                  'Hasil Ujian',
    };

    function renderPermissionGroups(checkedPerms) {
        if (!allPermissions || allPermissions.length === 0) {
            $('#permissionGroups').html('<p class="text-sm text-slate-400 text-center py-4">Memuat permission...</p>');
            return;
        }

        let html = '';
        allPermissions.forEach(function (group) {
            const label = GROUP_LABELS[group.group] || group.group;
            const allChecked = group.permissions.every(p => checkedPerms.includes(p.nama_permission));

            html += `
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">${label}</h4>
                        <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-500 hover:text-slate-700">
                            <input type="checkbox" class="group-toggle rounded" data-group="${group.group}" ${allChecked ? 'checked' : ''}
                                onchange="toggleGroup('${group.group}', this.checked)">
                            Semua
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 rounded-xl p-3">
            `;

            group.permissions.forEach(function (perm) {
                const checked = checkedPerms.includes(perm.nama_permission);
                html += `
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="permissions[]" value="${perm.id}"
                            data-name="${perm.nama_permission}" data-group="${group.group}"
                            class="perm-checkbox rounded accent-sky-500" ${checked ? 'checked' : ''}
                            onchange="updateSummary()">
                        <span class="text-xs text-slate-700 group-hover:text-slate-900">${perm.display_permission}</span>
                    </label>
                `;
            });

            html += `</div></div>`;
        });

        $('#permissionGroups').html(html);
        updateSummary();
    }

    function toggleGroup(group, checked) {
        $(`input.perm-checkbox[data-group="${group}"]`).prop('checked', checked);
        updateSummary();
    }

    function updateSummary() {
        const total    = $('input.perm-checkbox').length;
        const selected = $('input.perm-checkbox:checked').length;
        $('#permSummary').text(`${selected} dari ${total} permission dipilih`);

        $('[data-group]').each(function () {
            if ($(this).hasClass('group-toggle')) {
                const group   = $(this).data('group');
                const all     = $(`input.perm-checkbox[data-group="${group}"]`);
                const checked = $(`input.perm-checkbox[data-group="${group}"]:checked`);
                $(this).prop('checked',       all.length > 0 && checked.length === all.length);
                $(this).prop('indeterminate', checked.length > 0 && checked.length < all.length);
            }
        });
    }

    function savePermissions() {
        if (!currentRoleId) return;

        const permIds = $('input.perm-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        $('#btnSavePerms').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: `/api/v1/roles/${currentRoleId}/permissions`,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ permissions: permIds }),
            success: function () {
                Toast.success('Permission berhasil disimpan');
                Modal.close('modalPermission');
                loadRoles();
            },
            error: function (xhr) {
                Toast.error(xhr.responseJSON?.message || 'Gagal menyimpan');
            },
            complete: function () {
                $('#btnSavePerms').prop('disabled', false).html('Simpan');
            }
        });
    }
</script>
@endpush
