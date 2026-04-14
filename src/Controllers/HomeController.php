<?php

namespace App\Controllers;

use App\Config\Settings;
use App\Helpers\PricingDisplay;

class HomeController extends Controller
{
    public function index()
    {
        $premiumGhs = (float) Settings::get('premium_price', '350');
        $this->view('landing', [
            'proPlanPricing' => PricingDisplay::formatMonthly($premiumGhs),
        ]);
    }
}
