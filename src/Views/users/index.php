<?php ob_start();
$invite_ttl_hours = $invite_ttl_hours ?? 48;
?>

<?php if (!empty($_GET['invited'])): ?>
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        Invite sent to <span class="font-semibold"><?= htmlspecialchars((string) $_GET['invited']) ?></span>.
    </div>
<?php endif; ?>

<?php if (!empty($_GET['resent'])): ?>
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        Invite resent to <span class="font-semibold"><?= htmlspecialchars((string) $_GET['resent']) ?></span>.
    </div>
<?php endif; ?>

<?php if (($_GET['resend'] ?? '') === 'invalid'): ?>
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Could not resend — user not found or already active.
    </div>
<?php endif; ?>

<?php if (($_GET['resend'] ?? '') === 'error'): ?>
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        Could not resend invite (database unavailable).
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Team Members</h2>
            <p class="text-sm text-slate-500">Manage users who have access to your company account.</p>
            <p class="text-xs text-slate-400 mt-1">Invite links expire after <?= (int) $invite_ttl_hours ?> hours.</p>
        </div>
        <a href="/saas/users/create"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Team Member
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            No users found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <?= htmlspecialchars((string) $user['name']) ?>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <span class="ml-2 text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">(You)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars((string) $user['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                    <?= $user['role'] === 'company_admin' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-800' ?>">
                                    <?= str_replace('_', ' ', (string) $user['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs">
                                <?php if (!empty($user['invite_pending'])): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-200">Pending</span>
                                    <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars((string) ($user['invite_detail'] ?? '')) ?></p>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-100">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <?= date('M j, Y', strtotime((string) $user['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <?php if ($user['id'] != $_SESSION['user_id'] && !empty($user['invite_pending']) && !empty($user['invite_can_resend'])): ?>
                                    <form action="/saas/users/resend-invite" method="POST" class="inline-block">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit"
                                            class="text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 px-3 py-1 rounded-md transition-colors">Resend invite</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form action="/saas/users/delete" method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to remove this user?');">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-900 hover:bg-rose-50 px-3 py-1 rounded-md transition-colors">Remove</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Team Management";
require __DIR__ . '/../dashboard_layout.php';
?>
