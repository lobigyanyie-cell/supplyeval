<?php ob_start(); ?>

<div class="p-6 space-y-8 animate-fade-in">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Activity & Audit Logs</h1>
            <p class="text-slate-500 mt-1">Detailed trail of all critical actions performed on the platform.</p>
        </div>
        <div class="flex items-center gap-3">
            <div
                class="px-4 py-2 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-bold text-slate-600 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Monitoring Active
            </div>
        </div>
    </div>

    <!-- Logs Table Card -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th
                            class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            Timestamp</th>
                        <th
                            class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            User</th>
                        <th
                            class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            Action</th>
                        <th
                            class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            Details</th>
                        <th
                            class="px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">
                            IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">No activity logs recorded
                                yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4 text-sm text-slate-500 font-medium font-mono">
                                    <?= date('M d, H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 border border-white shadow-sm">
                                            <?= substr($log['user_name'], 0, 1) ?>
                                        </div>
                                        <span class="text-sm font-bold text-slate-900">
                                            <?= htmlspecialchars($log['user_name']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $actionClass = 'bg-slate-100 text-slate-600';
                                    if (strpos($log['action'], 'Deleted') !== false)
                                        $actionClass = 'bg-red-50 text-red-600 border border-red-100';
                                    if (strpos($log['action'], 'Created') !== false)
                                        $actionClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                                    if (strpos($log['action'], 'Login Success') !== false)
                                        $actionClass = 'bg-blue-50 text-blue-600 border border-blue-100';
                                    ?>
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest <?= $actionClass ?>">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-600 max-w-xs truncate"
                                        title="<?= htmlspecialchars($log['details']) ?>">
                                        <?= htmlspecialchars($log['details']) ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-mono text-slate-400">
                                        <?= htmlspecialchars($log['ip_address']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Audit Logs";
require __DIR__ . '/../../dashboard_layout.php';
?>