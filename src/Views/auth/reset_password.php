<?php ob_start(); ?>

<div class="min-h-screen relative overflow-hidden bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-brand-200/30 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-200/30 blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center mb-8">
            <a href="/saas" class="inline-flex items-center gap-2 group transition-transform hover:scale-105">
                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-slate-900 tracking-tight">SupplierEval</span>
            </a>
        </div>

        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-slate-900">Set New Password</h2>
            <p class="mt-2 text-sm text-slate-600">Please choose a strong password for your account.</p>
        </div>
    </div>

    <div class="mt-8 relative z-10 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white/80 backdrop-blur-xl py-8 px-4 shadow-2xl border border-white sm:rounded-3xl sm:px-10">
            <?php if (isset($error)): ?>
                <div class="rounded-2xl bg-red-50 p-4 mb-6 border border-red-100 italic">
                    <p class="text-sm font-medium text-red-800">
                        <?= $error ?>
                    </p>
                </div>
            <?php endif; ?>

            <form action="/saas/reset-password" method="POST" class="space-y-6">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">New Password</label>
                    <div class="mt-1 relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required placeholder="••••••••"
                            class="appearance-none block w-full pl-10 pr-3 py-4 border border-slate-200 rounded-2xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-semibold text-slate-700">Confirm New
                        Password</label>
                    <div class="mt-1 relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="confirm_password" name="confirm_password" type="password" required
                            placeholder="••••••••"
                            class="appearance-none block w-full pl-10 pr-3 py-4 border border-slate-200 rounded-2xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-brand-500/25 text-sm font-black uppercase tracking-widest text-white bg-slate-900 hover:bg-slate-800 transition-all transform active:scale-[0.98]">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Reset Password";
require __DIR__ . '/../layout.php';
?>