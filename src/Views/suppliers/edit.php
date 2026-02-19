<?php ob_start(); ?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Supplier Profile</h1>
            <p class="text-sm text-slate-600 mt-1">Update information for
                <?= htmlspecialchars($supplier['name']) ?>
            </p>
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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <form action="/saas/suppliers/update" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= $supplier['id'] ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier Name -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-sm font-black text-slate-700 uppercase tracking-wider">Supplier Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                </div>

                <!-- Contact Person -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-700 uppercase tracking-wider">Contact Person</label>
                    <input type="text" name="contact_person"
                        value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-700 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-700 uppercase tracking-wider">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                </div>

                <!-- Address -->
                <div class="md:col-span-2 space-y-2">
                    <label class="text-sm font-black text-slate-700 uppercase tracking-wider">Business Address</label>
                    <textarea name="address" rows="3"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea>
                </div>

                <!-- Reevaluation Cycle -->
                <div class="space-y-2">
                    <label class="text-sm font-black text-slate-700 uppercase tracking-wider">Reevaluation Cycle
                        (Days)</label>
                    <input type="number" name="reevaluation_days" value="<?= $supplier['reevaluation_days'] ?? 90 ?>"
                        min="1"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">Days between periodic reviews
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-slate-100">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-xl font-black uppercase tracking-widest text-sm shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5 active:translate-y-0">
                    Save Changes
                </button>
                <a href="/saas/suppliers"
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 px-8 py-4 rounded-xl font-black uppercase tracking-widest text-sm text-center transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Edit Supplier";
require __DIR__ . '/../dashboard_layout.php';
?>