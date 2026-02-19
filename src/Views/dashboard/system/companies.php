<?php ob_start(); ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-800">All Registered Companies</h3>
            <p class="text-sm text-slate-500">Manage tenants and their subscription status.</p>
        </div>
        <div>
           <!-- Search or Filte could go here -->
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Company Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subscription</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($companies as $company): ?>
                    <?php 
                        $is_trial = $company['subscription_status'] === 'trial';
                        $trial_ended = !empty($company['trial_ends_at']) && strtotime($company['trial_ends_at']) < time();
                        $is_expired = $is_trial && $trial_ended;
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-500">#<?= $company['id'] ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-900"><?= htmlspecialchars($company['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= htmlspecialchars($company['email']) ?></td>
                        <td class="px-6 py-4">
                            <?php if ($is_expired): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                    Expired (Trial)
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize 
                                    <?= $company['subscription_status'] === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' ?>">
                                    <?= ucfirst($company['subscription_status']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($is_trial && !$trial_ended): ?>
                                <div class="text-xs text-slate-400 mt-1">
                                    Ends: <?= date('M j', strtotime($company['trial_ends_at'])) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize 
                                <?= ($company['account_status'] ?? 'active') === 'active' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                                <?= ucfirst($company['account_status'] ?? 'active') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?= date('M j, Y', strtotime($company['created_at'])) ?></td>
                        <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                            <form action="/saas/admin/companies/suspend" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to change the status of this company?');">
                                <input type="hidden" name="id" value="<?= $company['id'] ?>">
                                <?php if (($company['account_status'] ?? 'active') === 'active'): ?>
                                    <input type="hidden" name="status" value="suspended">
                                    <button type="submit" class="text-amber-600 hover:text-amber-900 hover:bg-amber-50 px-2 py-1 rounded transition-colors" title="Suspend Access">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-900 hover:bg-emerald-50 px-2 py-1 rounded transition-colors" title="Activate Access">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </form>
                            
                            <form action="/saas/admin/companies/delete" method="POST" class="inline-block ml-2" onsubmit="return confirm('WARNING: This will permanently delete the company and. Are you sure?');">
                                <input type="hidden" name="id" value="<?= $company['id'] ?>">
                                <button type="submit" class="text-rose-600 hover:text-rose-900 hover:bg-rose-50 px-2 py-1 rounded transition-colors" title="Delete Company">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Manage Companies";
require __DIR__ . '/../../dashboard_layout.php';
?>