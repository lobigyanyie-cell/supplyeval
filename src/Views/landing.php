<?php ob_start();
/** @var array $proPlanPricing from HomeController */
$proPlanPricing = $proPlanPricing ?? \App\Helpers\PricingDisplay::formatMonthly(\App\Config\Settings::get('premium_price', '350'));
?>

<!-- Hero Section -->
<div class="relative overflow-hidden bg-white">
    <!-- Background Decor -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-brand-200/30 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-200/30 blur-[100px]">
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24 lg:pt-32 lg:pb-40">
        <div class="text-center max-w-4xl mx-auto">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-sm font-medium mb-8 animate-fade-in-up">
                <span class="flex h-2 w-2 rounded-full bg-brand-500"></span>
                v2.0 is now live: Enhanced Reporting
            </div>
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-slate-900 mb-8 animate-fade-in-up"
                style="animation-delay: 0.1s;">
                Master Your Supply Chain with
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-indigo-600">Intelligent
                    Data</span>
            </h1>
            <p class="mt-6 text-xl text-slate-600 max-w-2xl mx-auto mb-10 animate-fade-in-up"
                style="animation-delay: 0.2s;">
                Stop relying on guesswork. Evaluate, rank, and monitor your suppliers with a professional decision
                support system designed for modern enterprises.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up"
                style="animation-delay: 0.3s;">
                <a href="/saas/register?plan=starter"
                    class="inline-flex justify-center items-center px-8 py-4 text-base font-semibold text-white bg-slate-900 rounded-full hover:bg-slate-800 transition-all hover:shadow-lg transform hover:-translate-y-1">
                    Start 3-Month Free Trial
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <button onclick="openVideoModal()"
                    class="inline-flex justify-center items-center px-8 py-4 text-base font-semibold text-slate-700 bg-white border border-slate-200 rounded-full hover:bg-slate-50 transition-all hover:border-slate-300">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                    </svg>
                    Watch Demo
                </button>
            </div>
        </div>

        <!-- Dashboard Preview -->
        <div class="mt-20 relative animate-fade-in-up" style="animation-delay: 0.5s;">
            <div
                class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent z-20 h-full w-full pointer-events-none">
            </div>
            <div class="relative rounded-2xl bg-slate-900 p-2 shadow-2xl ring-1 ring-slate-900/10 mb-[-10%]">
                <div class="bg-slate-800 rounded-xl overflow-hidden shadow-2xl border border-slate-700/50">
                    <!-- Fake standard window controls -->
                    <div class="flex items-center gap-2 px-4 py-3 bg-slate-800/50 border-b border-slate-700">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                        </div>
                        <div
                            class="ml-4 px-3 py-1 bg-slate-900 rounded-md text-slate-400 text-[10px] font-mono tracking-wider flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    stroke-width="2" />
                            </svg>
                            dashboard.suppliereval.com
                        </div>
                    </div>
                    <!-- Mock UI -->
                    <div class="grid grid-cols-12 gap-0 min-h-[500px] bg-slate-900 text-slate-300">
                        <!-- Sidebar -->
                        <div class="col-span-2 border-r border-slate-800 p-4 space-y-2 hidden md:block">
                            <div class="flex items-center gap-2 mb-8 px-2">
                                <div
                                    class="w-6 h-6 rounded bg-brand-500 flex items-center justify-center text-white font-bold text-xs">
                                    S</div>
                                <div class="h-3 w-16 bg-slate-700 rounded-full"></div>
                            </div>
                            <div class="bg-slate-800 rounded-lg p-2 flex items-center gap-2">
                                <div
                                    class="w-4 h-4 rounded bg-brand-500/20 text-brand-400 flex items-center justify-center">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                </div>
                                <div class="h-2 w-12 bg-slate-600 rounded-full"></div>
                            </div>
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div class="p-2 flex items-center gap-2 opacity-50">
                                    <div class="w-4 h-4 rounded bg-slate-700"></div>
                                    <div class="h-2 w-16 bg-slate-700 rounded-full"></div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <!-- Main -->
                        <div class="col-span-12 md:col-span-10 p-6 md:p-8">
                            <div class="flex justify-between items-end mb-8">
                                <div>
                                    <div class="h-6 w-32 bg-slate-700 rounded-full mb-2"></div>
                                    <div class="h-3 w-48 bg-slate-800 rounded-full"></div>
                                </div>
                                <div class="h-9 w-24 bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20"></div>
                            </div>
                            <!-- Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-8">
                                <div class="bg-slate-800/50 p-5 rounded-2xl border border-slate-700/50">
                                    <div class="h-2 w-16 bg-slate-600 rounded-full mb-4"></div>
                                    <div class="flex items-end gap-2">
                                        <div class="h-6 w-12 bg-slate-200 rounded-lg"></div>
                                        <div class="h-3 w-8 bg-emerald-500/20 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="bg-slate-800/50 p-5 rounded-2xl border border-slate-700/50">
                                    <div class="h-2 w-16 bg-slate-600 rounded-full mb-4"></div>
                                    <div class="flex items-end gap-2 text-brand-400">
                                        <div class="h-6 w-16 bg-brand-500/80 rounded-lg"></div>
                                    </div>
                                </div>
                                <div class="bg-slate-800/50 p-5 rounded-2xl border border-slate-700/50">
                                    <div class="h-2 w-16 bg-slate-600 rounded-full mb-4"></div>
                                    <div class="h-6 w-10 bg-amber-500/80 rounded-lg"></div>
                                </div>
                            </div>
                            <!-- Rankings Table Simulation -->
                            <div class="bg-slate-800/40 rounded-2xl border border-slate-700/50 overflow-hidden">
                                <div
                                    class="h-10 bg-slate-800/80 border-b border-slate-700/50 flex items-center px-4 gap-4">
                                    <div class="h-2 w-4 bg-slate-600 rounded-full"></div>
                                    <div class="h-2 w-24 bg-slate-600 rounded-full"></div>
                                    <div class="h-2 w-16 bg-slate-600 rounded-full ml-auto"></div>
                                    <div class="h-2 w-12 bg-slate-600 rounded-full"></div>
                                </div>
                                <div class="p-4 space-y-4">
                                    <?php
                                    $rows = [
                                        ['name' => 'TechLogix Solutions', 'score' => 94, 'color' => 'bg-emerald-500'],
                                        ['name' => 'Global Freight Co.', 'score' => 88, 'color' => 'bg-emerald-500'],
                                        ['name' => 'Prime Mfg Group', 'score' => 76, 'color' => 'bg-amber-500'],
                                        ['name' => 'Apex Components', 'score' => 62, 'color' => 'bg-amber-500'],
                                    ];
                                    foreach ($rows as $row):
                                        ?>
                                        <div class="flex items-center gap-4 group">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center text-[10px] font-bold">
                                                <?= substr($row['name'], 0, 1) ?></div>
                                            <div class="text-sm font-medium text-slate-400"><?= $row['name'] ?></div>
                                            <div class="ml-auto w-24 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full <?= $row['color'] ?> shadow-[0_0_10px_rgba(0,0,0,0.5)]"
                                                    style="width: <?= $row['score'] ?>%"></div>
                                            </div>
                                            <div class="w-8 text-right text-xs font-bold text-slate-300">
                                                <?= $row['score'] ?>%</div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Social Proof -->
<div class="py-12 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest mb-8">Trusted by forward-thinking
            procurement teams</p>
        <div class="flex justify-center gap-12 invert opacity-50 grayscale">
            <!-- Mock Logos -->
            <div class="h-8 font-bold text-2xl">ACME</div>
            <div class="h-8 font-bold text-2xl">Globex</div>
            <div class="h-8 font-bold text-2xl">Soylent</div>
            <div class="h-8 font-bold text-2xl">Umbrella</div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="py-24 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">Capabilities</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">
                Everything you need to evaluate with confidence
            </p>
            <p class="text-lg text-slate-600">
                Our platform replaces scattered spreadsheets with a centralized, intelligent hub for all your supplier
                data.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div
                class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110">
                </div>
                <div
                    class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center text-brand-600 mb-6 relative z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Weighted Scoring</h3>
                <p class="text-slate-600 leading-relaxed">
                    Customizable criteria allow you to assign specific weights to Quality, Price, and Delivery, ensuring
                    your unique priorities are met.
                </p>
            </div>

            <!-- Feature 2 -->
            <div
                class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110">
                </div>
                <div
                    class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 relative z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Smart Ranking</h3>
                <p class="text-slate-600 leading-relaxed">
                    Automatically rank suppliers based on historical performance data. Spot trends and deviations before
                    they impact your business.
                </p>
            </div>

            <!-- Feature 3 -->
            <div
                class="group bg-white rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -mr-16 -mt-16 transition-transform group-hover:scale-110">
                </div>
                <div
                    class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 relative z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Enterprise Security</h3>
                <p class="text-slate-600 leading-relaxed">
                    Your data is isolated and protected. With Role-Based Access Control, ensure the right team members
                    see only what they need.
                </p>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Comparison Section -->
<div class="py-24 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">Why SupplierEval?</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">Built for the future of procurement</p>
            <p class="text-lg text-slate-600">See how we stack up against the old way of doing things.</p>
        </div>

        <div class="max-w-4xl mx-auto overflow-hidden rounded-3xl border border-slate-200 shadow-xl relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 via-transparent to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <table class="w-full text-left border-collapse relative z-10">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="p-6 text-sm font-bold uppercase tracking-wider">Feature</th>
                        <th class="p-6 text-sm font-bold uppercase tracking-wider text-center bg-brand-600">SupplierEval</th>
                        <th class="p-6 text-sm font-bold uppercase tracking-wider text-center opacity-50">Spreadsheets</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 font-semibold text-slate-900 text-sm">Data Integrity</td>
                        <td class="p-6 text-center bg-brand-50/30">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        </td>
                        <td class="p-6 text-center text-slate-400 font-medium">Manual Errors</td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 font-semibold text-slate-900 text-sm">Real-time Ranking</td>
                        <td class="p-6 text-center bg-brand-50/30">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        </td>
                        <td class="p-6 text-center text-slate-400 font-medium">Hours of Calculation</td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 font-semibold text-slate-900 text-sm">Audit Trail</td>
                        <td class="p-6 text-center bg-brand-50/30">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        </td>
                        <td class="p-6 text-center text-slate-400 font-medium">Non-existent</td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-6 font-semibold text-slate-900 text-sm">Collaborative Scoring</td>
                        <td class="p-6 text-center bg-brand-50/30">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        </td>
                        <td class="p-6 text-center text-slate-400 font-medium">Version Mismatch</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div id="how-it-works" class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">Workflow</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">
                From chaos to clarity in three steps
            </p>
        </div>

        <div class="relative grid grid-cols-1 md:grid-cols-3 gap-12">
            <!-- Connecting Line (Desktop) -->
            <div
                class="hidden md:block absolute top-12 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-brand-200 via-indigo-200 to-emerald-200 z-0">
            </div>

            <!-- Step 1 -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div
                    class="w-24 h-24 bg-white border-4 border-brand-100 rounded-full flex items-center justify-center text-2xl font-bold text-brand-600 shadow-sm mb-6">
                    1
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Add Suppliers</h3>
                <p class="text-slate-600">
                    Import your supplier list or add them manually. Centralize contact details and contracts.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div
                    class="w-24 h-24 bg-white border-4 border-indigo-100 rounded-full flex items-center justify-center text-2xl font-bold text-indigo-600 shadow-sm mb-6">
                    2
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Define Criteria</h3>
                <p class="text-slate-600">
                    Set up your weighted scoring model. Decide what matters most: Quality, Speed, or Cost.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="relative z-10 flex flex-col items-center text-center">
                <div
                    class="w-24 h-24 bg-white border-4 border-emerald-100 rounded-full flex items-center justify-center text-2xl font-bold text-emerald-600 shadow-sm mb-6">
                    3
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Evaluate & Rank</h3>
                <p class="text-slate-600">
                    Input scores and let our engine calculate rankings. Export reports for your stakeholders.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Industry Solutions Section -->
<div class="py-24 bg-slate-50 relative overflow-hidden">
    <!-- Decor -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-brand-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl -ml-48 -mb-48"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">Verticals</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">Tailored for your industry</p>
            <p class="text-lg text-slate-600">Compliance and performance metrics built for your specific requirements.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Manufacturing -->
            <div class="group bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 hover:border-brand-300 transition-all hover:shadow-2xl hover:-translate-y-2">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Manufacturing</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">Track production quality, lead times, and ISO compliance across your global supply chain with specialized templates.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">Defect Rates</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">ISO:9001</span>
                </div>
            </div>

            <!-- Retail -->
            <div class="group bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 hover:border-brand-300 transition-all hover:shadow-2xl hover:-translate-y-2">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Retail & E-commerce</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">Monitor inventory reliability, seasonal demand fulfillment, and ethical sourcing standards for global vendors.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">SKU Fulfillment</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">Ethics</span>
                </div>
            </div>

            <!-- Healthcare -->
            <div class="group bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200 hover:border-brand-300 transition-all hover:shadow-2xl hover:-translate-y-2">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-width="2"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Healthcare</h3>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">Ensure strict adherence to clinical standards, sterile storage requirements, and prompt mission-critical delivery.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">Compliance</span>
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-500 uppercase">Cold Chain</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="py-24 bg-slate-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">Testimonials</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">
                Loved by procurement teams worldwide
            </p>
            <p class="text-lg text-slate-600">
                See how companies are transforming their supplier evaluation process with SupplierEval.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-1 mb-4">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                </div>
                <p class="text-slate-700 mb-6 leading-relaxed">
                    "SupplierEval transformed how we manage our 200+ suppliers. We reduced evaluation time by 60% and
                    made data-driven decisions that saved us $500K in the first year."
                </p>
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-lg">
                        SM
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Sarah Mitchell</p>
                        <p class="text-sm text-slate-500">VP of Procurement, TechCorp Industries</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-1 mb-4">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                </div>
                <p class="text-slate-700 mb-6 leading-relaxed">
                    "The weighted scoring system is brilliant. We can finally compare suppliers objectively across
                    quality, price, and delivery. Our team loves the intuitive interface."
                </p>
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-lg">
                        JC
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">James Chen</p>
                        <p class="text-sm text-slate-500">Supply Chain Director, GlobalMart</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-1 mb-4">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                </div>
                <p class="text-slate-700 mb-6 leading-relaxed">
                    "Switching from Excel to SupplierEval was a game-changer. The automated rankings and beautiful
                    reports impressed our executive team. Setup took less than an hour!"
                </p>
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                        ER
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Emily Rodriguez</p>
                        <p class="text-sm text-slate-500">Operations Manager, MedSupply Co</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div
            class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-8 bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
            <div class="text-center">
                <div class="text-4xl font-bold text-brand-600 mb-2">500+</div>
                <div class="text-sm text-slate-600 font-medium">Companies Trust Us</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-emerald-600 mb-2">50K+</div>
                <div class="text-sm text-slate-600 font-medium">Suppliers Evaluated</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-indigo-600 mb-2">60%</div>
                <div class="text-sm text-slate-600 font-medium">Time Saved</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-amber-600 mb-2">4.9/5</div>
                <div class="text-sm text-slate-600 font-medium">Customer Rating</div>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Section -->
<div id="pricing" class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">Pricing</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">
                Simple, transparent pricing
            </p>
            <p class="text-lg text-slate-600">
                Choose the plan that fits your business needs. All plans include a 3-month free trial.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Starter Plan -->
            <div
                class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 flex flex-col hover:border-brand-200 transition-colors">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Starter</h3>
                    <p class="text-slate-500 text-sm mt-1">For small teams just getting started.</p>
                </div>
                <div class="mb-6 space-y-2">
                    <div>
                        <span class="text-4xl font-bold text-slate-900">Free</span>
                        <span class="text-slate-500 text-lg font-medium">/trial</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Includes core supplier evaluation features for small teams.
                    </p>
                </div>
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">Up to 50 Suppliers</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">Basic Evaluation Criteria</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">Standard Reporting</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">1 Admin User</span>
                    </li>
                </ul>
                <a href="/saas/register?plan=starter"
                    class="w-full block text-center py-3 px-4 border border-brand-200 rounded-xl text-brand-700 font-semibold hover:bg-brand-50 transition-colors">
                    Start Trial
                </a>
            </div>

            <!-- Pro Plan -->
            <div
                class="bg-slate-900 rounded-3xl p-8 shadow-xl border border-slate-800 flex flex-col relative transform scale-105 z-10">
                <div
                    class="absolute top-0 right-0 bg-gradient-to-r from-brand-500 to-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-2xl uppercase tracking-wider">
                    Popular
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-white">Professional</h3>
                    <p class="text-slate-400 text-sm mt-1">For growing businesses needing more power.</p>
                </div>
                <div class="mb-6 space-y-2.5">
                    <div class="text-5xl font-black text-white tracking-tight leading-none">
                        <?= htmlspecialchars($proPlanPricing['usd_per_month']) ?>
                    </div>
                    <p class="text-sm text-slate-400"><?= htmlspecialchars($proPlanPricing['ghs_billed_line']) ?></p>
                </div>
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-brand-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-300 text-sm">Unlimited Suppliers</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-brand-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-300 text-sm">Advanced Weighted Scoring</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-brand-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-300 text-sm">Export to CSV/PDF</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-brand-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-300 text-sm">5 Team Members</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-brand-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-300 text-sm">Priority Support</span>
                    </li>
                </ul>
                <a href="/saas/register?plan=professional"
                    class="w-full block text-center py-3 px-4 bg-brand-600 rounded-xl text-white font-semibold hover:bg-brand-500 transition-colors shadow-lg shadow-brand-500/30">
                    Get Started
                </a>
            </div>

            <!-- Enterprise Plan -->
            <div
                class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 flex flex-col hover:border-brand-200 transition-colors">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Enterprise</h3>
                    <p class="text-slate-500 text-sm mt-1">For large organizations with custom needs.</p>
                </div>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-slate-900">Custom</span>
                </div>
                <ul class="space-y-4 mb-8 flex-1">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">Unlimited Everything</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">API Access</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">Dedicated Account Manager</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span class="text-slate-600 text-sm">SSO Integration</span>
                    </li>
                </ul>
                <?php
                $salesEmail = \App\Config\Settings::get('support_email', '') ?: 'sales@suppliereval.com';
                $salesSubject = rawurlencode('Enterprise plan inquiry');
                $salesBody = rawurlencode("Hi,\n\nI'm interested in the Enterprise plan for SupplierEval. Please contact me with pricing and onboarding details.\n\nCompany: \nTeam size: \nEstimated suppliers: \n\nThanks.");
                ?>
                <a href="mailto:<?= htmlspecialchars($salesEmail) ?>?subject=<?= $salesSubject ?>&body=<?= $salesBody ?>"
                    class="w-full block text-center py-3 px-4 border border-slate-200 rounded-xl text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                    Contact Sales
                </a>
            </div>
        </div>
        <p class="text-center text-xs text-slate-500 mt-10 max-w-2xl mx-auto leading-relaxed">
            <?= htmlspecialchars($proPlanPricing['disclaimer']) ?>
        </p>
    </div>
</div>

<!-- FAQ Section -->
<div id="faq" class="py-24 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-brand-600 font-semibold tracking-wide uppercase text-sm mb-3">FAQ</h2>
            <p class="text-4xl font-bold text-slate-900 mb-6">Frequently Asked Questions</p>
            <p class="text-lg text-slate-600">Everything you need to know about SupplierEval.</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-6">
            <div
                class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:bg-white hover:shadow-md transition-all">
                <h3 class="text-lg font-bold text-slate-900 mb-2">How long is the free trial?</h3>
                <p class="text-slate-600">We offer a generous 3-month free trial on all plans. No credit card is
                    required to start, and you can cancel at any time.</p>
            </div>
            <div
                class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:bg-white hover:shadow-md transition-all">
                <h3 class="text-lg font-bold text-slate-900 mb-2">Can I customize the evaluation criteria?</h3>
                <p class="text-slate-600">Yes! You can define your own criteria (e.g., Sustainability, Innovation) and
                    assign custom weights to match your company's priorities.</p>
            </div>
            <div
                class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:bg-white hover:shadow-md transition-all">
                <h3 class="text-lg font-bold text-slate-900 mb-2">Is my data secure?</h3>
                <p class="text-slate-600">Absolutely. We use enterprise-grade encryption and isolated multi-tenant
                    architecture to ensure your supplier data is private and secure.</p>
            </div>
            <div
                class="bg-slate-50 rounded-2xl p-6 border border-slate-200 hover:bg-white hover:shadow-md transition-all">
                <h3 class="text-lg font-bold text-slate-900 mb-2">How many team members can I add?</h3>
                <p class="text-slate-600">Our Starter plan includes 1 admin, while the Professional plan supports up to
                    5 team members. Custom limits are available for Enterprise customers.</p>
            </div>
        </div>
    </div>
</div>

<!-- Trust Badges Section -->
<div class="py-12 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div
            class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900">SOC 2 Compliant</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900">GDPR Protected</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd"
                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900">SSL Encrypted</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900">Top Rated 2024</span>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-24 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900 rounded-[2.5rem] p-12 md:p-24 relative overflow-hidden text-center">
            <!-- Decor -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-brand-500/20 blur-[80px]"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-500/20 blur-[80px]">
            </div>

            <div class="relative z-10 max-w-3xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-8">
                    Ready to transform your procurement?
                </h2>
                <p class="text-lg text-slate-300 mb-10">
                    Join over 500+ companies using SupplierEval to streamline their decision-making process. Start your
                    3-month free trial today.
                </p>
                <a href="/saas/register?plan=professional"
                    class="inline-flex justify-center items-center px-10 py-5 text-lg font-bold text-slate-900 bg-white rounded-full hover:bg-brand-50 transition-all hover:shadow-[0_0_40px_-10px_rgba(255,255,255,0.3)] transform hover:-translate-y-1">
                    Get Started for Free
                </a>
                <p class="mt-4 text-sm text-slate-400">No credit card required • Cancel anytime</p>
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div id="videoModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" onclick="closeVideoModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div
            class="bg-white rounded-[2rem] overflow-hidden shadow-2xl relative w-full max-w-4xl aspect-video translate-y-4 animate-fade-in-up">
            <button onclick="closeVideoModal()"
                class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 border border-white/20 flex items-center justify-center text-white transition-all shadow-lg backdrop-blur-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div id="videoContainer" class="w-full h-full bg-slate-900 flex items-center justify-center p-12">
                <div class="text-center">
                    <div
                        class="w-24 h-24 rounded-full bg-brand-600 flex items-center justify-center text-white mx-auto mb-6 shadow-xl shadow-brand-500/30">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" />
                        </svg>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-4">Product Demo</h3>
                    <p class="text-indigo-200 text-lg mb-8 max-w-md mx-auto">Discover how to modernize your procurement
                        workflow in just 2 minutes.</p>
                    <div
                        class="inline-flex items-center px-4 py-2 bg-slate-800 rounded-full text-slate-400 text-sm border border-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                        Demo stream ready
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openVideoModal() {
        document.getElementById('videoModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeVideoModal() {
        document.getElementById('videoModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeVideoModal();
    });
</script>

<?php
$content = ob_get_clean();
$title = "Smart Supplier Evaluation";
require __DIR__ . '/layout.php';
?>