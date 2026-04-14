<?php ob_start();
$criteria_slots_remaining = $criteria_slots_remaining ?? null;
$plan_max_criteria = $plan_max_criteria ?? null;
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Add Evaluation Criteria</h1>
            <p class="text-sm text-slate-600 mt-1">Define a new criterion for supplier evaluation</p>
        </div>
    </div>

    <?php if ($plan_max_criteria !== null): ?>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
            Starter plan: up to <?= (int) $plan_max_criteria ?> criteria.
            <?php if ($criteria_slots_remaining !== null): ?>
                <span class="text-slate-600"><?= (int) $criteria_slots_remaining ?> remaining.</span>
            <?php endif; ?>
            <a href="/saas/subscription/upgrade" class="font-semibold text-brand-700 hover:text-brand-800 ml-1">Upgrade for unlimited</a>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div
            class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3 animate-headShake">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-bold"><?= $error ?></p>
        </div>
    <?php endif; ?>

    <?php $old = $old ?? []; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="/saas/criteria/store" method="POST" class="space-y-6">

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                    Criteria Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                    value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="e.g., Quality, Delivery Time, Price Competitiveness">
                <p class="mt-1 text-xs text-slate-500">Each name must be unique for your company (spaces and letter case are ignored for comparison).</p>
            </div>

            <div>
                <label for="weight" class="block text-sm font-medium text-slate-700 mb-2">
                    Weight (%) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="weight" id="weight" min="0" max="100" step="0.1" required
                    value="<?= htmlspecialchars($old['weight'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="20">
                <p class="mt-1 text-xs text-slate-500">How important is this criterion? (All weights should sum to 100%)
                </p>
            </div>

            <div>
                <label for="max_score" class="block text-sm font-medium text-slate-700 mb-2">
                    Maximum Score
                </label>
                <input type="number" name="max_score" id="max_score" value="10" readonly
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg cursor-not-allowed text-slate-600">
                <p class="mt-1 text-xs text-slate-500">Fixed at 10 for consistency across all criteria</p>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors shadow-sm hover:shadow-md">
                    Add Criteria
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
$title = "Add Criteria";
require __DIR__ . '/../dashboard_layout.php';
?>