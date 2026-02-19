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
            <h2 class="text-3xl font-extrabold text-slate-900">Reset Password</h2>
            <p class="mt-2 text-sm text-slate-600">Enter your email and we'll send you a recovery link.</p>
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

            <?php if (isset($success)): ?>
                <div class="rounded-2xl bg-emerald-50 p-6 mb-6 border border-emerald-100 text-center">
                    <div
                        class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-900">
                        <?= $success ?>
                    </p>
                    <a href="/saas/login"
                        class="mt-4 inline-block text-xs font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-700 underline">Back
                        to Login</a>
                </div>
            <?php else: ?>
                <form action="/saas/forgot-password" method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                        <div class="mt-1 relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" required placeholder="name@company.com"
                                class="appearance-none block w-full pl-10 pr-3 py-4 border border-slate-200 rounded-2xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-brand-500/25 text-sm font-black uppercase tracking-widest text-white bg-slate-900 hover:bg-slate-800 transition-all transform active:scale-[0.98]">
                        Send Reset Link
                    </button>

                    <div class="text-center">
                        <a href="/saas/login"
                            class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Back to
                            login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Forgot Password";
require __DIR__ . '/../layout.php';
?>