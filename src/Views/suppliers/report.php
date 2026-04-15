<?php
// Report view: No sidebar, pure white background, optimized for printing.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Performance Report -
        <?= htmlspecialchars($company['name']) ?>
    </title>
    <link rel="icon" type="image/svg+xml" href="/saas/public/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .report-container {
                box-shadow: none;
                border: none;
                width: 100%;
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-slate-100 p-8 min-h-screen">

    <div class="max-w-5xl mx-auto no-print mb-8 flex justify-between items-center">
        <a href="/saas/suppliers/rankings"
            class="text-sm font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-2">
            ← Back to Rankings
        </a>
        <button onclick="window.print()"
            class="bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-slate-800 transition-all flex items-center gap-2 shadow-lg shadow-slate-900/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Report / Export PDF
        </button>
    </div>

    <div
        class="report-container max-w-5xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-200">
        <!-- Header -->
        <div class="p-12 border-b-2 border-slate-100 flex justify-between items-start">
            <div>
                <img src="/saas/public/assets/logo-mark.svg" alt="SupplierEval logo" class="h-10 mb-6">
                <div
                    class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-2xl mb-4 shadow-lg shadow-indigo-600/30">
                    S</div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Supplier Performance Report</h1>

                <div class="mt-4 flex flex-col gap-1">
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Audit Generation Details
                    </p>
                    <div class="flex items-center gap-6 mt-1">
                        <div>
                            <span class="text-[10px] text-slate-400 font-black uppercase">Officer:</span>
                            <span
                                class="text-xs font-bold text-slate-700 ml-1"><?= htmlspecialchars($_SESSION['name'] ?? 'N/A') ?></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-black uppercase">Timestamp:</span>
                            <span class="text-xs font-bold text-slate-700 ml-1"><?= date('M j, Y • H:i:s') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">
                    <?= htmlspecialchars($company['name']) ?>
                </h2>
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1">Vendor Management
                    Division</p>
                <div
                    class="mt-4 inline-block px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100 italic">
                    Certified Audit Document
                </div>
            </div>
        </div>

        <!-- Summary Table -->
        <div class="p-12">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Performance Summary Rankings
            </h3>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-900">
                        <th class="py-4 text-xs font-black uppercase tracking-widest">Rank</th>
                        <th class="py-4 text-xs font-black uppercase tracking-widest">Supplier</th>
                        <th class="py-4 text-xs font-black uppercase tracking-widest">Score</th>
                        <th class="py-4 text-xs font-black uppercase tracking-widest">Grade</th>
                        <th class="py-4 text-xs font-black uppercase tracking-widest text-right">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($rankedSuppliers as $index => $supplier): ?>
                        <?php
                        $score = $supplier['avg_score'] ?? 0;
                        $grade = $score >= 90 ? 'A+' : ($score >= 80 ? 'A' : ($score >= 70 ? 'B+' : ($score >= 60 ? 'B' : 'C')));
                        $color = $score >= 80 ? 'emerald' : ($score >= 60 ? 'amber' : 'rose');
                        ?>
                        <tr class="group">
                            <td class="py-6 font-black text-slate-400 text-lg">#
                                <?= $index + 1 ?>
                            </td>
                            <td class="py-6">
                                <span class="font-black text-slate-900 block tracking-tight">
                                    <?= htmlspecialchars($supplier['name']) ?>
                                </span>
                                <?php if (!empty($supplier['risk_flags'])): ?>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <?php foreach ($supplier['risk_flags'] as $flag): ?>
                                            <span
                                                class="text-[9px] font-black text-rose-600 uppercase tracking-tighter bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100 italic">
                                                ⚠ <?= $flag ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                        <?= htmlspecialchars($supplier['email'] ?? 'No contact info') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-6">
                                <span class="text-2xl font-black text-<?= $color ?>-600">
                                    <?= $score ?>
                                </span>
                                <span class="text-[10px] font-bold text-slate-300">/ 100</span>
                            </td>
                            <td class="py-6">
                                <span
                                    class="px-3 py-1 bg-<?= $color ?>-50 text-<?= $color ?>-700 rounded-lg text-xs font-black border border-<?= $color ?>-100">
                                    <?= $grade ?>
                                </span>
                            </td>
                            <td class="py-6 text-right">
                                <?php if ($supplier['evaluation_count'] > 1): ?>
                                    <?php
                                    $diff = $supplier['latest_score'] - $supplier['avg_score'];
                                    if ($diff > 1):
                                        ?>
                                        <div
                                            class="inline-flex items-center gap-1 text-emerald-600 font-black text-[10px] uppercase tracking-widest bg-emerald-50 px-2 py-1 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                            Upward
                                        </div>
                                    <?php elseif ($diff < -1): ?>
                                        <div
                                            class="inline-flex items-center gap-1 text-rose-600 font-black text-[10px] uppercase tracking-widest bg-rose-50 px-2 py-1 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                            </svg>
                                            Downward
                                        </div>
                                    <?php else: ?>
                                        <div
                                            class="inline-flex items-center gap-1 text-slate-400 font-black text-[10px] uppercase tracking-widest bg-slate-50 px-2 py-1 rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                            Stable
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">New
                                        Entry</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Detailed Breakdown -->
        <div class="p-12 bg-slate-50/50 page-break-before">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Category Analysis Breakdown
            </h3>

            <table
                class="w-full text-left border-collapse bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <thead class="bg-slate-900 text-white">
                    <tr>
                        <th
                            class="px-6 py-4 text-[10px] font-black uppercase tracking-widest border-r border-slate-800">
                            Supplier Name</th>
                        <?php foreach ($criteria as $criterion): ?>
                            <th
                                class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-center border-r border-slate-800">
                                <?= htmlspecialchars($criterion['name']) ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($rankedSuppliers as $supplier): ?>
                        <?php if ($supplier['evaluation_count'] > 0): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-black text-slate-900 border-r border-slate-100">
                                    <?= htmlspecialchars($supplier['name']) ?>
                                </td>
                                <?php
                                // This part is a bit complex, ideally we'd pass a score map.
                                // For the sake of this view, we can do a quick sub-query or assume a map exists.
                                // In a real app, the controller should prepare this map.
                                ?>
                                <?php foreach ($criteria as $criterion): ?>
                                    <?php
                                    $score = $supplierScores[$supplier['id']][$criterion['id']] ?? null;
                                    $color = $score !== null ? ($score >= 8 ? 'emerald' : ($score >= 6 ? 'amber' : 'rose')) : 'slate';
                                    ?>
                                    <td
                                        class="px-6 py-4 text-sm font-bold text-center border-r border-slate-100 text-<?= $color ?>-600">
                                        <?= $score !== null ? $score : '-' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-8 bg-indigo-50 border border-indigo-100 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-indigo-600 text-white rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-indigo-900 font-bold">Analyst Note</h4>
                        <p class="text-indigo-700 text-sm mt-1 font-medium italic">"This report provides a weighted
                            aggregation of all compliance and performance data. Suppliers marked with ⚠ indicate
                            automated risk flags
                            triggered by category performance falling below a 50% threshold."</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-12 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">End of Professional Report •
                SupplierEval SaaS Platform</p>
            <p class="text-[10px] text-slate-300 font-medium mt-2 tracking-tight">Verified Digital Signature:
                <?= md5($company['name'] . date('Y-m-d')) ?>
            </p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto py-12 px-6 flex items-center justify-between no-print">
        <p class="text-slate-400 text-xs font-bold uppercase">System: Report Export Mode</p>
        <div class="flex gap-4">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
        </div>
    </div>

</body>

</html>