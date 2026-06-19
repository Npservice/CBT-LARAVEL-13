@props(['version' => 'v1.0.0-Beta'])

<!-- Mobile Sidebar Overlay -->
<label for="sidebar-toggle"
       class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 md:hidden hidden peer-checked:block transition-all duration-300"></label>

<!-- Mobile Sidebar -->
<aside
    class="fixed top-0 bottom-0 left-0 w-64 bg-white z-50 md:hidden border-r border-slate-200 transform -translate-x-full peer-checked:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col justify-between">
    <div>
        <div class="h-14 flex items-center px-6 border-b border-slate-100 justify-between">
            <span class="font-bold text-xs tracking-tight text-slate-900">VEXTA ADMIN</span>
            <label for="sidebar-toggle" class="p-1 text-slate-400 hover:text-slate-900 rounded-md cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                     stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </label>
        </div>

        <nav class="p-4 space-y-1 text-xs font-medium">

            {{-- Dashboard: selalu tampil --}}
            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2.5 rounded-lg {{ request()->is('admin') && !request()->is('admin/*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                Dashboard
            </a>

            {{-- User --}}
            <a id="sb-users" href="{{ route('admin.users') }}"
               class="hidden px-3 py-2.5 rounded-lg {{ request()->is('admin/users*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                User
            </a>

            {{-- Role & Permission --}}
            <a id="sb-roles" href="{{ route('admin.roles') }}"
               class="hidden px-3 py-2.5 rounded-lg {{ request()->is('admin/roles*', 'admin/permissions*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                Role & Permission
            </a>

            {{-- User Data --}}
            <div id="sb-userdata" class="hidden">
                <button onclick="toggleUserDataMenu()"
                        class="w-full text-left px-3 py-2.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition flex items-center justify-between">
                    User Data
                    <svg id="sbUserDataArrow" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="sbUserDataSubmenu" class="hidden space-y-1 bg-slate-50/50 rounded mx-1 px-2 py-1">
                    <a id="sb-siswa" href="{{ route('admin.siswa') }}"
                       class="hidden px-3 py-2 text-sm rounded {{ request()->is('admin/siswa*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} transition">
                        Siswa
                    </a>
                    <a id="sb-guru" href="{{ route('admin.guru') }}"
                       class="hidden px-3 py-2 text-sm rounded {{ request()->is('admin/guru*') && !request()->is('admin/guru-pengampu*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} transition">
                        Guru
                    </a>
                    <a id="sb-guru-pengampu" href="{{ route('admin.guru-pengampu') }}"
                       class="hidden px-3 py-2 text-sm rounded {{ request()->is('admin/guru-pengampu*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} transition">
                        Guru Pengampu
                    </a>
                </div>
            </div>

            {{-- Ujian --}}
            <div id="sb-ujian" class="hidden">
                <button onclick="toggleExamMenu()"
                        class="w-full text-left px-3 py-2.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition flex items-center justify-between">
                    Ujian
                    <svg id="sbExamArrow" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="sbExamSubmenu" class="hidden space-y-1 bg-slate-50/50 rounded mx-1 px-2 py-1">
                    <a id="sb-paket-soal" href="{{ route('admin.paket-soal') }}"
                       class="hidden px-3 py-2 text-sm rounded {{ request()->is('admin/paket-soal*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} transition">
                        Paket Soal
                    </a>
                    <a id="sb-sesi-ujian" href="{{ route('admin.sesi-ujian') }}"
                       class="hidden px-3 py-2 text-sm rounded {{ request()->is('admin/sesi-ujian*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-600 hover:bg-slate-100' }} transition">
                        Sesi Ujian
                    </a>
                </div>
            </div>

            {{-- Master Data Section --}}
            <div id="sb-masterdata-section" class="hidden">
                <hr class="my-3 border-slate-200">
                <div class="px-3 py-1.5 mb-2">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Master Data</p>
                </div>
                <a id="sb-institusi" href="{{ route('admin.institusi') }}"
                   class="hidden px-3 py-2 text-sm rounded-lg {{ request()->is('admin/institusi*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                    Institusi
                </a>
                <a id="sb-kelas" href="{{ route('admin.kelas') }}"
                   class="hidden px-3 py-2 text-sm rounded-lg {{ request()->is('admin/kelas*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                    Kelas
                </a>
                <a id="sb-jurusan" href="{{ route('admin.jurusan') }}"
                   class="hidden px-3 py-2 text-sm rounded-lg {{ request()->is('admin/jurusan*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                    Jurusan
                </a>
                <a id="sb-mapel" href="{{ route('admin.mata-pelajaran') }}"
                   class="hidden px-3 py-2 text-sm rounded-lg {{ request()->is('admin/mata-pelajaran*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                    Mata Pelajaran
                </a>
            </div>

            {{-- Hasil --}}
            <a id="sb-hasil" href="{{ route('admin.hasil') }}"
               class="hidden px-3 py-2.5 rounded-lg {{ request()->is('admin/hasil*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} transition">
                Hasil Ujian
            </a>

        </nav>
    </div>

    <div class="p-4 border-t border-slate-100 text-[10px] text-slate-400 font-medium">
        VEXTA {{ $version }}
    </div>
</aside>

<script>
    window.addEventListener('userLoaded', function () {
        applySidebarPermissions();
    });

    function applySidebarPermissions() {
        const show = (id) => {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('hidden'); el.classList.add('block'); }
        };

        // User
        if (hasPermission('manage-users')) show('sb-users');

        // Role & Permission
        if (hasPermission('manage-roles')) show('sb-roles');

        // User Data
        const showSiswa = hasPermission('view-siswa');
        const showGuru  = hasPermission('view-guru');
        const showGP    = hasPermission('view-guru-pengampu');
        if (showSiswa) show('sb-siswa');
        if (showGuru)  show('sb-guru');
        if (showGP)    show('sb-guru-pengampu');
        if (showSiswa || showGuru || showGP) show('sb-userdata');

        // Ujian
        const showPaket = hasPermission('view-paket-soal');
        const showSesi  = hasPermission('view-sesi-ujian');
        if (showPaket) show('sb-paket-soal');
        if (showSesi)  show('sb-sesi-ujian');
        if (showPaket || showSesi) show('sb-ujian');

        // Master Data
        const showInstitusi = hasPermission('view-institusi');
        const showKelas     = hasPermission('view-kelas');
        const showJurusan   = hasPermission('view-jurusan');
        const showMapel     = hasPermission('view-mata-pelajaran');
        if (showInstitusi) show('sb-institusi');
        if (showKelas)     show('sb-kelas');
        if (showJurusan)   show('sb-jurusan');
        if (showMapel)     show('sb-mapel');
        if (showInstitusi || showKelas || showJurusan || showMapel) show('sb-masterdata-section');

        // Hasil (paling bawah)
        if (hasPermission('view-hasil')) show('sb-hasil');
    }

    function toggleUserDataMenu() {
        document.getElementById('sbUserDataSubmenu').classList.toggle('hidden');
        document.getElementById('sbUserDataArrow').classList.toggle('rotate-180');
    }

    function toggleExamMenu() {
        document.getElementById('sbExamSubmenu').classList.toggle('hidden');
        document.getElementById('sbExamArrow').classList.toggle('rotate-180');
    }
</script>
