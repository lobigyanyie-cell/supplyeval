<?php

namespace App\Controllers;

use App\Config\Settings;
use App\Helpers\PricingDisplay;

class HomeController extends Controller
{
    public function index()
    {
        $currency = (string) Settings::get('currency', 'USD');
        $price = (float) Settings::get('premium_price', '79.99');
        $this->view('landing', [
            'proPlanPricing' => PricingDisplay::formatMonthly($price, $currency),
        ]);
    }
}
