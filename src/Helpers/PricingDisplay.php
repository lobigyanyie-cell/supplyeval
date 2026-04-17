<?php

namespace App\Helpers;

use App\Config\Settings;

/**
 * Display-only pricing helpers.
 *
 * The app stores `premium_price` as an amount in the selected Settings currency (GHS/USD/NGN).
 * USD ↔ GHS equivalents use `ghs_per_usd` from Platform Settings (see admin).
 */
class PricingDisplay
{
    /** Fallback when setting is missing or invalid (1 USD = N GHS, display only). */
    private const GHS_PER_USD_DEFAULT = 11.05;

    private static function ghsPerUsd(): float
    {
        $raw = Settings::get('ghs_per_usd', (string) self::GHS_PER_USD_DEFAULT);
        $v = (float) $raw;

        return $v > 0 ? $v : self::GHS_PER_USD_DEFAULT;
    }

    /**
     * @param float|string $amount Value from settings (e.g. premium_price), in Settings currency
     * @param string $currency Currency code (GHS/USD/NGN)
     * @return array{
     *   ghs: float,
     *   usd: float,
     *   usd_per_month: string,
     *   ghs_billed_line: string,
     *   charge_notice: string,
     *   disclaimer: string
     * }
     */
    public static function formatMonthly(float|string $amount, string $currency = 'GHS'): array
    {
        $currency = strtoupper(trim($currency));
        $value = max(0.0, (float) $amount);

        $ghs = 0.0;
        $usd = 0.0;
        $chargeNotice = '';
        $disclaimer = '';
        $ghsLine = '';

        $rate = self::ghsPerUsd();

        if ($currency === 'USD') {
            $usd = $value;
            $ghs = $usd * $rate;
            $chargeNotice = 'You will be charged $' . number_format($usd, 2) . ' (USD)';
            $ghsLine = '≈ ₵' . number_format($ghs, 2) . ' equivalent (display only)';
            $disclaimer = '*Payments processed in USD.';
        } elseif ($currency === 'GHS') {
            $ghs = $value;
            $usd = $ghs > 0 ? $ghs / $rate : 0.0;
            $ghsDecimals = (floor($ghs) === $ghs) ? 0 : 2;
            $chargeNotice = 'You will be charged ₵' . number_format($ghs, $ghsDecimals) . ' (GHS)';
            $ghsLine = '₵' . number_format($ghs, $ghsDecimals) . ' billed in GHS';
            $disclaimer = '*Prices shown in USD. Payments processed in GHS.';
        } else {
            // For other currencies, show value as-is and no conversion line.
            $chargeNotice = 'You will be charged ' . $currency . ' ' . number_format($value, 2);
            $disclaimer = '*Payments processed in ' . $currency . '.';
        }

        return [
            'ghs' => $ghs,
            'usd' => $usd,
            'usd_per_month' => '$' . number_format($usd, 2) . '/mo',
            'ghs_billed_line' => $ghsLine,
            'charge_notice' => $chargeNotice,
            'disclaimer' => $disclaimer,
        ];
    }

    /**
     * Format a stored transaction amount for UI using the app's billing currency (Settings).
     * Amounts are assumed to be in major units (same as Paystack verify `data.amount` / 100).
     */
    public static function formatMoneyAmount(float|string $amount, string $currency = 'GHS'): string
    {
        $currency = strtoupper(trim($currency));
        $value = (float) $amount;

        if ($currency === 'USD') {
            return '$' . number_format($value, 2);
        }
        if ($currency === 'GHS') {
            $decimals = (floor($value) === $value) ? 0 : 2;

            return '₵' . number_format($value, $decimals);
        }
        if ($currency === 'NGN') {
            return '₦' . number_format($value, 2);
        }

        return $currency . ' ' . number_format($value, 2);
    }
}
