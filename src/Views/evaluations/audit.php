<?php ob_start(); ?>

<div class="max-w-3xl mx-auto space-y-8">
    <nav class="flex text-sm font-medium text-slate-500" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="/saas/suppliers" class="hover:text-indigo-600">Suppliers</a></li>
            <li class="text-slate-400">/</li>
            <li><a href="/saas/suppliers/scorecard?id=<?= (int) $evaluation['supplier_id'] ?>" class="hover:text-indigo-600">Scorecard</a></li>
            <li class="text-slate-400">/</li>
            <li class="text-slate-900">Evaluation #<?= (int) $evaluation['id'] ?></li>
        </ol>
    </nav>

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Evaluation audit trail</h1>
        <p class="text-slate-600 mt-1">
            <?= htmlspecialchars($evaluation['supplier_name'] ?? 'Supplier') ?> ·
            Status: <span class="font-semibold"><?= htmlspecialchars($evaluation['status'] ?? '') ?></span>
            <?php if (isset($evaluation['total_score'])): ?>
                · Score: <span class="font-semibold"><?= number_format((float) $evaluation['total_score'], 1) ?></span>
            <?php endif; ?>
        </p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/80">
            <h2 class="text-sm font-black text-slate-500 uppercase tracking-widest">Workflow events</h2>
        </div>
        <ul class="divide-y divide-slate-100">
            <?php if (empty($events)): ?>
                <li class="px-6 py-10 text-center text-slate-500 text-sm">No workflow events recorded yet.</li>
            <?php else: ?>
                <?php foreach ($events as $ev): ?>
                    <?php
                    $meta = [];
                    if (!empty($ev['meta'])) {
                        $decoded = json_decode((string) $ev['meta'], true);
                        $meta = is_array($decoded) ? $decoded : [];
                    }
                    ?>
                    <li class="px-6 py-4 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                        <div>
                            <p class="font-bold text-slate-900"><?= htmlspecialchars(str_replace('_', ' ', (string) $ev['action'])) ?></p>
                            <p class="text-xs text-slate-500 mt-1">
                                <?= htmlspecialchars((string) ($ev['actor_name'] ?? 'Unknown user')) ?>
                                <?php if (!empty($meta)): ?>
                                    <span class="text-slate-400"> · <?= htmlspecialchars(json_encode($meta, JSON_UNESCAPED_UNICODE)) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <time class="text-xs font-medium text-slate-400 whitespace-nowrap" datetime="<?= htmlspecialchars((string) $ev['created_at']) ?>">
                            <?= htmlspecialchars((string) $ev['created_at']) ?>
                        </time>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

</div>

<?php
$content = ob_get_clean();
$title = 'Evaluation audit';
require __DIR__ . '/../dashboard_layout.php';
?>
