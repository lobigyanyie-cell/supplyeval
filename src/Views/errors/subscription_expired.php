<?php
$title = "Subscription Expired";
ob_start();
?>
<div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-amber-500 p-6 flex justify-center">
            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
        </div>
        <div class="p-8 text-center">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Read-Only Mode</h2>
            <p class="text-slate-600 mb-6">
                Your subscription has expired. You can still view your data, but you cannot create or edit records until
                you renew your plan.
            </p>
            <div class="space-y-3">
                <a href="/saas/subscription/upgrade"
                    class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                    Upgrade / Renew Now
                </a>
                <a href="/saas/dashboard"
                    class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-lg transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
// Minimal layout
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <?= $content ?>
</body>

</html>