<!-- Toast Container - CSS only, JS creates dynamic toasts -->
<div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2 max-w-sm pointer-events-none"></div>

<!-- Toast Script (auto-loaded) -->
<script>
    // Extend Toast object to insert toasts into container
    const originalToastShow = Toast.show;
    Toast.show = function(message, type = 'info', duration = 5000) {
        const toastTypes = {
            'success': {
                bg: 'bg-emerald-50',
                border: 'border-emerald-200',
                text: 'text-emerald-800',
                icon: '✓'
            },
            'error': {
                bg: 'bg-rose-50',
                border: 'border-rose-200',
                text: 'text-rose-800',
                icon: '✕'
            },
            'warning': {
                bg: 'bg-amber-50',
                border: 'border-amber-200',
                text: 'text-amber-800',
                icon: '⚠'
            },
            'info': {
                bg: 'bg-sky-50',
                border: 'border-sky-200',
                text: 'text-sky-800',
                icon: 'ℹ'
            }
        };

        const config = toastTypes[type] || toastTypes['info'];
        const container = document.getElementById('toast-container');

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto rounded-lg shadow-lg p-4 border ${config.bg} ${config.border} animate-in fade-in slide-in-from-right-10`;
        toast.innerHTML = `
            <div class="flex items-center space-x-3">
                <span class="text-lg font-bold ${config.text}">${config.icon}</span>
                <p class="text-sm font-medium ${config.text}">${message}</p>
            </div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease-in forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };
</script>

<style>
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>
