<?php

use App\Config\Settings;
use App\Helpers\PricingDisplay;

ob_start();
$billingCurrency = Settings::get('currency', 'GHS');
?>

<div class="max-w-4xl mx-auto space-y-8 py-8 px-4 sm:px-6 lg:px-8">
    <!-- Action Bar -->
    <div class="flex items-center justify-between mb-8 no-print">
        <a href="/saas/subscription/history"
            class="text-sm font-bold text-slate-500 hover:text-brand-600 flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Billing
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/25 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print Invoice
        </button>
    </div>

    <!-- Invoice Card -->
    <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-200 overflow-hidden relative"
        id="invoice-content">
        <!-- Brand Decor -->
        <div class="h-3 bg-gradient-to-r from-brand-600 to-indigo-600 w-full"></div>

        <div class="p-12 md:p-16">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between gap-12 mb-16">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white shadow-xl">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">SupplierEval</span>
                    </div>
                    <div class="space-y-1 text-slate-500 text-sm">
                        <p class="font-bold text-slate-900">SupplierEval SaaS Ghana</p>
                        <p>Accra Digital Centre</p>
                        <p>Accra, Ghana</p>
                        <p>billing@suppliereval.com</p>
                    </div>
                </div>

                <div class="text-right">
                    <h1 class="text-5xl font-black text-slate-200 mb-6 uppercase tracking-widest">Invoice</h1>
                    <div class="space-y-2">
                        <p class="text-sm font-bold text-slate-900">Invoice Number</p>
                        <p class="text-xl font-mono text-brand-600">INV-
                            <?= strtoupper(substr($transaction['transaction_id'], 0, 8)) ?>
                        </p>
                        <div class="pt-4">
                            <p class="text-sm font-bold text-slate-900">Date Issued</p>
                            <p class="text-slate-500">
                                <?= date('M d, Y', strtotime($transaction['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipient & Details -->
            <div class="grid grid-cols-2 gap-12 mb-16">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Billed To</h2>
                    <div class="space-y-1 text-slate-600">
                        <p class="font-bold text-slate-900 text-lg">
                            <?= htmlspecialchars($company['name']) ?>
                        </p>
                        <p>
                            <?= htmlspecialchars($company['email']) ?>
                        </p>
                        <!-- In a real app, you'd have company address here -->
                        <p>Default Billing Address</p>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Payment Info</h2>
                    <div class="space-y-1 text-slate-600">
                        <p class="font-bold text-slate-900">Paystack Transaction</p>
                        <p class="font-mono text-sm">
                            <?= htmlspecialchars($transaction['transaction_id']) ?>
                        </p>
                        <p class="pt-2">
                            <span
                                class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase rounded-full border border-emerald-100">
                                Paid via Card
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="border-t border-b border-slate-100 mb-12">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-6">Description</th>
                            <th class="py-6 text-center">Qty</th>
                            <th class="py-6 text-right">Price</th>
                            <th class="py-6 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr>
                            <td class="py-6">
                                <p class="font-bold text-slate-900">SupplierEval Pro Subscription</p>
                                <p class="text-sm text-slate-500">Full access to all features (30 Days)</p>
                            </td>
                            <td class="py-6 text-center text-slate-600">1</td>
                            <td class="py-6 text-right text-slate-600">
                                <?= htmlspecialchars(PricingDisplay::formatMoneyAmount($transaction['amount'], $billingCurrency)) ?>
                            </td>
                            <td class="py-6 text-right font-bold text-slate-900">
                                <?= htmlspecialchars(PricingDisplay::formatMoneyAmount($transaction['amount'], $billingCurrency)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total -->
            <div class="flex justify-end mb-16">
                <div class="w-full max-w-xs space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 uppercase font-bold tracking-tight">Subtotal</span>
                        <span class="text-slate-900 font-bold"><?= htmlspecialchars(PricingDisplay::formatMoneyAmount($transaction['amount'], $billingCurrency)) ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 uppercase font-bold tracking-tight">Tax (0%)</span>
                        <span class="text-slate-900 font-bold"><?= htmlspecialchars(PricingDisplay::formatMoneyAmount(0, $billingCurrency)) ?></span>
                    </div>
                    <div class="pt-4 border-t border-slate-200 flex justify-between items-end">
                        <span class="text-brand-600 uppercase font-black tracking-widest text-lg">Total</span>
                        <span
                            class="text-4xl font-black text-slate-900"><?= htmlspecialchars(PricingDisplay::formatMoneyAmount($transaction['amount'], $billingCurrency)) ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 rounded-2xl p-8 text-center text-slate-500 text-sm border border-slate-100">
                <p class="font-bold text-slate-700 mb-2">Thank you for your business!</p>
                <p>If you have any questions regarding this invoice, please contact our support team.</p>
                <p class="mt-4 text-[10px] uppercase tracking-widest text-slate-400">©
                    <?= date('Y') ?> SupplierEval SaaS. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white !important;
        }

        #invoice-content {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            width: 100% !important;
        }

        aside,
        header,
        nav {
            display: none !important;
        }

        main {
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            height: auto !important;
            width: 100% !important;
            position: static !important;
        }

        .max-w-7xl {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = "Invoice " . strtoupper(substr($transaction['transaction_id'], 0, 8));
require __DIR__ . '/../dashboard_layout.php';
?>