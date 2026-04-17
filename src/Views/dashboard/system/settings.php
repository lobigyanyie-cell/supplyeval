<?php ob_start(); ?>

<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Platform Settings</h1>
        <p class="text-slate-500 font-medium">Configure global system parameters and business rules.</p>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div
            class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-bold">Settings saved successfully!</span>
        </div>
    <?php endif; ?>
    <?php if (($_GET['test'] ?? '') === 'sent'): ?>
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl">
            <span class="font-bold">Test email sent to <?= htmlspecialchars($_GET['to'] ?? '') ?>.</span>
        </div>
    <?php elseif (($_GET['test'] ?? '') === 'failed'): ?>
        <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl">
            <span class="font-bold">Test email failed. Check your Brevo/SendGrid key and verified sender.</span>
            <?php if (!empty($_GET['reason'])): ?>
                <p class="mt-2 text-sm font-medium break-words"><?= htmlspecialchars($_GET['reason']) ?></p>
            <?php endif; ?>
        </div>
    <?php elseif (($_GET['test'] ?? '') === 'invalid_email'): ?>
        <div class="bg-amber-50 border border-amber-100 text-amber-800 px-6 py-4 rounded-2xl">
            <span class="font-bold">Please enter a valid test recipient email.</span>
        </div>
    <?php endif; ?>

    <form action="/saas/admin/settings/save" method="POST" class="space-y-6">
        <!-- Branding Section -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Branding & Identity</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Global platform aesthetics</p>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Platform Name</label>
                        <input type="text" name="site_name"
                            value="<?= htmlspecialchars($settings['site_name'] ?? 'SupplierEval') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Support Email</label>
                        <input type="email" name="support_email"
                            value="<?= htmlspecialchars($settings['support_email'] ?? '') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Billing & Monetization</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Pricing and subscription rules
                    </p>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Currency Code</label>
                        <select name="currency"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                            <option value="GHS" <?= ($settings['currency'] ?? '') === 'GHS' ? 'selected' : '' ?>>GHS
                                (Ghanaian Cedi)</option>
                            <option value="USD" <?= ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD (US
                                Dollar)</option>
                            <option value="NGN" <?= ($settings['currency'] ?? '') === 'NGN' ? 'selected' : '' ?>>NGN
                                (Nigerian Naira)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Premium Price</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">
                                <?= $settings['currency'] ?? 'GHS' ?>
                            </span>
                            <input type="number" name="premium_price"
                                value="<?= htmlspecialchars($settings['premium_price'] ?? '350') ?>"
                                class="w-full pl-16 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-black text-indigo-600">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Trial Days</label>
                        <input type="number" name="trial_days"
                            value="<?= htmlspecialchars($settings['trial_days'] ?? '14') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">GHS per 1 USD</label>
                        <input type="number" name="ghs_per_usd" step="0.01" min="0.01"
                            value="<?= htmlspecialchars($settings['ghs_per_usd'] ?? '11.05') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-black text-indigo-600"
                            title="Approximate rate for USD ↔ ₵ lines on pricing pages (not live FX)">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight leading-relaxed">
                            Display-only conversion for landing &amp; upgrade pricing
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Gateway -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V12.944M12 2.944v.382m0 20.308c1.173 0 2.3-.232 3.344-.657M12 2.944a11.959 11.959 0 01-3.344.657" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Paystack Integration</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Connect your merchant account
                    </p>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Public Key (Live/Test)</label>
                        <input type="password" name="paystack_public_key"
                            value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono text-sm tracking-widest">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Secret Key (Live/Test)</label>
                        <input type="password" name="paystack_secret_key"
                            value="<?= htmlspecialchars($settings['paystack_secret_key'] ?? '') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono text-sm tracking-widest">
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Engine -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Email Engine (Brevo / SendGrid)</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Brevo preferred, SendGrid fallback</p>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Brevo API Key</label>
                        <input type="password" name="brevo_api_key"
                            value="<?= htmlspecialchars($settings['brevo_api_key'] ?? '') ?>"
                            placeholder="xkeysib-xxxxxxxxxx"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono text-sm tracking-widest">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">SendGrid API Key</label>
                        <input type="password" name="sendgrid_api_key"
                            value="<?= htmlspecialchars($settings['sendgrid_api_key'] ?? '') ?>"
                            placeholder="SG.xxxxxxxxxx"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono text-sm tracking-widest">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-black text-slate-700">Verified Sender Email</label>
                        <input type="email" name="smtp_from"
                            value="<?= htmlspecialchars($settings['smtp_from'] ?? 'noreply@suppliereval.com') ?>"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Must be verified in your Brevo or SendGrid sender settings</p>
                    </div>
                    <div class="pt-2">
                        <label class="text-sm font-black text-slate-700">Send Test Email</label>
                        <div class="mt-2 flex flex-col sm:flex-row gap-3">
                            <input type="email" name="test_email_to" placeholder="you@example.com"
                                class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                            <button type="submit"
                                formaction="/saas/admin/settings/test-email"
                                formmethod="POST"
                                class="px-5 py-3 bg-indigo-600 text-white rounded-xl font-black text-sm hover:bg-indigo-700 transition-colors">
                                Send Test
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluation scoring (Java service) -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Evaluation scoring service</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Spring Boot weighted engine (optional)</p>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-700">Service base URL</label>
                    <input type="text" name="evaluation_service_url"
                        value="<?= htmlspecialchars($settings['evaluation_service_url'] ?? '') ?>"
                        placeholder="http://127.0.0.1:8080 or http://evaluation:8080 (Docker)"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono text-sm">
                    <p class="text-xs text-slate-500">Leave empty to score in PHP only. When set, submissions call <code
                            class="bg-slate-100 px-1 rounded">POST /api/v1/evaluations/score</code> on this host.</p>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-700">Fallback if service is down</label>
                    <select name="evaluation_service_fallback"
                        class="w-full max-w-xs px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-bold">
                        <option value="0" <?= ($settings['evaluation_service_fallback'] ?? '0') === '0' ? 'selected' : '' ?>>Require Java service (show error)</option>
                        <option value="1" <?= ($settings['evaluation_service_fallback'] ?? '0') === '1' ? 'selected' : '' ?>>Use PHP calculator if unreachable</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="sticky bottom-8 z-30 flex justify-end">
            <button type="submit"
                class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-lg hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 flex items-center gap-3 active:scale-95">
                Apply Changes
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = "Platform Engine";
require __DIR__ . '/../../dashboard_layout.php';
?>