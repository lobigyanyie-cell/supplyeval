<?php ob_start(); ?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Evaluation Criteria</h1>
            <p class="text-sm text-slate-600 mt-1">Update the weighted importance of this criterion</p>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div
            class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3 animate-headShake">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-bold">
                <?= $error ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="/saas/criteria/update" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $criteria['id'] ?>">

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Criteria Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($criteria['name']) ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>

            <div>
                <label for="weight" class="block text-sm font-medium text-slate-700 mb-2">
                    Weight (%) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="weight" id="weight" min="0" max="100" step="0.1" required
                    value="<?= htmlspecialchars($criteria['weight']) ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                <p class="mt-1 text-xs text-slate-500">How important is this criterion? Total across all MUST be 100%.
                </p>
            </div>

            <div>
                <label for="max_score" class="block text-sm font-medium text-slate-700 mb-2">
                    Maximum Score
                </label>
                <input type="number" name="max_score" id="max_score"
                    value="<?= htmlspecialchars($criteria['max_score']) ?>" readonly
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg cursor-not-allowed text-slate-600">
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors shadow-sm hover:shadow-md">
                    Update Criteria
                </button>
                <a href="/saas/criteria"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-lg font-medium transition-colors text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Edit Criteria";
require __DIR__ . '/../dashboard_layout.php';
?>