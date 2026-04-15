<?php ob_start();
$user_slots_remaining = $user_slots_remaining ?? null;
$plan_max_users = $plan_max_users ?? null;
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Add Team Member</h1>
            <p class="text-sm text-slate-600 mt-1">Invite a new user to your team</p>
        </div>
        <a href="/saas/users" class="text-sm text-brand-600 hover:text-brand-700 font-medium">
            ← Back to Team
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($plan_max_users !== null): ?>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
            Your plan allows up to <?= (int) $plan_max_users ?> team member<?= (int) $plan_max_users === 1 ? '' : 's' ?>.
            <?php if ($user_slots_remaining !== null && $user_slots_remaining <= 0): ?>
                <span class="font-semibold text-rose-700">You cannot add more users without upgrading.</span>
            <?php elseif ($user_slots_remaining !== null): ?>
                <span class="text-slate-600">You have <?= (int) $user_slots_remaining ?> slot<?= $user_slots_remaining === 1 ? '' : 's' ?> left.</span>
            <?php endif; ?>
            <a href="/saas/subscription/upgrade" class="font-semibold text-brand-700 hover:text-brand-800 ml-1">Upgrade</a>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
            <h3 class="text-lg font-bold text-slate-800">User Information</h3>
            <p class="text-sm text-slate-600 mt-1">Fill in the details for the new team member</p>
        </div>

        <form action="/saas/users/store" method="POST" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        placeholder="John Doe">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        placeholder="john@example.com">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Password Setup
                    </label>
                    <div class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-sm text-slate-600">
                        The team member will receive an email link to set their own password.
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-slate-700 mb-2">
                        Role <span class="text-rose-500">*</span>
                    </label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors bg-white">
                        <option value="">Select a role</option>
                        <option value="company_admin">Company Admin</option>
                        <option value="evaluator">Evaluator</option>
                        <option value="viewer">Viewer</option>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">
                        <strong>Admin:</strong> Full access |
                        <strong>Evaluator:</strong> Can evaluate |
                        <strong>Viewer:</strong> Read-only
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                <h4 class="text-sm font-semibold text-slate-700 mb-2">Role Permissions</h4>
                <ul class="text-xs text-slate-600 space-y-1">
                    <li><strong>Company Admin:</strong> Manage team, suppliers, criteria, and evaluations</li>
                    <li><strong>Evaluator:</strong> Create and submit supplier evaluations</li>
                    <li><strong>Viewer:</strong> View suppliers and evaluation results only</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="/saas/users"
                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 font-medium transition-colors shadow-sm hover:shadow-md">
                    Add Team Member
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Add Team Member";
require __DIR__ . '/../dashboard_layout.php';
?>