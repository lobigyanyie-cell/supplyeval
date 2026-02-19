<?php ob_start(); ?>

<div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
    <div
        class="px-8 py-6 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Global Transaction History</h3>
            <p class="text-sm text-slate-500 font-medium">Monitoring all subscription payments across the platform.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2 flex items-center gap-2 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">Live Updates</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50">
                    <th
                        class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">
                        Reference ID</th>
                    <th
                        class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">
                        Customer / Company</th>
                    <th
                        class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">
                        Revenue</th>
                    <th
                        class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">
                        Status</th>
                    <th
                        class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">
                        Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 bg-white">
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-3 opacity-30">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="text-lg font-bold">No transactions found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr class="hover:bg-slate-50/80 transition-all duration-300 group">
                            <td class="px-8 py-5">
                                <span class="font-mono text-[11px] text-slate-400 block mb-1">TX_
                                    <?= $tx['id'] ?>
                                </span>
                                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-md tracking-tight">
                                    <?= htmlspecialchars($tx['transaction_id']) ?>
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                        <?= substr($tx['company_name'], 0, 1) ?>
                                    </div>
                                    <span class="text-sm font-black text-slate-900">
                                        <?= htmlspecialchars($tx['company_name']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-black text-emerald-600">₵<?= number_format($tx['amount'], 2) ?></span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Net
                                        Revenue</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight
                                    <?= $tx['status'] === 'succeeded' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' ?>">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full <?= $tx['status'] === 'succeeded' ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
                                    <?= $tx['status'] ?>
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-xs font-bold text-slate-600">
                                    <?= date('M j, Y', strtotime($tx['created_at'])) ?>
                                </div>
                                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">
                                    <?= date('H:i:s', strtotime($tx['created_at'])) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Footer -->
    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Showing last
            <?= count($transactions) ?> platform payments
        </p>
        <div class="flex gap-2">
            <button
                class="px-3 py-1.5 text-[10px] font-black text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all uppercase tracking-widest">Previous</button>
            <button
                class="px-3 py-1.5 text-[10px] font-black text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all uppercase tracking-widest">Next</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Financial Control";
require __DIR__ . '/../../dashboard_layout.php';
?>