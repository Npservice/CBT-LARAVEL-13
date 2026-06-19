<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - VEXTA CBT')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/components/swal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/select2.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 5px;
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-900 antialiased flex flex-col min-h-screen overflow-x-hidden">

<input type="checkbox" id="sidebar-toggle" class="hidden peer">

<!-- Header -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-40 w-full flex-shrink-0">
    <div class="border-b border-slate-100 w-full px-6">
        <div class="w-full h-14 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <label for="sidebar-toggle"
                       class="p-1.5 text-slate-500 hover:text-slate-900 rounded-md hover:bg-slate-50 cursor-pointer md:hidden block select-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                         stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </label>
                <div class="flex items-center space-x-2">
                    <div
                        class="w-6 h-6 bg-sky-500 rounded flex items-center justify-center font-bold text-white text-xs">
                        V
                    </div>
                    <span class="font-bold text-sm tracking-tight text-slate-900">VEXTA</span>
                    <span
                        class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] font-semibold tracking-wider uppercase">Console</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div
                    class="hidden sm:flex items-center space-x-1.5 bg-emerald-50 px-2 py-0.5 rounded text-[11px] font-medium text-emerald-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Online</span>
                </div>
                <span class="h-4 w-px bg-slate-200 hidden sm:block"></span>

                <div class="relative group py-2">
                    <button class="flex items-center space-x-2.5 focus:outline-none cursor-pointer">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-slate-900" id="headerUserName">Admin</p>
                            <p class="text-[10px] text-slate-400 font-medium" id="headerUserRole">admin</p>
                        </div>
                        <div
                            class="w-7 h-7 bg-slate-100 rounded border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 group-hover:border-slate-300 transition"
                            id="headerUserAvatar">
                            A
                        </div>
                    </button>
                    <div
                        class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-lg opacity-0 scale-95 pointer-events-none group-hover:opacity-100 group-hover:scale-100 group-hover:pointer-events-auto transition-all duration-150 origin-top-right z-50 overflow-hidden">
                        <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50/50">
                            <p class="text-[10px] font-medium text-slate-400">Signed in as</p>
                            <p class="text-xs font-semibold text-slate-700 truncate" id="headerUserEmail">
                                admin@vexta.sch.id</p>
                        </div>
                        <div class="p-1 space-y-0.5">
                            <a href="#"
                               class="block px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded font-medium transition">Settings</a>
                            <hr class="border-slate-100 my-1">
                            <button onclick="adminLogout()"
                                    class="w-full text-left px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 rounded font-medium transition">
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <x-navigation />
</header>

<!-- Sidebar -->
<x-sidebar />

<!-- Main Content -->
<main class="flex-grow w-full px-6 py-8 space-y-6">
    @yield('content')
</main>

<!-- Footer -->
<footer
    class="bg-white border-t border-slate-200 text-slate-400 text-[11px] font-medium mt-auto w-full px-6 flex-shrink-0">
    <div class="w-full h-12 flex items-center justify-between whitespace-nowrap">
        <p>&copy; 2026 VEXTA CBT System. All rights reserved.</p>
        <p class="text-slate-300">v1.0.0-Beta</p>
    </div>
</footer>

<script src="{{ asset('assets/js/components/toast.js') }}"></script>
<script src="{{ asset('assets/js/components/swal.js') }}"></script>
<script src="{{ asset('assets/js/components/select2.js') }}"></script>
<script>
    let currentUser = null;

    // Helper function to get cookie value
    function getCookie(name) {
        const nameEQ = name + '=';
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let c = cookies[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return null;
    }

    // Helper function to delete cookie
    function deleteCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }

    $(document).ready(function() {
        const token = getCookie('api_token');
        const userData = getCookie('user');


        if (!token || !userData) {
            window.location.href = '/login';
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer ' + token
            }
        });

        $(document).ajaxError(function(event, xhr) {
            if (xhr.status === 401) {
                deleteCookie('api_token');
                deleteCookie('user');
                window.location.href = '/login';
            }
        });

        // Load user from cookie
        try {
            currentUser = JSON.parse(userData);
            updateHeaderUser();
            window.dispatchEvent(new Event('userLoaded'));
        } catch (e) {
            deleteCookie('api_token');
            deleteCookie('user');
            window.location.href = '/login';
        }
    });

    function updateHeaderUser() {
        if (!currentUser) return;
        document.getElementById('headerUserName').textContent = currentUser.name || 'Admin';
        document.getElementById('headerUserRole').textContent = currentUser.role || 'admin';
        document.getElementById('headerUserEmail').textContent = currentUser.email || 'admin@vexta.sch.id';
        document.getElementById('headerUserAvatar').textContent = (currentUser.name || 'A').substring(0, 2).toUpperCase();
    }

    function hasPermission(permission) {
        if (!currentUser) return false;
        if (currentUser.role === 'admin') return true;
        return currentUser.permissions && currentUser.permissions.includes(permission);
    }

    function hasAnyPermission(permissions) {
        return permissions.some(p => hasPermission(p));
    }

    function showIfPermission(element, permission) {
        if (hasPermission(permission)) {
            $(element).show();
        } else {
            $(element).hide();
        }
    }

    function adminLogout() {
        $.ajax({
            url: '/api/v1/auth/logout',
            type: 'POST',
            complete: function() {
                deleteCookie('api_token');
                deleteCookie('user');
                window.location.href = '/login';
            }
        });
    }
</script>
@stack('scripts')
</body>
</html>
