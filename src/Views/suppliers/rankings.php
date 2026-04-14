<?php ob_start();
$plan_can_export = $plan_can_export ?? true;
$plan_notice = $plan_notice ?? null;
?>

<style>
    /* Fade-in animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }

        100% {
            background-position: 1000px 0;
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }

    .animate-slide-in {
        animation: slideInLeft 0.5s ease-out forwards;
    }

    .animate-scale-in {
        animation: scaleIn 0.4s ease-out forwards;
    }

    /* Staggered animations */
    .stagger-1 {
        animation-delay: 0.1s;
        opacity: 0;
    }

    .stagger-2 {
        animation-delay: 0.2s;
        opacity: 0;
    }

    .stagger-3 {
        animation-delay: 0.3s;
        opacity: 0;
    }

    .stagger-4 {
        animation-delay: 0.4s;
        opacity: 0;
    }

    .stagger-5 {
        animation-delay: 0.5s;
        opacity: 0;
    }

    .stagger-6 {
        animation-delay: 0.6s;
        opacity: 0;
    }

    /* Loading spinner */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner {
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        width: 16px;
        height: 16px;
        animation: spin 0.6s linear infinite;
    }

    /* Smooth transitions */
    .smooth-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Pulse animation for top supplier */
    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(99, 102, 241, 0);
        }
    }

    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }
</style>

<div class="space-y-6">
    <div class="flex items-center justify-between animate-fade-in">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Supplier Rankings</h1>
            <p class="text-sm text-slate-600 mt-1">Compare supplier performance based on evaluations</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <?php if ($plan_can_export): ?>
                <a href="/saas/suppliers/rankings/export" onclick="showExportLoading(event)"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md flex items-center gap-2 transform hover:scale-105">
                    <svg class="w-5 h-5 export-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span class="spinner hidden"></span>
                    <span class="export-text">Export CSV</span>
                </a>
                <a href="/saas/suppliers/rankings/report" target="_blank"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md flex items-center gap-2 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2a4 4 0 10-8 0v2l8-2zm12-2v2a3 3 0 01-3 3H7a3 3 0 01-3-3v-2m8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    <span>Print Report</span>
                </a>
            <?php else: ?>
                <span class="bg-slate-200 text-slate-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed"
                    title="Upgrade to export">Export CSV</span>
                <span class="bg-slate-200 text-slate-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed"
                    title="Upgrade for printable report">Print Report</span>
            <?php endif; ?>
            <a href="/saas/suppliers"
                class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                ← Back to Suppliers
            </a>
        </div>
    </div>

    <?php if ($plan_notice === 'export'): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 animate-fade-in">
            Export and printable reports are included on Professional and Enterprise.
            <a href="/saas/subscription/upgrade" class="font-semibold text-amber-950 underline ml-1">Upgrade your plan</a>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['alert_triggered'])): ?>
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-r-xl shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-indigo-900 font-bold">Alert Engine Triggered</h4>
                    <p class="text-indigo-700 text-sm">Critical low-score alert has been dispatched to the Company Administrator via email.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($rankedSuppliers)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                </path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-slate-900">No suppliers to rank</h3>
            <p class="mt-1 text-sm text-slate-500">Add suppliers and complete evaluations to see rankings.</p>
        </div>
    <?php else: ?>

        <!-- Performance Legend -->
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-slate-700">Performance Zones:</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-xs font-medium text-slate-600">Excellent (80-100)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <span class="text-xs font-medium text-slate-600">Good (60-79)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                        <span class="text-xs font-medium text-slate-600">Needs Improvement (< 60)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 3 Suppliers with Enhanced Design -->
        <?php
        $topSuppliers = array_slice(array_filter($rankedSuppliers, function ($s) {
            return $s['evaluation_count'] > 0;
        }), 0, 3);
        ?>

        <?php if (!empty($topSuppliers)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($topSuppliers as $index => $supplier): ?>
                    <?php
                    $medals = ['🥇', '🥈', '🥉'];
                    $colors = [
                        'from-yellow-50 to-amber-50 border-yellow-300 shadow-yellow-100',
                        'from-slate-50 to-gray-100 border-slate-300 shadow-slate-100',
                        'from-orange-50 to-amber-50 border-orange-300 shadow-orange-100'
                    ];
                    $score = $supplier['avg_score'];
                    $grade = $score >= 90 ? 'A+' : ($score >= 80 ? 'A' : ($score >= 70 ? 'B+' : ($score >= 60 ? 'B' : 'C')));
                    $zoneColor = $score >= 80 ? 'text-emerald-600' : ($score >= 60 ? 'text-amber-600' : 'text-rose-600');
                    $zoneBg = $score >= 80 ? 'bg-emerald-100' : ($score >= 60 ? 'bg-amber-100' : 'bg-rose-100');
                    ?>
                    <div
                        class="bg-gradient-to-br <?= $colors[$index] ?> rounded-2xl border-2 p-6 text-center shadow-lg relative overflow-hidden">
                        <?php if ($index === 0): ?>
                            <div class="absolute top-0 right-0 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                                RECOMMENDED
                            </div>
                        <?php endif; ?>
                        <div class="text-5xl mb-3"><?= $medals[$index] ?></div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">
                            <a href="/saas/suppliers/profile?id=<?= $supplier['id'] ?>" class="hover:text-indigo-600 transition-colors">
                                <?= htmlspecialchars($supplier['name']) ?>
                            </a>
                        </h3>
                        <p class="text-xs text-slate-600 mb-4"><?= htmlspecialchars($supplier['contact_person'] ?? 'No contact') ?>
                        </p>
                        <div class="flex items-center justify-center gap-3 mb-3">
                            <div class="text-center">
                                <div class="text-4xl font-bold <?= $zoneColor ?>"><?= $score ?></div>
                                <div class="text-xs text-slate-500 font-medium">out of 100</div>
                            </div>
                        </div>
                        <div class="inline-block px-4 py-2 <?= $zoneBg ?> rounded-full">
                            <span class="text-sm font-bold <?= $zoneColor ?>">Grade: <?= $grade ?></span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <div class="text-xs text-slate-600">
                                <span class="font-semibold"><?= $supplier['evaluation_count'] ?></span>
                                evaluation<?= $supplier['evaluation_count'] != 1 ? 's' : '' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Rankings Table with Enhanced Visuals -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Complete Rankings</h3>
                <p class="text-sm text-slate-600 mt-1">All suppliers ranked by performance</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Score</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Zone</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Evaluations</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php
                        $rank = 1;
                        foreach ($rankedSuppliers as $supplier):
                            $score = $supplier['avg_score'] ?? 0;
                            $grade = $score >= 90 ? 'A+' : ($score >= 80 ? 'A' : ($score >= 70 ? 'B+' : ($score >= 60 ? 'B' : 'C')));
                            $gradeColor = $score >= 80 ? 'bg-emerald-100 text-emerald-800' : ($score >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800');
                            $zoneColor = $score >= 80 ? 'text-emerald-600' : ($score >= 60 ? 'text-amber-600' : 'text-rose-600');
                            $zoneBg = $score >= 80 ? 'bg-emerald-50' : ($score >= 60 ? 'bg-amber-50' : 'bg-rose-50');
                            $zoneText = $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : 'Needs Work');
                            $barColor = $score >= 80 ? 'bg-emerald-500' : ($score >= 60 ? 'bg-amber-500' : 'bg-rose-500');
                            $isTop = $rank === 1 && $supplier['evaluation_count'] > 0;
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors <?= $isTop ? 'bg-indigo-50/30' : '' ?>">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-slate-900">#<?= $rank++ ?></span>
                                        <?php if ($isTop): ?>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">
                                                ⭐ TOP
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                            <?= strtoupper(substr($supplier['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                                                <a href="/saas/suppliers/profile?id=<?= $supplier['id'] ?>" class="hover:text-indigo-600 transition-colors">
                                                    <?= htmlspecialchars($supplier['name']) ?>
                                                </a>
                                                <?php if (!empty($supplier['risk_flags'])): ?>
                                                    <span class="inline-flex items-center text-rose-500" title="Critical Risk Identified">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($supplier['risk_flags'])): ?>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    <?php foreach ($supplier['risk_flags'] as $flag): ?>
                                                        <span class="px-2 py-0.5 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-tight rounded border border-rose-100 flex items-center gap-1">
                                                            ⚠ <?= $flag ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php elseif ($supplier['contact_person']): ?>
                                                <div class="text-xs text-slate-500">
                                                    <?= htmlspecialchars($supplier['contact_person']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($supplier['evaluation_count'] > 0): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl font-bold <?= $zoneColor ?>"><?= $score ?></span>
                                            <span class="text-xs text-slate-400">/100</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-slate-400">Not evaluated</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($supplier['evaluation_count'] > 0): ?>
                                        <span class="px-3 py-1 text-sm font-bold rounded-full <?= $gradeColor ?>">
                                            <?= $grade ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($supplier['evaluation_count'] > 0): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full <?= $zoneBg ?> <?= $zoneColor ?> text-xs font-semibold">
                                            <span
                                                class="w-2 h-2 rounded-full <?= str_replace('text-', 'bg-', $zoneColor) ?>"></span>
                                            <?= $zoneText ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-700"><?= $supplier['evaluation_count'] ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($supplier['evaluation_count'] > 0): ?>
                                        <div class="flex items-center gap-4">
                                            <div class="flex-1 w-24 bg-slate-200 rounded-full h-3 overflow-hidden">
                                                <div class="<?= $barColor ?> h-3 rounded-full transition-all duration-500"
                                                    style="width: <?= $score ?>%"></div>
                                            </div>
                                            
                                            <!-- Trend Indicator -->
                                            <div class="flex flex-col items-center min-w-[70px]">
                                                <?php if ($supplier['evaluation_count'] > 1): ?>
                                                    <?php 
                                                        $latest = $supplier['latest_score'] ?? $supplier['avg_score'];
                                                        $diff = $latest - $supplier['avg_score'];
                                                        if ($diff > 1): // Significant Improvement
                                                    ?>
                                                        <div class="flex items-center gap-1 text-emerald-600 font-black text-[10px] uppercase tracking-tighter">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                            Improving
                                                        </div>
                                                    <?php elseif ($diff < -1): // Significant Decline ?>
                                                        <div class="flex items-center gap-1 text-rose-600 font-black text-[10px] uppercase tracking-tighter">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                                                            Declining
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="flex items-center gap-1 text-slate-400 font-black text-[10px] uppercase tracking-tighter">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                            Stable
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">Baslining</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Interactive Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Bar Chart: Score Comparison -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-lg font-bold text-slate-800">Score Comparison</h3>
                    <p class="text-sm text-slate-600 mt-1">Visual comparison of all suppliers</p>
                </div>
                <div class="p-6">
                    <canvas id="scoreComparisonChart" height="300"></canvas>
                </div>
            </div>

            <!-- Radar Chart: Multi-Criteria Analysis -->
            <?php
            $evaluatedSuppliers = array_filter($rankedSuppliers, function ($s) {
                return $s['evaluation_count'] > 0;
            });
            $topForRadar = array_slice($evaluatedSuppliers, 0, 3);
            ?>
            <?php if (!empty($topForRadar) && !empty($criteria)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <h3 class="text-lg font-bold text-slate-800">Multi-Criteria Analysis</h3>
                        <p class="text-sm text-slate-600 mt-1">Top 3 suppliers across all criteria</p>
                    </div>
                    <div class="p-6">
                        <canvas id="radarChart" height="300"></canvas>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Criteria Breakdown with Enhanced Colors -->
        <?php if (!empty($criteria) && !empty($supplierBreakdowns)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Performance by Criteria</h3>
                    <p class="text-sm text-slate-600 mt-1">Detailed breakdown showing strengths and weaknesses</p>
                </div>
                <div class="p-6 space-y-6">
                    <?php foreach ($rankedSuppliers as $supplier): ?>
                        <?php if ($supplier['evaluation_count'] > 0 && isset($supplierBreakdowns[$supplier['id']])): ?>
                            <div class="border-2 border-slate-200 rounded-xl p-5 hover:border-indigo-300 transition-colors">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-bold text-slate-900 text-lg"><?= htmlspecialchars($supplier['name']) ?></h4>
                                    <span class="text-sm text-slate-500">Overall: <span
                                            class="font-bold text-indigo-600"><?= $supplier['avg_score'] ?>/100</span></span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <?php foreach ($supplierBreakdowns[$supplier['id']] as $breakdown): ?>
                                        <?php
                                        $percentage = ($breakdown['avg_score'] / $breakdown['max_score']) * 100;
                                        $barColor = $percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 60 ? 'bg-amber-500' : 'bg-rose-500');
                                        $textColor = $percentage >= 80 ? 'text-emerald-700' : ($percentage >= 60 ? 'text-amber-700' : 'text-rose-700');
                                        $bgColor = $percentage >= 80 ? 'bg-emerald-50' : ($percentage >= 60 ? 'bg-amber-50' : 'bg-rose-50');
                                        ?>
                                        <div class="<?= $bgColor ?> rounded-lg p-3 border border-slate-200">
                                            <div class="flex justify-between items-start mb-2">
                                                <span
                                                    class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($breakdown['criterion_name']) ?></span>
                                                <span class="text-xs font-medium text-slate-500"><?= $breakdown['weight'] ?>%</span>
                                            </div>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="text-xl font-bold <?= $textColor ?>"><?= $breakdown['avg_score'] ?></span>
                                                <span class="text-xs text-slate-500">/ <?= $breakdown['max_score'] ?></span>
                                            </div>
                                            <div class="w-full bg-white rounded-full h-2.5 overflow-hidden shadow-inner">
                                                <div class="<?= $barColor ?> h-2.5 rounded-full transition-all duration-500"
                                                    style="width: <?= $percentage ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Prepare data for charts
    const suppliers = <?= json_encode(array_map(function ($s) {
        return [
            'name' => $s['name'],
            'score' => $s['avg_score'] ?? 0,
            'evaluated' => $s['evaluation_count'] > 0
        ];
    }, $rankedSuppliers)) ?>;

    const evaluatedSuppliers = suppliers.filter(s => s.evaluated);

    // Score Comparison Bar Chart
    if (evaluatedSuppliers.length > 0) {
        const ctx1 = document.getElementById('scoreComparisonChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: evaluatedSuppliers.map(s => s.name),
                    datasets: [{
                        label: 'Total Score',
                        data: evaluatedSuppliers.map(s => s.score),
                        backgroundColor: evaluatedSuppliers.map(s => {
                            if (s.score >= 80) return 'rgba(16, 185, 129, 0.8)'; // emerald
                            if (s.score >= 60) return 'rgba(245, 158, 11, 0.8)'; // amber
                            return 'rgba(239, 68, 68, 0.8)'; // rose
                        }),
                        borderColor: evaluatedSuppliers.map(s => {
                            if (s.score >= 80) return 'rgb(16, 185, 129)';
                            if (s.score >= 60) return 'rgb(245, 158, 11)';
                            return 'rgb(239, 68, 68)';
                        }),
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Score: ' + context.parsed.x + '/100';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function (value) {
                                    return value;
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }

    // Radar Chart for Multi-Criteria Analysis
    <?php if (!empty($topForRadar) && !empty($criteria)): ?>
        const criteriaNames = <?= json_encode(array_column($criteria, 'name')) ?>;
        const radarData = {
            labels: criteriaNames,
            datasets: [
                <?php foreach ($topForRadar as $index => $supplier): ?>
                                        <?php if (isset($supplierBreakdowns[$supplier['id']])): ?>
                                                        {
                            label: '<?= addslashes($supplier['name']) ?>',
                            data: [
                                <?php
                                $breakdown = $supplierBreakdowns[$supplier['id']];
                                foreach ($criteria as $criterion) {
                                    $score = 0;
                                    foreach ($breakdown as $b) {
                                        if ($b['criteria_id'] == $criterion['id']) {
                                            $score = ($b['avg_score'] / $b['max_score']) * 100;
                                            break;
                                        }
                                    }
                                    echo round($score, 1) . ',';
                                }
                                ?>
                            ],
                            backgroundColor: '<?= $index === 0 ? "rgba(99, 102, 241, 0.2)" : ($index === 1 ? "rgba(16, 185, 129, 0.2)" : "rgba(245, 158, 11, 0.2)") ?>',
                            borderColor: '<?= $index === 0 ? "rgb(99, 102, 241)" : ($index === 1 ? "rgb(16, 185, 129)" : "rgb(245, 158, 11)") ?>',
                            borderWidth: 2,
                            pointBackgroundColor: '<?= $index === 0 ? "rgb(99, 102, 241)" : ($index === 1 ? "rgb(16, 185, 129)" : "rgb(245, 158, 11)") ?>',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '<?= $index === 0 ? "rgb(99, 102, 241)" : ($index === 1 ? "rgb(16, 185, 129)" : "rgb(245, 158, 11)") ?>'
                        }<?= $index < count($topForRadar) - 1 ? ',' : '' ?>
                                        <?php endif; ?>
                        <?php endforeach; ?>
            ]
        };

        const ctx2 = document.getElementById('radarChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'radar',
                data: radarData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + context.parsed.r.toFixed(1) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20,
                                callback: function (value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            angleLines: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            pointLabels: {
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }
    <?php endif; ?>
    // Export loading state
    function showExportLoading(event) {
        const button = event.currentTarget;
        const icon = button.querySelector('.export-icon');
        const spinner = button.querySelector('.spinner');
        const text = button.querySelector('.export-text');

        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        text.textContent = 'Exporting...';
        button.classList.add('opacity-75', 'cursor-wait');
        button.style.pointerEvents = 'none';

        // Allow the download to proceed
        setTimeout(() => {
            icon.classList.remove('hidden');
            spinner.classList.add('hidden');
            text.textContent = 'Export Rankings';
            button.classList.remove('opacity-75', 'cursor-wait');
            button.style.pointerEvents = 'auto';
        }, 2000);
    }
</script>

<?php
$content = ob_get_clean();
$title = "Supplier Rankings";
require __DIR__ . '/../dashboard_layout.php';
?>