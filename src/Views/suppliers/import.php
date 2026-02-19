<?php ob_start(); ?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Bulk Import Suppliers</h1>
            <p class="text-sm text-slate-600 mt-1">Onboard multiple suppliers instantly via CSV</p>
        </div>
        <a href="/saas/suppliers" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">← Back to
            Suppliers</a>
    </div>

    <!-- Instructions Card -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6">
        <div class="flex gap-4">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-2">
                <h3 class="font-bold text-indigo-900">How to use Bulk Import</h3>
                <ul class="text-sm text-indigo-700 space-y-1 list-disc list-inside">
                    <li>Download the standardized <a href="/saas/suppliers/import/template"
                            class="font-bold underline">CSV Template</a></li>
                    <li>Fill in your supplier data (Name and Email are required)</li>
                    <li>Upload the saved CSV file below</li>
                    <li>The system will skip duplicates or invalid entries automatically</li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-bold">
                <?= $error ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-bold">
                <?= $success ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Upload Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <form action="/saas/suppliers/import/process" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="space-y-4">
                <label class="block text-sm font-black text-slate-700 uppercase tracking-wider text-center">Select CSV
                    File</label>

                <div class="relative group">
                    <input type="file" name="csv_file" accept=".csv" required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div
                        class="border-2 border-dashed border-slate-200 group-hover:border-indigo-400 rounded-2xl p-12 text-center transition-all bg-slate-50 group-hover:bg-indigo-50/30">
                        <svg class="w-12 h-12 text-slate-400 group-hover:text-indigo-500 mx-auto mb-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm font-bold text-slate-600 group-hover:text-indigo-900">Drag and drop your file
                            here or click to browse</p>
                        <p class="text-xs text-slate-400 mt-2">Only .csv files are supported</p>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded-xl font-black uppercase tracking-widest text-sm shadow-lg transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                </svg>
                Process Import
            </button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Bulk Import Suppliers";
require __DIR__ . '/../dashboard_layout.php';
?>