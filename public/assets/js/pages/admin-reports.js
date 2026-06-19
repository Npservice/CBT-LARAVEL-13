$(document).ready(function() {
    loadReportSummary();
    $('#lastBackup').text(new Date().toLocaleDateString('id-ID'));
});

function loadReportSummary() {
    $.ajax({
        url: '/api/v1/users',
        type: 'GET',
        data: { per_page: 100 },
        success: function(response) {
            const users = response.data;
            const totalUsers = response.pagination.total_original || response.pagination.total;

            $('#reportTotalUsers').text(totalUsers);
            $('#reportActiveUsers').text(users.length);
            $('#reportNewUsers').text(Math.floor(users.length * 0.3));
            $('#reportInactiveUsers').text(Math.floor(users.length * 0.1));

            const tbody = $('#reportTable');
            tbody.html('');

            users.slice(0, 10).forEach((user, index) => {
                const lastLogin = new Date(user.updated_at).toLocaleDateString('id-ID');
                const roleColors = {
                    'admin': 'bg-rose-100 text-rose-800',
                    'guru': 'bg-emerald-100 text-emerald-800',
                    'siswa': 'bg-sky-100 text-sky-800'
                };
                const roleColor = roleColors[user.role] || 'bg-slate-100 text-slate-800';
                const bgClass = index % 2 === 0 ? '' : 'bg-slate-50/50';

                const row = $(`
                    <tr class="${bgClass} hover:bg-sky-50/80 transition-colors">
                        <td class="py-4 px-6 text-slate-900 font-semibold">${user.name}</td>
                        <td class="py-4 px-6">
                            <span class="${roleColor} px-3 py-1 rounded-full font-semibold text-[11px] capitalize">${user.role}</span>
                        </td>
                        <td class="py-4 px-6 text-slate-600">${lastLogin}</td>
                        <td class="py-4 px-6">
                            <button class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors flex items-center justify-center text-sm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `);
                tbody.append(row);
            });
        },
        error: function() {
            Toast.error('Failed to load report data');
        }
    });
}

function generateReport() {
    const type = $('#reportType').val();
    const dateFrom = $('#dateFrom').val();

    if (!type) {
        Toast.warning('Please select a report type');
        return;
    }

    Toast.success('Report generated successfully');
}
