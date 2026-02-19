<?php ob_start(); ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">New Evaluation</h1>
        <p class="text-slate-500 mt-1">Evaluating <span class="font-semibold text-indigo-600"><?= htmlspecialchars($supplier['name']) ?></span></p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8">
            <form action="/saas/evaluations/store" method="POST" class="space-y-8">
                <input type="hidden" name="supplier_id" value="<?= $supplier['id'] ?>">

                <?php if (empty($criteria)): ?>
                    <div class="text-center py-12 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-slate-600 font-medium">No evaluation criteria defined.</p>
                        <a href="/saas/criteria" class="text-indigo-600 hover:text-indigo-800 font-semibold mt-2 inline-block">Add criteria first &rarr;</a>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-100 pb-2">Score Criteria</h3>
                        <?php foreach ($criteria as $criterion): ?>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div>
                                        <label for="criteria_<?= $criterion['id'] ?>" class="block font-semibold text-slate-800">
                                            <?= htmlspecialchars($criterion['name']) ?>
                                        </label>
                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">
                                            Weight: <?= $criterion['weight'] ?>%
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="scores[<?= $criterion['id'] ?>]"
                                            id="criteria_<?= $criterion['id'] ?>" min="0"
                                            max="<?= $criterion['max_score'] ?>" required
                                            class="w-24 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 font-semibold text-center text-slate-900"
                                            placeholder="0-<?= $criterion['max_score'] ?>">
                                        <span class="text-slate-500 font-medium">/ <?= $criterion['max_score'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div>
                        <label for="comments" class="block text-lg font-semibold text-slate-800 mb-2">Comments & Notes</label>
                        <textarea name="comments" id="comments" rows="4"
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                            placeholder="Add any additional context, observations, or feedback for this evaluation..."></textarea>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="/saas/suppliers"
                            class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5">
                            Submit Evaluation
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Evaluation";
require __DIR__ . '/../dashboard_layout.php';
?>