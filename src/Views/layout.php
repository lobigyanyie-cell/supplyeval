<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>SupplierEval SaaS</title>
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
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
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

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>

<?php
// Logic to check subscription status
$show_expired_banner = false;
if (isset($_SESSION['company_id'])) {
    $db = new \App\Config\Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT subscription_status, trial_ends_at FROM companies WHERE id = :id");
    $stmt->bindParam(":id", $_SESSION['company_id']);
    $stmt->execute();
    $company = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($company) {
        $is_trial = $company['subscription_status'] === 'trial';
        // Check if trial_ends_at is not null before using strtotime
        $trial_ends_at_timestamp = !empty($company['trial_ends_at']) ? strtotime($company['trial_ends_at']) : 0;
        $trial_ended = $trial_ends_at_timestamp < time();
        $is_inactive = $company['subscription_status'] === 'inactive';


        if (($is_trial && $trial_ended) || $is_inactive) {
            $show_expired_banner = true;
        }
    }
}
?>

<body
    class="text-slate-900 bg-white antialiased selection:bg-brand-500 selection:text-white flex flex-col min-h-screen">
    <nav class="fixed w-full z-50 transition-all duration-300 glass border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="/saas/" class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-600 to-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                            S
                        </div>
                        <span
                            class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-900 to-brand-600">
                            SupplierEval
                        </span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features"
                        class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Features</a>
                    <a href="#how-it-works"
                        class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">How it
                        works</a>
                    <a href="#pricing"
                        class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Pricing</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/saas/login"
                        class="text-sm font-medium text-slate-600 hover:text-brand-600 transition-colors">Log in</a>
                    <a href="/saas/register"
                        class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-full text-sm font-semibold transition-all hover:shadow-lg transform hover:-translate-y-0.5">
                        Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="fixed top-20 left-0 w-full z-40">
        <?php if ($show_expired_banner && !str_contains($_SERVER['REQUEST_URI'], 'upgrade')): ?>
            <div class="bg-amber-600 text-white px-4 py-3 shadow-md">
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
    </div>

    <main
        class="flex-grow pt-<?= ($show_expired_banner && !str_contains($_SERVER['REQUEST_URI'], 'upgrade')) ? '36' : '20' ?>">
        <?= $content ?? '' ?>
    </main>

    <footer class="bg-slate-900 text-slate-300 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-12 mb-12">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-brand-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xl">
                            S
                        </div>
                        <span class="text-xl font-bold text-white">
                            SupplierEval
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-sm mb-6">
                        Empowering businesses to make data-driven decisions with advanced supplier evaluation and
                        performance tracking.
                    </p>
                    <!-- Social Media -->
                    <div class="flex items-center gap-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-800 hover:bg-brand-600 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Product -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Product</h3>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-sm hover:text-white transition-colors">Features</a></li>
                        <li><a href="#pricing" class="text-sm hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Enterprise</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Integrations</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">Security</a></li>
                        <li><a href="#" class="text-sm hover:text-white transition-colors">GDPR</a></li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="border-t border-slate-800 pt-8 pb-8">
                <div class="max-w-md">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-3">Stay Updated</h3>
                    <p class="text-sm text-slate-400 mb-4">Get the latest updates on product features and supplier
                        evaluation best practices.</p>
                    <form class="flex gap-2">
                        <input type="email" placeholder="Enter your email"
                            class="flex-1 px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        <button type="submit"
                            class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500">
                    &copy; <?= date('Y') ?> SupplierEval SaaS. All rights reserved.
                </p>
                <div class="flex items-center gap-6">
                    <a href="#" class="text-xs text-slate-500 hover:text-white transition-colors">Status</a>
                    <a href="#" class="text-xs text-slate-500 hover:text-white transition-colors">API</a>
                    <a href="#" class="text-xs text-slate-500 hover:text-white transition-colors">Documentation</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>