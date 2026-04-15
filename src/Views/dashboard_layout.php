<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= isset($title) ? $title . ' - ' : '' ?>SupplierEval SaaS
    </title>
    <link rel="icon" type="image/svg+xml" href="/saas/public/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased font-sans">

    <?php
    // Get current role and name
    $role = $_SESSION['role'] ?? 'viewer';
    $name = $_SESSION['name'] ?? 'User';
    $initial = strtoupper(substr($name, 0, 1));
    ?>

    <div class="flex h-screen bg-slate-50">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl z-20">
            <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-900">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                        S
                    </div>
                    <span class="text-lg font-bold tracking-wide">
                        <?= $role === 'system_admin' ? 'SysAdmin' : 'SupplierEval' ?>
                    </span>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                <?php
                // Fetch alert count for badge
                $alert_count = 0;
                if (isset($_SESSION['company_id'])) {
                    $s_model = new \App\Models\Supplier();
                    $s_stmt = $s_model->getDueForEvaluation($_SESSION['company_id']);
                    $alert_count = $s_stmt->rowCount();
                }
                ?>
                <!-- Dashboard / Overview -->
                <a href="/saas/dashboard"
                    class="flex items-center justify-between px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= $_SERVER['REQUEST_URI'] === '/saas/dashboard' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        <span class="font-medium">Overview</span>
                    </div>
                    <?php if ($alert_count > 0): ?>
                        <span
                            class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-black text-white animate-pulse">
                            <?= $alert_count ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if ($role === 'system_admin'): ?>
                    <!-- System Admin Links -->
                    <a href="/saas/admin/companies"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/admin/companies') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span class="font-medium">Companies</span>
                    </a>
                    <a href="/saas/admin/users"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/admin/users') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Users</span>
                    </a>

                    <a href="/saas/admin/transactions"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/admin/transactions') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                            </path>
                        </svg>
                        <span class="font-medium">Transactions</span>
                    </a>

                    <a href="/saas/admin/audit-logs"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/admin/audit-logs') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span class="font-medium">Activity Logs</span>
                    </a>

                    <a href="/saas/admin/settings"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/admin/settings') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-medium">Settings</span>
                    </a>

                <?php elseif ($role === 'company_admin' || $role === 'evaluator'): ?>
                    <!-- Company Admin / Evaluator Links -->
                    <a href="/saas/suppliers"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/suppliers') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Suppliers</span>
                    </a>

                    <a href="/saas/criteria"
                        class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/criteria') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        <span class="font-medium">Criteria</span>
                    </a>

                    <?php if ($role === 'company_admin'): ?>
                        <a href="/saas/users"
                            class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/users') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            <span class="font-medium">Team</span>
                        </a>

                        <a href="/saas/subscription/history"
                            class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/subscription/history') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                            <span class="font-medium">Billing</span>
                        </a>

                        <a href="/saas/admin/audit-logs"
                            class="flex items-center gap-3 px-3 py-2.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all <?= str_contains($_SERVER['REQUEST_URI'], '/admin/audit-logs') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' : '' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            <span class="font-medium">Activity Logs</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="p-4 border-t border-slate-800">
                <form action="/saas/logout" method="POST">
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 text-slate-400 hover:text-white transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50 flex flex-col">
            <header
                class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                <h1 class="text-xl font-bold text-slate-800"><?= $title ?? 'Dashboard' ?></h1>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-600 hidden md:block">Welcome,
                        <?= htmlspecialchars($name) ?></span>
                    <div
                        class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold">
                        <?= $initial ?>
                    </div>
                </div>
            </header>

            <!-- Warning Banner Injection -->
            <?php
            $show_expired_banner = false;
            if (isset($_SESSION['company_id'])) {
                $db = new \App\Config\Database();
                $conn = $db->getConnection();
                $stmt = $conn->prepare("SELECT subscription_status, trial_ends_at, account_status FROM companies WHERE id = :id");
                $stmt->bindParam(":id", $_SESSION['company_id']);
                $stmt->execute();
                $company = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($company) {
                    $is_trial = $company['subscription_status'] === 'trial';
                    $trial_ended = strtotime($company['trial_ends_at']) < time();
                    $is_inactive = $company['subscription_status'] === 'inactive';

                    if (($is_trial && $trial_ended) || $is_inactive) {
                        $show_expired_banner = true;
                    }
                }
            }
            ?>

            <?php if ($show_expired_banner && !str_contains($_SERVER['REQUEST_URI'], 'upgrade')): ?>
                <div class="bg-amber-600 text-white px-4 py-3 shadow-md relative z-40">
                    <div class="max-w-7xl mx-auto flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <span class="font-medium">Your subscription has expired. You have read-only access.</span>
                        </div>
                        <a href="/saas/subscription/upgrade"
                            class="bg-white text-amber-700 px-4 py-1.5 rounded-full text-sm font-bold hover:bg-amber-50 transition-colors">
                            Renew Now
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="max-w-7xl mx-auto px-8 py-8 w-full">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
</body>

</html>