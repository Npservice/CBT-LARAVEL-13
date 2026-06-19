<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VEXTA CBT — Exam System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
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
                        <span id="examStatus">Ujian Aktif</span>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-200 px-3 py-1 rounded-md flex items-center space-x-2">
                    <span class="text-xs font-mono font-bold tracking-wider text-slate-700">Sisa Waktu: <span id="timer">01:45:22</span></span>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="text-right hidden md:block">
                        <p class="text-xs font-medium text-slate-900">Aditya Pratama</p>
                        <p class="text-[10px] text-slate-400">X-RPL-1 • Siswa</p>
                    </div>
                    <div class="w-7 h-7 bg-slate-100 rounded-md border border-slate-200 flex items-center justify-center text-xs font-semibold text-slate-600">AP</div>
                </div>
            </div>
        </div>

        <!-- Subject & Progress -->
        <div class="bg-white border-t border-slate-100 py-3 px-6">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                <div class="flex items-center space-x-2">
                    <span class="font-medium text-slate-700">Mata Pelajaran:</span>
                    <span class="bg-sky-50 text-sky-700 px-2 py-0.5 rounded font-medium">Pemrograman Web (MAPEL-01)</span>
                </div>
                <div>
                    <span>Progress: <strong class="text-slate-800">25% (10/40 Soal)</strong></span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-4">
                <!-- Question Card -->
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                            <div class="flex items-center space-x-3">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pertanyaan <span id="questionNumber">01</span></span>
                                <span class="text-[11px] font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded">Bobot: <span id="questionScore">15</span> Poin</span>
                            </div>

                            <button onclick="ExamPage.toggleMark()" class="flex items-center space-x-1.5 px-2.5 py-1 rounded transition text-[11px] font-medium bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                    <path d="M3.5 2.75a.75.75 0 0 1 .75.75v3.31a5.5 5.5 0 0 1 10.512-.412l.142.427a4 4 0 0 0 7.646-.3l.141-.424a.75.75 0 1 1 1.424.475l-.142.424a5.5 5.5 0 0 1-10.512.412l-.142-.427a4 4 0 0 0-7.646.3v6.52a.75.75 0 0 1-1.5 0V3.5a.75.75 0 0 1 .75-.75Z" />
                                </svg>
                                <span id="markText">Tandai</span>
                            </button>
                        </div>

                        <h2 class="text-sm sm:text-base font-medium text-slate-900 mb-8 leading-relaxed" id="questionText">
                            Manakah di bawah ini yang merupakan keuntungan utama dari implementasi arsitektur penyimpanan berbasis JSON murni pada aplikasi kuis skala kecil?
                        </h2>

                        <div class="space-y-2.5" id="optionsContainer">
                            <!-- Options will be rendered here -->
                        </div>
                    </div>

                    <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 sm:px-8 sm:py-6 flex items-center justify-between">
                        <button onclick="ExamPage.prevQuestion()" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm">
                            ← Sebelumnya
                        </button>

                        <button onclick="ExamPage.nextQuestion()" class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                            Selanjutnya →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Question Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-sm sticky top-40 max-h-[calc(100vh-200px)] overflow-y-auto custom-scrollbar">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-4">Navigasi Soal</h3>

                    <div class="grid grid-cols-5 gap-2" id="questionGrid">
                        <!-- Question buttons will be rendered here -->
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 space-y-2.5 text-[11px] text-slate-400 font-medium">
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded bg-emerald-500 mr-2"></span>
                            <span>Terjawab (<span id="answeredCount">10</span>)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded bg-indigo-500 mr-2"></span>
                            <span>Ditandai (<span id="markedCount">2</span>)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded border border-slate-200 bg-white mr-2"></span>
                            <span>Belum Dijawab</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200/60 text-slate-400 text-[11px] font-medium mt-auto">
        <div class="max-w-7xl mx-auto px-6 h-12 flex items-center justify-between">
            <p>&copy; 2026 VEXTA CBT System. All rights reserved.</p>
            <p class="text-slate-300">v1.0.0-Beta</p>
        </div>
    </footer>

    <script src="{{ asset('assets/js/pages/exam.js') }}"></script>
</body>
</html>
