<?php ob_start(); ?>

<div class="min-h-screen relative overflow-hidden bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Background Decor to match landing page & login -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-brand-200/30 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-200/30 blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo/Home Link -->
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
            <h2 class="text-3xl font-extrabold text-slate-900">
                Join our platform
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Already have an account?
                <a href="/saas/login" class="font-semibold text-brand-600 hover:text-brand-500 transition-colors">
                    Sign in here
                </a>
            </p>
        </div>
    </div>

    <div class="mt-8 relative z-10 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white/80 backdrop-blur-xl py-8 px-4 shadow-2xl border border-white sm:rounded-3xl sm:px-10">
            <?php if (isset($error)): ?>
                <div class="rounded-2xl bg-red-50 p-4 mb-6 border border-red-100 animate-fade-in-up">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                <?= $error ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="/saas/register" method="POST" class="space-y-4">
                <div>
                    <label for="company_name" class="block text-sm font-semibold text-slate-700">
                        Company Name
                    </label>
                    <div class="mt-1 relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input id="company_name" name="company_name" type="text" required placeholder="Acme Corp"
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        Full Name
                    </label>
                    <div class="mt-1 relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="name" name="name" type="text" required placeholder="John Doe"
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Email Address
                    </label>
                    <div class="mt-1 relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            placeholder="name@company.com"
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Create Password
                    </label>
                    <div class="mt-1 relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required placeholder="••••••••"
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all text-slate-900 sm:text-sm">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-brand-500/25 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all transform active:scale-[0.98]">
                        Start 3-Month Free Trial
                    </button>
                </div>

                <div class="text-xs text-center text-slate-500 mt-4 leading-relaxed">
                    By starting your trial, you agree to our
                    <a href="#" class="font-semibold text-slate-700 hover:text-brand-600">Terms of Service</a>
                    and
                    <a href="#" class="font-semibold text-slate-700 hover:text-brand-600">Privacy Policy</a>.
                </div>
            </form>
        </div>

        <p class="mt-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> SupplierEval. All rights reserved.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Register";
require __DIR__ . '/../layout.php';
?>