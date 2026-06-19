<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VEXTA CBT - Exam System')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/components/swal.css') }}">

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 5px; width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen bg-[#f8fafc] text-slate-900 antialiased">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 bg-sky-500 rounded flex items-center justify-center font-bold text-white text-xs">V</div>
                        <span class="font-semibold text-sm tracking-tight text-slate-900">VEXTA CBT</span>
                    </div>
                    <span class="h-4 w-px bg-slate-200"></span>
                    <div class="flex items-center space-x-1.5 bg-emerald-50 px-2 py-0.5 rounded text-[11px] font-medium text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Online</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-medium text-slate-900">{{ auth()->user()?->name ?? 'Student' }}</p>
                        <p class="text-[10px] text-slate-400">Siswa</p>
                    </div>
                    <div class="w-7 h-7 bg-slate-100 rounded-md border border-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">
                        {{ substr(auth()->user()?->name ?? 'S', 0, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/60 text-slate-400 text-[11px] font-medium">
        <div class="max-w-7xl mx-auto px-6 h-12 flex items-center justify-between">
            <p>&copy; 2026 VEXTA CBT System. All rights reserved.</p>
            <p class="text-slate-300">v1.0.0-Beta</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/components/toast.js') }}"></script>
    <script src="{{ asset('assets/js/components/swal.js') }}"></script>
    <script>
        // Helper function to get cookie value
        function getCookie(name) {
            const nameEQ = name + "=";
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
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }

        $(document).ready(function() {
            const token = getCookie('api_token');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Authorization': 'Bearer ' + token
                }
            });

            if (!token) {
                window.location.href = '/login';
                return;
            }

            $(document).ajaxError(function(event, xhr) {
                if (xhr.status === 401) {
                    deleteCookie('api_token');
                    deleteCookie('user');
                    window.location.href = '/login';
                }
            });
        });

        function siswaLogout() {
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
