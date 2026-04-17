<?php

use App\Config\Settings;
use App\Helpers\PricingDisplay;

ob_start();
$billingCurrency = Settings::get('currency', 'GHS');
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Billing History</h1>
            <p class="text-sm text-slate-600 mt-1">Manage your subscriptions and download invoices</p>
        </div>
        <div class="flex items-center gap-3">
            <span
                class="px-3 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-full border border-brand-100 uppercase tracking-wider">
                Current Plan: Pro
            </span>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500 mb-1">Total Spent</p>
            <h3 class="text-2xl font-bold text-slate-900">
                <?= htmlspecialchars(PricingDisplay::formatMoneyAmount(array_sum(array_column($transactions, 'amount')), $billingCurrency)) ?></h3>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500 mb-1">Last Payment Date</p>
            <h3 class="text-2xl font-bold text-slate-900">
                <?= !empty($transactions) ? date('M d, Y', strtotime($transactions[0]['created_at'])) : 'N/A' ?>
            </h3>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500 mb-1">Billing Cycle</p>
            <h3 class="text-2xl font-bold text-slate-900">Monthly</h3>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Past Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50/50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4">Transaction ID</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="text-lg">No transactions found</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs text-slate-500">
                                        <?= htmlspecialchars($tx['transaction_id']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?= date('M d, Y', strtotime($tx['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900">
                                    <?= htmlspecialchars(PricingDisplay::formatMoneyAmount($tx['amount'], $billingCurrency)) ?>
                                </td>
                                <td class="px-6 py-4 italic">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight 
                                    <?= $tx['status'] === 'succeeded' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' ?>">
                                        <?= $tx['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="/saas/subscription/invoice?id=<?= $tx['id'] ?>"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-brand-600 hover:text-white border border-brand-100 hover:bg-brand-600 rounded-lg transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-indigo-900 rounded-2xl p-8 relative overflow-hidden text-white">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-brand-500/20 blur-[80px]"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-xl font-bold mb-2">Need help with your billing?</h3>
                <p class="text-indigo-200 text-sm max-w-md">Our support team is available 24/7 to assist you with any
                    questions about your subscription or invoices.</p>
            </div>
            <a href="mailto:support@suppliereval.com"
                class="px-6 py-3 bg-white text-indigo-900 font-bold rounded-xl hover:bg-brand-50 transition-all transform hover:-translate-y-1 shadow-xl">
                Contact Billing Support
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Billing History";
require __DIR__ . '/../dashboard_layout.php';
?>