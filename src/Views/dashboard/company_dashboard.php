<?php ob_start(); ?>

<div class="space-y-6">
    <?php if (!empty($overdue_suppliers)): ?>
        <div class="bg-indigo-950 border border-slate-700/50 rounded-3xl p-1 shadow-2xl relative overflow-hidden group shadow-indigo-500/20"
            id="critical-reminders">
            <!-- Dynamic Background Effects -->
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 to-transparent"></div>
            <div
                class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-32 -mt-32 blur-[100px] animate-pulse-slow">
            </div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-rose-500/5 rounded-full -ml-32 -mb-32 blur-[80px]"></div>

            <div
                class="relative z-10 flex flex-col md:flex-row items-stretch gap-0 bg-slate-900/40 rounded-[1.4rem] backdrop-blur-xl border border-white/5">
                <!-- Left Icon Wing -->
                <div
                    class="flex items-center justify-center p-6 bg-white/5 border-b md:border-b-0 md:border-r border-white/5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-indigo-500/10 animate-pulse-slow"></div>
                    <div class="relative">
                        <div class="absolute inset-0 bg-indigo-500/40 blur-xl animate-ping opacity-50"></div>
                        <div class="p-4 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-500/40 relative z-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Main Content Body -->
                <div class="flex-1 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight flex items-center gap-3">
                                System Alerts Center
                                <span
                                    class="px-2 py-0.5 bg-rose-500 text-[10px] font-black uppercase text-white rounded-md animate-bounce">Live</span>
                            </h3>
                            <p class="text-indigo-200/60 text-sm font-medium mt-1">
                                Found <span
                                    class="text-indigo-400 font-black text-base drop-shadow-[0_0_8px_rgba(129,140,248,0.5)]"><?= count($overdue_suppliers) ?></span>
                                high-priority risk markers requiring intervention.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2.5">
                            <?php foreach (array_slice($overdue_suppliers, 0, 2) as $s): ?>
                                <?php
                                $is_low_score = isset($s['latest_score']) && $s['latest_score'] < 50;
                                $label = $is_low_score ? 'Critical Performance' : 'Reevaluation Due';
                                $color = $is_low_score ? 'bg-rose-500/10 text-rose-300 border-rose-500/30' : 'bg-amber-500/10 text-amber-300 border-amber-500/30';
                                $icon = $is_low_score ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' : 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z';
                                ?>
                                <div
                                    class="flex items-center gap-2 px-3 py-1.5 <?= $color ?> text-[10px] font-black uppercase tracking-widest rounded-xl border backdrop-blur-md hover:scale-105 transition-transform cursor-default">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="<?= $icon ?>" />
                                    </svg>
                                    <?= $label ?>: <?= htmlspecialchars($s['name']) ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($overdue_suppliers) > 2): ?>
                                <span
                                    class="px-3 py-1.5 bg-white/5 text-white/50 text-[10px] font-black uppercase rounded-xl border border-white/5 self-center">+
                                    <?= count($overdue_suppliers) - 2 ?> more detections</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="/saas/suppliers"
                            class="group relative inline-flex items-center gap-3 px-8 py-4 bg-white text-indigo-950 rounded-2xl font-black text-sm transition-all hover:shadow-[0_0_30px_rgba(255,255,255,0.3)] hover:-translate-y-1">
                            Take Action
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Suppliers Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Suppliers</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1"><?= $total_suppliers ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="/saas/suppliers"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group">
                    View list <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Evaluations Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Evaluations Conducted</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1"><?= $total_evaluations ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="/saas/suppliers/rankings"
                    class="text-sm font-medium text-emerald-600 hover:text-emerald-800 flex items-center gap-1 group">
                    View reports <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>
        </div>

        <!-- Avg Score Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Average Supplier Score</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">
                        <?= $avg_score ? number_format($avg_score, 1) : '-' ?><span
                            class="text-lg text-slate-400 font-normal">/100</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm font-medium text-slate-500">Across all evaluations</span>
            </div>
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Performance Trend (6M)
            </h3>
            <div class="h-[250px] relative">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Supplier Tier Distribution
            </h3>
            <div class="h-[250px] relative">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-lg lg:col-span-1">
            <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="/saas/suppliers/create"
                    class="block w-full bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 transition-all hover:translate-x-1 group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Add Supplier</p>
                            <p class="text-xs text-indigo-200">Register a new partner</p>
                        </div>
                    </div>
                </a>

                <a href="/saas/suppliers"
                    class="block w-full bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 transition-all hover:translate-x-1 group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-500 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Start Evaluation</p>
                            <p class="text-xs text-emerald-200">Assess supplier performance</p>
                        </div>
                    </div>
                </a>

                <?php if ($role === 'company_admin'): ?>
                    <a href="/saas/users/create"
                        class="block w-full bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl p-4 transition-all hover:translate-x-1 group">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-500 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold">Invite User</p>
                                <p class="text-xs text-purple-200">Grow your team</p>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-2">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Recent Evaluations</h3>
                <a href="/saas/suppliers/rankings"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php if (empty($recent_evaluations)): ?>
                    <div class="p-12 text-center text-slate-500">
                        No recent activity found.
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_evaluations as $eval): ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-600">
                                    <?= substr($eval['supplier_name'], 0, 1) ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800"><?= htmlspecialchars($eval['supplier_name']) ?></p>
                                    <p class="text-xs text-slate-500">Evaluated by
                                        <?= htmlspecialchars($eval['evaluator_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                    <?= $eval['total_score'] >= 80 ? 'bg-emerald-100 text-emerald-800' :
                                        ($eval['total_score'] >= 50 ? 'bg-amber-100 text-amber-800' :
                                            'bg-rose-100 text-rose-800') ?>">
                                    <?= number_format($eval['total_score'], 1) ?>
                                </span>
                                <p class="text-xs text-slate-400 mt-1"><?= date('M j', strtotime($eval['created_at'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Performance Trend Chart
        const perfCtx = document.getElementById('performanceChart').getContext('2d');
        const perfData = <?= json_encode($performance_trends) ?>;

        new Chart(perfCtx, {
            type: 'line',
            data: {
                labels: perfData.map(d => d.month),
                datasets: [{
                    label: 'Avg Score',
                    data: perfData.map(d => d.avg),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { font: { weight: '600' }, color: '#94a3b8' }
                    },
                    x: { grid: { display: false }, ticks: { font: { weight: '600' }, color: '#94a3b8' } }
                }
            }
        });

        // 2. Distribution Chart
        const distCtx = document.getElementById('distributionChart').getContext('2d');
        const distData = <?= json_encode($distribution) ?>;

        const colors = {
            'Excellent': '#10b981',
            'Good': '#f59e0b',
            'At Risk': '#ef4444'
        };

        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: distData.map(d => d.tier),
                datasets: [{
                    data: distData.map(d => d.count),
                    backgroundColor: distData.map(d => colors[d.tier] || '#94a3b8'),
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { weight: '600', size: 11 } } }
                }
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
$title = "Dashboard";
require __DIR__ . '/../dashboard_layout.php';
?>