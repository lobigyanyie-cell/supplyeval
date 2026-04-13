<?php ob_start(); ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Suppliers</h2>
            <p class="text-sm text-slate-500">Manage your supplier relationships and evaluations.</p>
        </div>
        <div class="flex gap-3">
            <a href="/saas/suppliers/rankings"
                class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                View Rankings
            </a>
            <a href="/saas/suppliers/export"
                class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export CSV
            </a>
            <div class="flex items-center gap-3">
                <a href="/saas/suppliers/import"
                    class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-3 rounded-xl font-black uppercase tracking-widest text-xs flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                    </svg>
                    Bulk Import
                </a>
                <a href="/saas/suppliers/create"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-black uppercase tracking-widest text-xs flex items-center gap-2 shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5 active:translate-y-0 text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Supplier
                </a>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Avg
                        Score</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <p class="text-base font-medium text-slate-900">No suppliers found</p>
                                <p class="text-sm mt-1">Get started by adding your first supplier.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <a href="/saas/suppliers/profile?id=<?= $supplier['id'] ?>"
                                    class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    <?= htmlspecialchars($supplier['name']) ?>
                                </a>
                                <?php if ($supplier['days_since_eval'] >= $supplier['reevaluation_days']): ?>
                                    <span
                                        class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-black uppercase rounded-md border border-amber-200 animate-pulse">
                                        Due Soon
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                <?php if (isset($supplier['avg_score'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= $supplier['avg_score'] >= 80 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
                                            ($supplier['avg_score'] >= 50 ? 'bg-amber-100 text-amber-800 border border-amber-200' :
                                                'bg-rose-100 text-rose-800 border border-rose-200') ?>">
                                        <?= number_format($supplier['avg_score'], 1) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs italic">Not evaluated</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars($supplier['contact_person']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars($supplier['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?= htmlspecialchars($supplier['phone']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3 text-slate-400">
                                    <a href="/saas/suppliers/scorecard?id=<?= (int) $supplier['id'] ?>"
                                        class="text-slate-700 hover:text-slate-900 font-bold" title="Scorecard">
                                        Scorecard
                                    </a>
                                    <a href="/saas/evaluations/create?supplier_id=<?= $supplier['id'] ?>"
                                        class="text-indigo-600 hover:text-indigo-900 font-bold" title="Evaluate">
                                        Evaluate
                                    </a>

                                    <?php if ($_SESSION['role'] === 'company_admin'): ?>
                                        <a href="/saas/suppliers/edit?id=<?= $supplier['id'] ?>"
                                            class="hover:text-amber-600 transition-colors" title="Edit Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <form action="/saas/suppliers/delete" method="POST" class="inline"
                                            onsubmit="return confirm('WARNING: Deleting this supplier will also delete ALL their evaluation history. This cannot be undone. Proceed?');">
                                            <input type="hidden" name="id" value="<?= $supplier['id'] ?>">
                                            <button type="submit" class="hover:text-rose-600 transition-colors"
                                                title="Delete Supplier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
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
$title = "Suppliers";
require __DIR__ . '/../dashboard_layout.php';
?>