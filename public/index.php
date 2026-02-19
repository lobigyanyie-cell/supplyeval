<?php

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Start Session
session_start();

// Simple Router
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Normalize the request URI
// Handle both /saas/register and /register paths
if (strpos($requestUri, '/saas/public') === 0) {
    // Rewritten by root .htaccess: /saas/public/register
    $requestUri = substr($requestUri, strlen('/saas/public'));
} elseif (strpos($requestUri, '/saas') === 0) {
    // Direct access: /saas/register
    $requestUri = substr($requestUri, strlen('/saas'));
}
// else: already normalized (no prefix)

// Ensure we have a valid route
if ($requestUri === '' || $requestUri === '/') {
    $requestUri = '/';
}


// Routes
$routes = [
    'GET' => [
        '/' => 'HomeController@index',
        '/register' => 'AuthController@showRegister',
        '/login' => 'AuthController@showLogin',
        '/dashboard' => 'DashboardController@index',
        '/suppliers' => 'SupplierController@index',
        '/suppliers/create' => 'SupplierController@create',
        '/suppliers/edit' => 'SupplierController@edit',
        '/suppliers/import' => 'SupplierController@import',
        '/suppliers/import/template' => 'SupplierController@downloadTemplate',
        '/suppliers/rankings' => 'SupplierController@rankings',
        '/suppliers/rankings/report' => 'SupplierController@report',
        '/suppliers/rankings/export' => 'SupplierController@exportRankings',
        '/suppliers/profile' => 'SupplierController@profile',
        '/suppliers/export' => 'SupplierController@export',
        '/criteria' => 'CriteriaController@index',
        '/criteria/create' => 'CriteriaController@create',
        '/criteria/edit' => 'CriteriaController@edit',
        '/evaluations/create' => 'EvaluationController@create',
        '/subscription/upgrade' => 'SubscriptionController@showUpgrade',
        '/subscription/process' => 'SubscriptionController@process',
        '/admin/companies' => 'DashboardController@companies',
        '/admin/users' => 'DashboardController@users',
        '/admin/transactions' => 'DashboardController@globalTransactions',
        '/admin/settings' => 'DashboardController@showSettings',
        '/users' => 'UserController@index',
        '/users/create' => 'UserController@create',
        '/subscription/history' => 'SubscriptionController@history',
        '/subscription/invoice' => 'SubscriptionController@viewInvoice',
        '/admin/audit-logs' => 'DashboardController@auditLogs',
        '/forgot-password' => 'AuthController@showForgotPassword',
        '/reset-password' => 'AuthController@showResetPassword',
    ],
    'POST' => [
        '/register' => 'AuthController@register',
        '/login' => 'AuthController@login',
        '/logout' => 'AuthController@logout',
        '/suppliers/store' => 'SupplierController@store',
        '/suppliers/update' => 'SupplierController@update',
        '/suppliers/delete' => 'SupplierController@delete',
        '/suppliers/import/process' => 'SupplierController@processImport',
        '/criteria/store' => 'CriteriaController@store',
        '/criteria/update' => 'CriteriaController@update',
        '/criteria/delete' => 'CriteriaController@delete',
        '/evaluations/store' => 'EvaluationController@store',
        '/subscription/upgrade' => 'SubscriptionController@showUpgrade',
        '/subscription/process' => 'SubscriptionController@process',
        '/admin/companies/suspend' => 'DashboardController@suspendCompany',
        '/admin/companies/delete' => 'DashboardController@deleteCompany',
        '/admin/users/delete' => 'DashboardController@deleteUser',
        '/admin/settings/save' => 'DashboardController@saveSettings',
        '/users/create' => 'UserController@create',
        '/users/store' => 'UserController@store',
        '/users/delete' => 'UserController@delete',
        '/webhook/paystack' => 'WebhookController@handle',
        '/forgot-password' => 'AuthController@sendResetLink',
        '/reset-password' => 'AuthController@resetPassword',
    ]
];

if (array_key_exists($requestUri, $routes[$requestMethod])) {
    $parts = explode('@', $routes[$requestMethod][$requestUri]);
    $controllerName = "App\\Controllers\\" . $parts[0];
    $action = $parts[1];

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            http_response_code(500);
            echo "Action not found";
        }
    } else {
        http_response_code(500);
        echo "Controller not found: " . $controllerName;
    }
} else {
    // 404
    http_response_code(404);
    echo "404 Not Found";
}
