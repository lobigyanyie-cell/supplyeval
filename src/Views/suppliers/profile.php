<?php ob_start(); ?>

<div class="space-y-8">
    <!-- Breadcrumbs & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <nav class="flex text-sm font-medium text-slate-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="/saas/suppliers" class="hover:text-indigo-600 transition-colors">Suppliers</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-slate-900">Performance Profile</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <?= htmlspecialchars($supplier['name']) ?>
                <?php
                $latest_score = !empty($trend) ? end($trend)['total_score'] : 0;
                $status_color = 'rose';
                $status_text = 'At Risk';
                if ($latest_score >= 80) {
                    $status_color = 'emerald';
                    $status_text = 'Top Performer';
                } elseif ($latest_score >= 60) {
                    $status_color = 'amber';
                    $status_text = 'Steady';
                }
                ?>
                <span
                    class="px-3 py-1 bg-<?= $status_color ?>-100 text-<?= $status_color ?>-700 text-xs font-black uppercase rounded-full">
                    <?= $status_text ?>
                </span>
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="/saas/suppliers/scorecard?id=<?= (int) $supplier['id'] ?>"
                class="px-4 py-2.5 bg-slate-900 rounded-xl text-sm font-bold text-white hover:bg-slate-800 transition-all shadow-lg flex items-center gap-2">
                Scorecard
            </a>
            <a href="/saas/evaluations/create?supplier_id=<?= $supplier['id'] ?>"
                class="px-4 py-2.5 bg-indigo-600 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Evaluation
            </a>
            <a href="/saas/suppliers/edit?id=<?= $supplier['id'] ?>"
                class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm flex items-center gap-2">
                Edit Details
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Latest Score</p>
            <div class="flex items-end gap-2">
                <h3 class="text-3xl font-black text-slate-900">
                    <?= number_format($latest_score, 1) ?>
                </h3>
                <span class="text-slate-400 font-bold mb-1">/100</span>
            </div>
            <div
                class="mt-2 flex items-center gap-1.5 <?= $latest_score >= $companyAvg ? 'text-emerald-600' : 'text-rose-600' ?> font-bold text-xs uppercase tracking-tight">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="<?= $latest_score >= $companyAvg ? 'M5 10l7-7 7 7M12 3v18' : 'M19 14l-7 7-7-7M12 21V3' ?>" />
                </svg>
                <?= number_format(abs($latest_score - $companyAvg), 1) ?> pts vs company avg
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Evaluations</p>
            <h3 class="text-3xl font-black text-slate-900">
                <?= count($recentEvaluations) ?>
            </h3>
            <p class="text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Lifetime conducted</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Main Contact</p>
            <h3 class="text-lg font-bold text-slate-900 truncate">
                <?= htmlspecialchars($supplier['contact_person']) ?>
            </h3>
            <p class="text-xs font-medium text-slate-500 mt-1 truncate">
                <?= htmlspecialchars($supplier['email']) ?>
            </p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-md">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Company Avg</p>
            <h3 class="text-3xl font-black text-slate-900">
                <?= number_format($companyAvg, 1) ?>
            </h3>
            <p class="text-xs font-bold text-slate-500 mt-2 uppercase tracking-tight">Global Benchmark</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Performance Trend -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Performance Trend</h3>
                    <p class="text-sm font-medium text-slate-500">Score history over time</p>
                </div>
                <div class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-600 uppercase">Interactive
                </div>
            </div>
            <div class="h-80 w-full">
                <canvas id="supplierTrendChart"></canvas>
            </div>
        </div>

        <!-- Criteria Breakdown -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">Category Scores</h3>
            <p class="text-sm font-medium text-slate-500 mb-8">Average performance per criteria</p>

            <div class="space-y-6">
                <?php foreach ($breakdown as $item): ?>
                    <?php $percentage = ($item['avg_score'] / $item['max_score']) * 100; ?>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-bold text-slate-700"><?= htmlspecialchars($item['criteria_name']) ?></span>
                            <span class="font-black text-slate-900"><?= number_format($item['avg_score'], 1) ?></span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <?php
                            $bar_color = 'indigo';
                            if ($percentage < 50) $bar_color = 'rose';
                            elseif ($percentage < 75) $bar_color = 'amber';
                            else $bar_color = 'emerald';
                            ?>
                            <div class="h-full bg-<?= $bar_color ?>-500 rounded-full transition-all duration-1000" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] uppercase font-black tracking-widest text-slate-400">
                            <span>Weight: <?= $item['weight'] ?>%</span>
                            <span>Target: 100%</span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($breakdown)): ?>
                    <div class="py-12 text-center">
                        <div
                            class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">No evaluation data available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Evaluation History -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-black text-slate-900">Recent Evaluations</h3>
            <a href="/saas/suppliers/scorecard?id=<?= (int) $supplier['id'] ?>"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">Open scorecard</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Evaluator</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Score</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Comments</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($recentEvaluations as $eval): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5 text-sm font-bold text-slate-900">
                                <?= date('M d, Y', strtotime($eval['created_at'])) ?>
                            </td>
                            <td class="px-8 py-5 text-sm font-medium text-slate-600">
                                <?= htmlspecialchars($eval['evaluator_name'] ?? 'Unknown') ?>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2.5 py-1 bg-slate-900 text-white text-xs font-black rounded-lg">
                                    <?= number_format($eval['total_score'], 1) ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-500 italic">
                                "
                                <?= htmlspecialchars(substr((string) ($eval['comments'] ?? ''), 0, 100)) ?>
                                <?= strlen((string) ($eval['comments'] ?? '')) > 100 ? '...' : '' ?>"
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="/saas/evaluations/audit?id=<?= (int) $eval['id'] ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Trail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($recentEvaluations)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-medium">No evaluations found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('supplierTrendChart').getContext('2d');

        const trendData = <?= json_encode($trend) ?>;
        const labels = trendData.map(d => d.date);
        const scores = trendData.map(d => d.total_score);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Score',
                    data: scores,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                }
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
$title = "Performance Profile: " . htmlspecialchars($supplier['name']);
require __DIR__ . '/../dashboard_layout.php';
?>