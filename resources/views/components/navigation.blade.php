@props(['active' => null])

<div class="bg-slate-50/50 w-full px-6 hidden md:block overflow-visible custom-scrollbar border-b border-slate-200">
    <nav class="flex items-center space-x-1 h-11 text-xs font-medium whitespace-nowrap">

        {{-- Dashboard: selalu tampil --}}
        <a id="nav-dashboard" href="{{ route('admin.dashboard') }}"
           class="hidden items-center px-3 h-full border-b-2 {{ request()->is('admin') && !request()->is('admin/*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
            Dashboard
        </a>

        {{-- User --}}
        <a id="nav-users" href="{{ route('admin.users') }}"
           class="hidden items-center px-3 h-full border-b-2 {{ request()->is('admin/users*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
            User
        </a>

        {{-- Role & Permission --}}
        <a id="nav-roles" href="{{ route('admin.roles') }}"
           class="hidden items-center px-3 h-full border-b-2 {{ request()->is('admin/roles*', 'admin/permissions*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
            Role & Permission
        </a>

        {{-- User Data Dropdown --}}
        <div id="nav-userdata" class="hidden relative h-full items-center">
            <button type="button" onclick="toggleUserDataDropdown(event)"
                    class="flex items-center px-3 h-full border-b-2 {{ request()->is('admin/siswa*', 'admin/guru*', 'admin/guru-pengampu*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
                User Data
                <svg class="w-3.5 h-3.5 ml-1.5 transition-transform duration-200" id="userDataArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="userDataMenu" class="hidden absolute left-0 top-12 w-48 bg-white border border-slate-200 rounded-lg shadow-2xl z-[9999] py-1">
                <a id="nav-siswa" href="{{ route('admin.siswa') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/siswa*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Siswa
                </a>
                <a id="nav-guru" href="{{ route('admin.guru') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/guru*') && !request()->is('admin/guru-pengampu*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Guru
                </a>
                <a id="nav-guru-pengampu" href="{{ route('admin.guru-pengampu') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/guru-pengampu*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Guru Pengampu
                </a>
            </div>
        </div>

        {{-- Master Data Dropdown --}}
        <div id="nav-masterdata" class="hidden relative h-full items-center">
            <button type="button" onclick="toggleDropdown(event)"
                    class="flex items-center px-3 h-full border-b-2 {{ request()->is('admin/institusi*', 'admin/kelas*', 'admin/jurusan*', 'admin/mata-pelajaran*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
                Master Data
                <svg class="w-3.5 h-3.5 ml-1.5 transition-transform duration-200" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="masterDataMenu" class="hidden absolute left-0 top-12 w-52 bg-white border border-slate-200 rounded-lg shadow-2xl z-[9999] py-1">
                <a id="nav-institusi" href="{{ route('admin.institusi') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/institusi*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Institusi
                </a>
                <a id="nav-kelas" href="{{ route('admin.kelas') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/kelas*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Kelas
                </a>
                <a id="nav-jurusan" href="{{ route('admin.jurusan') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/jurusan*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Jurusan
                </a>
                <a id="nav-mapel" href="{{ route('admin.mata-pelajaran') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/mata-pelajaran*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Mata Pelajaran
                </a>
            </div>
        </div>

        {{-- Ujian Dropdown --}}
        <div id="nav-ujian" class="hidden relative h-full items-center">
            <button type="button" onclick="toggleExamDropdown(event)"
                    class="flex items-center px-3 h-full border-b-2 {{ request()->is('admin/paket-soal*', 'admin/sesi-ujian*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
                Ujian
                <svg class="w-3.5 h-3.5 ml-1.5 transition-transform duration-200" id="examArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="examMenu" class="hidden absolute left-0 top-12 w-48 bg-white border border-slate-200 rounded-lg shadow-2xl z-[9999] py-1">
                <a id="nav-paket-soal" href="{{ route('admin.paket-soal') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/paket-soal*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Paket Soal
                </a>
                <a id="nav-sesi-ujian" href="{{ route('admin.sesi-ujian') }}"
                   class="hidden px-4 py-2.5 text-xs {{ request()->is('admin/sesi-ujian*') ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }} transition">
                    Sesi Ujian
                </a>
            </div>
        </div>

        {{-- Hasil --}}
        <a id="nav-hasil" href="{{ route('admin.hasil') }}"
           class="hidden items-center px-3 h-full border-b-2 {{ request()->is('admin/hasil*') ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-900' }} transition">
            Hasil
        </a>

    </nav>
</div>

<script>
    window.addEventListener('userLoaded', function () {
        applyNavPermissions();
    });

    function applyNavPermissions() {
        // Helper: show element as flex
        const show = (id) => {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
        };
        // Helper: show element as block
        const showBlock = (id) => {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('hidden'); el.classList.add('block'); }
        };

        // Dashboard: selalu tampil
        show('nav-dashboard');

        // User management
        if (hasPermission('manage-users')) show('nav-users');

        // Role & Permission
        if (hasPermission('manage-roles')) show('nav-roles');

        // User Data: tampilkan item & parent dropdown
        const showSiswa    = hasPermission('view-siswa');
        const showGuru     = hasPermission('view-guru');
        const showGP       = hasPermission('view-guru-pengampu');
        if (showSiswa)    showBlock('nav-siswa');
        if (showGuru)     showBlock('nav-guru');
        if (showGP)       showBlock('nav-guru-pengampu');
        if (showSiswa || showGuru || showGP) show('nav-userdata');

        // Master Data: tampilkan item & parent dropdown
        const showInstitusi = hasPermission('view-institusi');
        const showKelas     = hasPermission('view-kelas');
        const showJurusan   = hasPermission('view-jurusan');
        const showMapel     = hasPermission('view-mata-pelajaran');
        if (showInstitusi) showBlock('nav-institusi');
        if (showKelas)     showBlock('nav-kelas');
        if (showJurusan)   showBlock('nav-jurusan');
        if (showMapel)     showBlock('nav-mapel');
        if (showInstitusi || showKelas || showJurusan || showMapel) show('nav-masterdata');

        // Hasil
        if (hasPermission('view-hasil')) show('nav-hasil');

        // Ujian
        const showPaket = hasPermission('view-paket-soal');
        const showSesi  = hasPermission('view-sesi-ujian');
        if (showPaket) showBlock('nav-paket-soal');
        if (showSesi)  showBlock('nav-sesi-ujian');
        if (showPaket || showSesi) show('nav-ujian');
    }

    function toggleUserDataDropdown(event) {
        event.preventDefault(); event.stopPropagation();
        document.getElementById('userDataMenu').classList.toggle('hidden');
        document.getElementById('userDataArrow').classList.toggle('rotate-180');
    }

    function toggleDropdown(event) {
        event.preventDefault(); event.stopPropagation();
        document.getElementById('masterDataMenu').classList.toggle('hidden');
        document.getElementById('dropdownArrow').classList.toggle('rotate-180');
    }

    function toggleExamDropdown(event) {
        event.preventDefault(); event.stopPropagation();
        document.getElementById('examMenu').classList.toggle('hidden');
        document.getElementById('examArrow').classList.toggle('rotate-180');
    }

    document.addEventListener('click', function(event) {
        [
            { wrap: 'nav-userdata',   menu: 'userDataMenu',   arrow: 'userDataArrow' },
            { wrap: 'nav-masterdata', menu: 'masterDataMenu', arrow: 'dropdownArrow' },
            { wrap: 'nav-ujian',      menu: 'examMenu',       arrow: 'examArrow' },
        ].forEach(({ wrap, menu, arrow }) => {
            const wrapper = document.getElementById(wrap);
            if (wrapper && !wrapper.contains(event.target)) {
                document.getElementById(menu)?.classList.add('hidden');
                document.getElementById(arrow)?.classList.remove('rotate-180');
            }
        });
    });
</script>
