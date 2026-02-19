<?php ob_start(); ?>

<div class="space-y-8 pb-12">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">System Overview</h1>
            <p class="text-slate-500 font-medium">Monitoring platform performance and global health.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Report
            </button>
            <a href="/saas/admin/companies"
                class="px-4 py-2.5 bg-indigo-600 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2 text-center">
                Manage Tenants
            </a>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                    <path d="M12 2.252A8.001 8.001 0 0117.748 8H12V2.252z" />
                </svg>
            </div>
            <?php $currency = \App\Config\Settings::get('currency', 'GHS'); ?>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Live Revenue</p>
            <h3 class="text-3xl font-black text-slate-900 mb-2">₵<?= number_format($total_revenue, 2) ?></h3>
            <div
                class="flex items-center gap-1.5 <?= $growth >= 0 ? 'text-emerald-600' : 'text-rose-600' ?> font-bold text-xs bg-slate-50 px-2.5 py-1 rounded-full w-fit">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="<?= $growth >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6' ?>" />
                </svg>
                <?= number_format(abs($growth), 1) ?>% vs last month
            </div>
        </div>

        <!-- Tenant Card -->
        <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Active Tenants</p>
            <h3 class="text-3xl font-black text-slate-900 mb-2"><?= $total_companies ?></h3>
            <div
                class="text-slate-500 font-bold text-xs bg-slate-50 px-2.5 py-1 rounded-full w-fit flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Scalable Infrastructure
            </div>
        </div>

        <!-- User Card -->
        <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Global Users</p>
            <h3 class="text-3xl font-black text-slate-900 mb-2"><?= $total_users ?></h3>
            <div
                class="text-slate-500 font-bold text-xs bg-slate-50 px-2.5 py-1 rounded-full w-fit flex items-center gap-1 text-emerald-600">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Active Sessions
            </div>
        </div>

        <!-- System Status -->
        <div
            class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl transition-all duration-500">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">System Health</p>
            <h3 class="text-3xl font-black text-slate-900 mb-2">99.9%</h3>
            <div
                class="text-slate-500 font-bold text-xs bg-slate-50 px-2.5 py-1 rounded-full w-fit flex items-center gap-1 text-emerald-600">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Operational
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Chart -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Revenue Trends</h3>
                <select class="text-xs font-bold border-none bg-slate-50 rounded-lg px-3 py-1.5 focus:ring-0">
                    <option>Last 6 Months</option>
                    <option>Year to Date</option>
                </select>
            </div>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Distribution Chart -->
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Tenant Distribution</h3>
            <div class="h-64 relative">
                <canvas id="tenantChart"></canvas>
            </div>
            <div class="mt-8 space-y-3">
                <?php foreach ($tenant_stats as $stat): ?>
                    <div class="flex items-center justify-between text-sm font-bold">
                        <span class="text-slate-500 flex items-center gap-2">
                            <span
                                class="w-3 h-3 rounded-full <?= $stat['subscription_status'] === 'active' ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
                            <?= ucfirst($stat['subscription_status']) ?>
                        </span>
                        <span class="text-slate-900"><?= $stat['count'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Bottom Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activities (Transactions) -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900">Live Transaction Feed</h3>
                <span
                    class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase rounded-full tracking-widest">Real-time</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                            <th class="px-8 py-4">Reference</th>
                            <th class="px-8 py-4">Company</th>
                            <th class="px-8 py-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($recent_transactions as $tx): ?>
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-8 py-6">
                                    <p class="font-mono text-[10px] text-slate-400 mb-1">#<?= $tx['id'] ?></p>
                                    <p class="text-xs font-bold text-slate-700">
                                        <?= substr($tx['transaction_id'], 0, 15) ?>...
                                    </p>
                                </td>
                                <td class="px-8 py-6 font-black text-slate-900 text-sm">
                                    <?= htmlspecialchars($tx['company_name']) ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span
                                        class="text-sm font-black text-emerald-600">₵<?= number_format($tx['amount'], 2) ?></span>
                                    <p class="text-[10px] text-slate-400">
                                        <?= date('M j, H:i', strtotime($tx['created_at'])) ?>
                                    </p>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Companies -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900 text-center">New Tenant Onboarding</h3>
                <a href="/saas/admin/companies"
                    class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">View
                    Directory</a>
            </div>
            <div class="px-8 py-6 space-y-6">
                <?php foreach ($recent_companies as $company): ?>
                    <div class="flex items-center justify-between group cursor-default">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-900 font-black text-lg group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <?= substr($company['name'], 0, 1) ?>
                            </div>
                            <div>
                                <p class="font-black text-slate-900"><?= htmlspecialchars($company['name']) ?></p>
                                <p class="text-xs text-slate-500 font-bold"><?= htmlspecialchars($company['email']) ?></p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight 
                        <?= $company['subscription_status'] === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?>">
                            <?= $company['subscription_status'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Trends Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueLabels = <?= json_encode(array_column($revenue_trends, 'month')) ?>;
    const revenueData = <?= json_encode(array_column($revenue_trends, 'total')) ?>;

    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: revenueLabels.length > 0 ? revenueLabels : ['No Data'],
            datasets: [{
                label: 'Revenue (₵)',
                data: revenueData.length > 0 ? revenueData : [0],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false }, ticks: { font: { weight: 'bold' } } },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
            }
        }
    });

    // Tenant Distribution Chart
    const tenCtx = document.getElementById('tenantChart').getContext('2d');
    const tenantLabels = <?= json_encode(array_column($tenant_stats, 'subscription_status')) ?>;
    const tenantData = <?= json_encode(array_column($tenant_stats, 'count')) ?>;

    new Chart(tenCtx, {
        type: 'doughnut',
        data: {
            labels: tenantLabels,
            datasets: [{
                data: tenantData,
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
</script>

<?php
$content = ob_get_clean();
$title = "Advanced System Control";
require __DIR__ . '/../dashboard_layout.php';
?>