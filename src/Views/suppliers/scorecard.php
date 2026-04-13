<?php ob_start();
$trend = $trend ?? [];
$latest_score = !empty($trend) ? (float) end($trend)['total_score'] : 0;
$status_color = 'rose';
$status_text = 'At Risk';
if ($latest_score >= 80) {
    $status_color = 'emerald';
    $status_text = 'Top Performer';
} elseif ($latest_score >= 60) {
    $status_color = 'amber';
    $status_text = 'Steady';
}
$companyAvg = (float) ($companyAvg ?? 0);
?>

<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <nav class="flex text-sm font-medium text-slate-500 mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="/saas/suppliers" class="hover:text-indigo-600 transition-colors">Suppliers</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-slate-900">Scorecard</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex flex-wrap items-center gap-3">
                <?= htmlspecialchars($supplier['name']) ?>
                <span class="px-3 py-1 bg-<?= $status_color ?>-100 text-<?= $status_color ?>-700 text-xs font-black uppercase rounded-full">
                    <?= $status_text ?>
                </span>
            </h1>
            <p class="text-sm text-slate-500 mt-2">Single-page view: latest snapshot, history, and evaluation workflow trail.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="/saas/suppliers/profile?id=<?= (int) $supplier['id'] ?>"
                class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                Performance profile
            </a>
            <?php if (in_array($_SESSION['role'] ?? '', ['company_admin', 'evaluator'], true)): ?>
                <a href="/saas/evaluations/create?supplier_id=<?= (int) $supplier['id'] ?>"
                    class="px-4 py-2.5 bg-indigo-600 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                    New evaluation
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Latest score</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format($latest_score, 1) ?></h3>
            <p class="text-xs font-bold text-slate-500 mt-2 uppercase">Last submitted evaluation</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Submitted evals</p>
            <h3 class="text-3xl font-black text-slate-900"><?= count($evaluationHistory ?? []) ?></h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">vs company avg</p>
            <h3 class="text-3xl font-black <?= $latest_score >= $companyAvg ? 'text-emerald-600' : 'text-rose-600' ?>">
                <?= $latest_score >= $companyAvg ? '+' : '−' ?><?= number_format(abs($latest_score - $companyAvg), 1) ?>
            </h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Company avg</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format($companyAvg, 1) ?></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">Latest evaluation — criteria snapshot</h3>
            <p class="text-sm text-slate-500 mb-6">Raw scores from the most recent submitted evaluation.</p>
            <div class="space-y-4">
                <?php if (empty($latestSnapshot)): ?>
                    <p class="text-sm text-slate-400">No submitted evaluations yet.</p>
                <?php else: ?>
                    <?php foreach ($latestSnapshot as $row): ?>
                        <?php $pct = ($row['max_score'] > 0) ? ((float) $row['score'] / (int) $row['max_score']) * 100 : 0; ?>
                        <div>
                            <div class="flex justify-between text-sm font-bold text-slate-800">
                                <span><?= htmlspecialchars($row['criteria_name']) ?></span>
                                <span><?= number_format((float) $row['score'], 1) ?> / <?= (int) $row['max_score'] ?></span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 rounded-full mt-2 overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: <?= min(100, $pct) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">Historical averages by criteria</h3>
            <p class="text-sm text-slate-500 mb-6">Across all submitted evaluations.</p>
            <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                <?php if (empty($breakdown)): ?>
                    <p class="text-sm text-slate-400">No data yet.</p>
                <?php else: ?>
                    <?php foreach ($breakdown as $item): ?>
                        <?php $percentage = ($item['max_score'] > 0) ? ((float) $item['avg_score'] / (int) $item['max_score']) * 100 : 0; ?>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="font-bold text-slate-700"><?= htmlspecialchars($item['criteria_name']) ?></span>
                                <span class="font-black"><?= number_format((float) $item['avg_score'], 1) ?></span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: <?= min(100, $percentage) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-black text-slate-900">Submitted evaluation history</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Evaluator</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Score</th>
                        <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($evaluationHistory ?? [] as $eval): ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-8 py-4 text-sm font-medium text-slate-700"><?= htmlspecialchars((string) $eval['created_at']) ?></td>
                            <td class="px-8 py-4 text-sm text-slate-600"><?= htmlspecialchars((string) ($eval['evaluator_name'] ?? '—')) ?></td>
                            <td class="px-8 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-800 border border-indigo-100">
                                    <?= number_format((float) ($eval['total_score'] ?? 0), 1) ?>
                                </span>
                            </td>
                            <td class="px-8 py-4 text-right">
                                <a href="/saas/evaluations/audit?id=<?= (int) $eval['id'] ?>" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">Trail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($evaluationHistory)): ?>
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-slate-400 text-sm font-medium">No submitted evaluations yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
            <h3 class="text-lg font-black text-slate-900">Workflow & audit timeline</h3>
            <p class="text-sm text-slate-500 mt-1">Draft saves, submissions, and other evaluation workflow events for this supplier.</p>
        </div>
        <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
            <?php if (empty($workflowTimeline)): ?>
                <li class="px-8 py-10 text-center text-slate-400 text-sm">No workflow events yet. Events appear when evaluations are saved or submitted.</li>
            <?php else: ?>
                <?php foreach ($workflowTimeline as $w): ?>
                    <li class="px-8 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="font-bold text-slate-900"><?= htmlspecialchars(str_replace('_', ' ', (string) $w['action'])) ?></p>
                            <p class="text-xs text-slate-500">
                                <?= htmlspecialchars((string) ($w['actor_name'] ?? 'User')) ?>
                                · Eval #<?= (int) $w['evaluation_id'] ?>
                                <?php if (!empty($w['total_score'])): ?>
                                    · score <?= number_format((float) $w['total_score'], 1) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <time class="text-xs text-slate-400 whitespace-nowrap"><?= htmlspecialchars((string) $w['created_at']) ?></time>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <?php if (!empty($trend)): ?>
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 mb-6">Score trend</h3>
            <div class="h-72 w-full">
                <canvas id="scorecardTrendChart"></canvas>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('scorecardTrendChart').getContext('2d');
                const trendData = <?= json_encode($trend) ?>;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.date),
                        datasets: [{
                            label: 'Total score',
                            data: trendData.map(d => d.total_score),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100, grid: { display: false } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
        </script>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = 'Scorecard: ' . htmlspecialchars($supplier['name']);
require __DIR__ . '/../dashboard_layout.php';
?>
