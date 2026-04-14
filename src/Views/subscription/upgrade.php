<?php

use App\Config\Settings;
use App\Helpers\PricingDisplay;

$title = "Upgrade Subscription";
ob_start();

$currency = Settings::get('currency', 'GHS');
$price = Settings::get('premium_price', '350');
$priceDisplay = PricingDisplay::formatMonthly($price);
?>

<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div
                class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                S
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">
            Upgrade to Pro
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Unlock full access and remove all limits.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-slate-200">

            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-8">
                <div class="rounded-2xl bg-brand-50 p-6 border border-brand-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 rounded-full bg-brand-500/10 blur-2xl">
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 relative z-10">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pro Plan</h3>
                            <p class="text-xs text-slate-500 mt-1">Unlimited Suppliers & Evaluations</p>
                            <ul class="mt-4 space-y-2">
                                <li class="flex items-center text-xs text-slate-600">
                                    <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Advanced Reporting
                                </li>
                                <li class="flex items-center text-xs text-slate-600">
                                    <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Priority Support
                                </li>
                            </ul>
                        </div>
                        <div class="text-left sm:text-right space-y-2 shrink-0">
                            <div class="text-4xl sm:text-5xl font-black text-brand-600 tracking-tight leading-none">
                                <?= htmlspecialchars($priceDisplay['usd_per_month']) ?>
                            </div>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars($priceDisplay['ghs_billed_line']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <form id="paymentForm" class="space-y-6">
                <input type="hidden" id="email-address" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">
                <input type="hidden" id="amount" value="<?= $price ?>">
                <input type="hidden" id="currency" value="<?= $currency ?>">

                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 mb-4">
                    <p class="text-sm font-semibold text-slate-800 text-center">
                        <?= htmlspecialchars($priceDisplay['charge_notice']) ?>
                    </p>
                </div>
                <div class="pt-2">
                    <button type="button" onclick="payWithPaystack()"
                        class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-brand-500/25 text-base font-bold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform active:scale-[0.98]">
                        Upgrade Now
                    </button>
                    <p class="mt-3 text-center text-xs text-slate-500"><?= htmlspecialchars($priceDisplay['disclaimer']) ?></p>
                    <p class="mt-4 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Secure payment powered by Paystack
                    </p>
                </div>
            </form>

            <div class="mt-8 border-t border-slate-100 pt-6">
                <p class="text-center text-sm">
                    <a href="/saas/dashboard"
                        class="font-semibold text-slate-500 hover:text-brand-600 transition-colors">
                        Cancel and return to dashboard
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    const paymentForm = document.getElementById('paymentForm');

    function payWithPaystack() {
        const email = document.getElementById("email-address").value;
        if (!email || !email.includes('@')) {
            alert('A valid email address is required to start the transaction. Please log out and log in again.');
            return;
        }

        let handler = PaystackPop.setup({
            key: '<?= \App\Config\Paystack::getPublicKey() ?>',
            email: email,
            amount: document.getElementById("amount").value * 100,
            currency: document.getElementById("currency").value,
            ref: 'sub_' + Math.floor((Math.random() * 1000000000) + 1),
            metadata: {
                custom_fields: [
                    {
                        display_name: "Company ID",
                        variable_name: "company_id",
                        value: "<?= $_SESSION['company_id'] ?>"
                    }
                ]
            },
            callback: function (response) {
                // Payment successful
                window.location.href = "/saas/subscription/process?reference=" + response.reference;
            },
            onClose: function () {
                alert('Transaction was not completed, window closed.');
            }
        });
        handler.openIframe();
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>