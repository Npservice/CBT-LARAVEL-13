<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - VEXTA CBT System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 to-slate-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-sky-500 px-6 py-8">
            <div class="flex items-center justify-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-white rounded flex items-center justify-center font-bold text-sky-500 text-lg">
                    V
                </div>
                <span class="text-white font-bold text-2xl tracking-tight">VEXTA</span>
            </div>
            <p class="text-sky-100 text-center text-sm">CBT System Login</p>
        </div>

        <!-- Form -->
        <form id="loginForm" class="p-8 space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    required
                />
                <p class="error-message mt-1 text-xs text-rose-500 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    required
                />
                <p class="error-message mt-1 text-xs text-rose-500 hidden"></p>
            </div>

            <button
                type="submit"
                class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2.5 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Login
            </button>
        </form>

        <!-- Footer -->
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 text-center">
            <p class="text-sm text-slate-600">
                Powered by <span class="font-semibold text-slate-900">VEXTA</span> v1.0.0
            </p>
        </div>
    </div>
</div>

<!-- Toast untuk notifikasi -->
<div id="toast"
     class="hidden fixed bottom-4 right-4 z-50 rounded-lg shadow-lg p-4 bg-emerald-50 border border-emerald-200">
    <div class="flex items-center space-x-3">
        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd" />
        </svg>
        <p id="toastMessage" class="text-sm font-medium text-emerald-800">Login successful!</p>
    </div>
</div>

<script src="{{ asset('assets/js/components/toast.js') }}"></script>
<script>
    $(document).ready(function() {
        // Demo credentials
        $('[name="username"]').val('admin');
        $('[name="password"]').val('password');

        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            const $btn = $(this).find('button[type="submit"]');
            const originalText = $btn.text();
            $btn.prop('disabled', true).text('Loading...');

            const formData = {
                username: $('[name="username"]').val(),
                password: $('[name="password"]').val()
            };

            $.ajax({
                url: '/api/v1/auth/login',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Store token in localStorage
                    localStorage.setItem('api_token', response.token);
                    localStorage.setItem('user', JSON.stringify(response.user));

                    // Store in cookie - simple format
                    const date = new Date();
                    date.setTime(date.getTime() + (5 * 60 * 60 * 1000));
                    document.cookie = `api_token=${response.token}; path=/; expires=${date.toUTCString()}`;
                    document.cookie = `user=${JSON.stringify(response.user)}; path=/; expires=${date.toUTCString()}`;

                    const redirectTo = response.user.role === 'siswa' ? '/siswa' : '/admin';
                    Toast.success('Login berhasil! Redirecting...');

                    setTimeout(() => {
                        window.location.href = redirectTo;
                    }, 500);
                },
                error: function(error) {
                    $btn.prop('disabled', false).text(originalText);
                    $('.error-message').addClass('hidden');
                    if (error.responseJSON?.errors) {
                        Object.keys(error.responseJSON.errors).forEach(field => {
                            $(`[name="${field}"]`).next('.error-message')
                                .text(error.responseJSON.errors[field][0])
                                .removeClass('hidden');
                        });
                    } else {
                        Toast.error(error.responseJSON?.message || 'Login gagal');
                    }
                }
            });
        });
    });
</script>
</body>
</html>
