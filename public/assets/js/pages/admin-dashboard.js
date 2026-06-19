$(document).ready(function() {
    loadDashboardStats();
});

function loadDashboardStats() {
    $.ajax({
        url: '/api/v1/users',
        type: 'GET',
        data: { per_page: 100 },
        success: function(response) {
            const users = response.data;
            // Use total_original to always show total users count
            const totalUsers = response.pagination.total_original || response.pagination.total;
            const adminCount = users.filter(u => u.role === 'admin').length;
            const guruCount = users.filter(u => u.role === 'guru').length;
            const siswaCount = users.filter(u => u.role === 'siswa').length;

            $('#totalUsers').text(totalUsers);
            $('#adminCount').text(adminCount);
            $('#guruCount').text(guruCount);
            $('#siswaCount').text(siswaCount);

            const recentUsers = users.slice(0, 5);
            const tbody = $('#recentUsersTable');
            tbody.html('');

            recentUsers.forEach((user, index) => {
                const createdDate = new Date(user.created_at).toLocaleDateString('id-ID');
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
                        <td class="py-4 px-6 text-slate-600 font-mono bg-slate-50/40 px-2 rounded">${user.username}</td>
                        <td class="py-4 px-6 text-slate-600">${user.email}</td>
                        <td class="py-4 px-6">
                            <span class="${roleColor} px-3 py-1 rounded-full font-semibold text-[11px] capitalize">${user.role}</span>
                        </td>
                        <td class="py-4 px-6 text-slate-600">${createdDate}</td>
                    </tr>
                `);
                tbody.append(row);
            });
        },
        error: function() {
            Toast.error('Failed to load dashboard stats');
        }
    });
}
