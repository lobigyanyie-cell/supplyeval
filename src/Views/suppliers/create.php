<?php ob_start(); ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Add New Supplier</h1>
        <p class="text-slate-500 mt-1">Enter the details of the supplier you wish to evaluate.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="/saas/suppliers/store" method="POST" class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2 md:col-span-1">
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Supplier Name <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" required
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                        placeholder="e.g. Acme Corp">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="contact_person" class="block text-sm font-semibold text-slate-700 mb-1">Contact
                        Person</label>
                    <input type="text" name="contact_person" id="contact_person"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                        placeholder="e.g. John Doe">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                        placeholder="john@example.com">
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" id="phone"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                        placeholder="+1 (555) 000-0000">
                </div>

                <div class="col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                        placeholder="123 Business Rd, City, Country"></textarea>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label for="reevaluation_days" class="block text-sm font-semibold text-slate-700 mb-1">Reevaluation
                        Cycle (Days)</label>
                    <input type="number" name="reevaluation_days" id="reevaluation_days" value="90" min="1"
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-slate-900"
                        placeholder="e.g. 90">
                    <p class="text-xs text-slate-500 mt-1">Number of days between expected evaluations.</p>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="/saas/suppliers"
                    class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5">
                    Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Add Supplier";
require __DIR__ . '/../dashboard_layout.php';
?>