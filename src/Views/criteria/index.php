<?php ob_start(); ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Evaluation Criteria</h1>
            <p class="text-sm text-slate-600 mt-1">Define the criteria used to evaluate your suppliers</p>
        </div>
        <?php if ($_SESSION['role'] === 'company_admin'): ?>
            <a href="/saas/criteria/create"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm hover:shadow-md flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Criteria
            </a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Active Criteria</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Criteria Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Weight (%)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Max Score</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($criteria)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <p class="mt-2 text-sm font-medium">No criteria defined yet</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $totalWeight = 0;
                        foreach ($criteria as $criterion):
                            $totalWeight += $criterion['weight'];
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-slate-900">
                                    <?= htmlspecialchars($criterion['name']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 font-mono">
                                    <?= htmlspecialchars($criterion['weight']) ?>%
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?= htmlspecialchars($criterion['max_score']) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <?php if ($_SESSION['role'] === 'company_admin'): ?>
                                        <div class="flex items-center justify-end gap-2 text-slate-400">
                                            <a href="/saas/criteria/edit?id=<?= $criterion['id'] ?>"
                                                class="p-1 hover:text-indigo-600 transition-colors" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="/saas/criteria/delete" method="POST" class="inline"
                                                onsubmit="return confirm('Delete this criterion? This may affect existing evaluations.');">
                                                <input type="hidden" name="id" value="<?= $criterion['id'] ?>">
                                                <button type="submit" class="p-1 hover:text-rose-600 transition-colors"
                                                    title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Total Weight Summary -->
                        <tr class="bg-slate-50/50 border-t-2 border-slate-200">
                            <td class="px-6 py-4 text-xs font-black text-slate-500 uppercase">Total Weight</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-black <?= $totalWeight == 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800 animate-pulse' ?>">
                                    <?= $totalWeight ?>%
                                </span>
                            </td>
                            <td colspan="2"
                                class="px-6 py-4 text-xs font-bold <?= $totalWeight == 100 ? 'text-emerald-600' : 'text-amber-600' ?>">
                                <?= $totalWeight == 100 ? 'Perfectly balanced' : 'Total must equal 100% for accurate scoring' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Evaluation Criteria";
require __DIR__ . '/../dashboard_layout.php';
?>