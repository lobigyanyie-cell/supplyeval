<?php ob_start(); ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
        <h3 class="text-lg font-bold text-slate-800">All System Users</h3>
        <p class="text-sm text-slate-500">Global user list across all companies.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Company</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-500">#<?= $user['id'] ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-900"><?= htmlspecialchars($user['name']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($user['company_name'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 capitalize">
                                <?= str_replace('_', ' ', $user['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= date('M j, Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                            <?php if ($user['role'] !== 'system_admin'): ?>
                                <form action="/saas/admin/users/delete" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit"
                                        class="text-rose-600 hover:text-rose-900 hover:bg-rose-50 px-2 py-1 rounded transition-colors">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Manage Users";
require __DIR__ . '/../../dashboard_layout.php';
?>